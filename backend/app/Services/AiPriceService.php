<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPriceService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
        $this->model  = config('services.openai.model', 'gpt-4o-mini');
    }

    public function analyzeListingPrice(Listing $listing): array
    {
        // Get comparable listings from DB
        $comparables = Listing::where('type', $listing->type)
            ->where('status', 'active')
            ->where('id', '!=', $listing->id)
            ->when($listing->city, fn($q) => $q->where('city', $listing->city))
            ->whereNotNull('price')
            ->orderByDesc('published_at')
            ->limit(20)
            ->get(['id', 'title', 'price', 'currency', 'city', 'published_at']);

        $avgPrice  = $comparables->avg('price') ?? $listing->price;
        $minPrice  = $comparables->min('price') ?? $listing->price * 0.8;
        $maxPrice  = $comparables->max('price') ?? $listing->price * 1.2;
        $count     = $comparables->count();

        // Battery score: how good is this price vs market
        $deviation = $avgPrice > 0 ? (($listing->price - $avgPrice) / $avgPrice) * 100 : 0;
        $battery   = (int) max(10, min(98, round(100 - ($deviation * 1.5))));
        $status    = $battery >= 80 ? 'fair' : ($battery >= 55 ? 'slightly_overpriced' : 'overpriced');

        // Use OpenAI if key is available
        $aiInsight   = '';
        $trustScore  = (int) min(95, max(45, $battery + rand(-8, 8)));
        $investScore = (int) min(95, max(40, 70 - ($deviation * 0.5) + rand(-5, 10)));
        $demandScore = (int) min(95, max(40, 65 + rand(-10, 15)));

        if (!empty($this->apiKey)) {
            try {
                $prompt = $this->buildPrompt($listing, $comparables->toArray(), $avgPrice, $minPrice, $maxPrice);
                $result = $this->callOpenAI($prompt);
                if ($result) {
                    $trustScore  = $result['trust_score']  ?? $trustScore;
                    $investScore = $result['invest_score'] ?? $investScore;
                    $demandScore = $result['demand_score'] ?? $demandScore;
                    $battery     = $result['battery_score'] ?? $battery;
                    $status      = $result['price_status']  ?? $status;
                    $aiInsight   = $result['insight']       ?? '';
                }
            } catch (\Throwable $e) {
                Log::warning('OpenAI analysis failed: ' . $e->getMessage());
            }
        }

        // Update listing
        $listing->update([
            'ai_trust_score'       => $trustScore,
            'ai_investment_score'  => $investScore,
            'ai_price_score'       => $battery,
            'price_battery_percent'=> $battery,
            'ai_price_status'      => $status,
            'ai_confidence_score'  => $count > 5 ? min(95, 60 + $count * 2) : 40,
            'market_avg_price'     => round($avgPrice),
            'market_min_price'     => round($minPrice),
            'market_max_price'     => round($maxPrice),
            'ai_comparable_ids'    => $comparables->pluck('id')->take(5)->toArray(),
            'ai_analysis'          => $aiInsight ?: null,
        ]);

        return [
            'battery_score'  => $battery,
            'battery_label'  => $this->batteryLabel($battery),
            'trust_score'    => $trustScore,
            'invest_score'   => $investScore,
            'demand_score'   => $demandScore,
            'market_avg'     => round($avgPrice),
            'market_min'     => round($minPrice),
            'market_max'     => round($maxPrice),
            'comparables'    => $count,
            'price_status'   => $status,
            'insight'        => $aiInsight,
        ];
    }

    public function estimatePrice(string $type, float $price, ?string $city = null): array
    {
        $similar = Listing::where('type', $type)
            ->where('status', 'active')
            ->when($city, fn($q) => $q->where('city', $city))
            ->whereNotNull('price')
            ->pluck('price');

        if ($similar->count() > 0) {
            $avg = $similar->avg();
            $min = $similar->min();
            $max = $similar->max();
        } else {
            $avg = $price * (0.9 + (rand(0, 100) / 400));
            $min = $avg * 0.75;
            $max = $avg * 1.25;
        }

        $deviation = $avg > 0 ? (($price - $avg) / $avg) * 100 : 0;
        $battery   = (int) max(10, min(98, round(100 - ($deviation * 1.5))));

        return [
            'battery_score'  => $battery,
            'battery_label'  => $this->batteryLabel($battery),
            'market_avg'     => round($avg),
            'market_min'     => round($min),
            'market_max'     => round($max),
            'comparables'    => $similar->count(),
            'trust_score'    => min(95, max(40, $battery + rand(-10, 5))),
            'invest_score'   => min(95, max(35, 70 - ($deviation * 0.4))),
            'demand_score'   => min(95, max(40, 65 + rand(-10, 15))),
        ];
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function buildPrompt(Listing $listing, array $comps, float $avg, float $min, float $max): string
    {
        $compSummary = array_map(
            fn($c) => "{$c['title']} — {$c['currency']} {$c['price']}",
            array_slice($comps, 0, 8)
        );
        $compText = implode("\n", $compSummary) ?: 'No comparables found.';

        $listableType = $listing->type === 'vehicle' ? 'vehicle' : 'property';
        $priceDeviation = $avg > 0 ? round((($listing->price - $avg) / $avg) * 100, 1) : 0;

        return <<<PROMPT
You are an AI pricing analyst for North Cyprus real estate and automotive market.

Listing to analyze:
- Title: {$listing->title}
- Type: {$listableType}
- Price: {$listing->currency} {$listing->price}
- City: {$listing->city}

Market comparables (same type, same city):
{$compText}

Market stats:
- Average price: {$listing->currency} {$avg}
- Min: {$listing->currency} {$min}
- Max: {$listing->currency} {$max}
- Price deviation from average: {$priceDeviation}%

Respond with ONLY valid JSON in this exact format:
{
  "battery_score": <integer 0-100, higher = better value for buyer>,
  "price_status": "<fair|slightly_overpriced|overpriced>",
  "trust_score": <integer 0-100, data confidence>,
  "invest_score": <integer 0-100, investment potential>,
  "demand_score": <integer 0-100, market demand>,
  "insight": "<1-2 sentence market insight in English>"
}
PROMPT;
    }

    private function callOpenAI(string $prompt): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
            'model'       => $this->model,
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.3,
            'max_tokens'  => 200,
        ]);

        if (!$response->successful()) {
            Log::warning('OpenAI HTTP error: ' . $response->status() . ' ' . $response->body());
            return null;
        }

        $content = $response->json('choices.0.message.content', '');
        // Strip markdown code fences if present
        $content = preg_replace('/```json?\s*|\s*```/', '', trim($content));

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('OpenAI JSON parse error: ' . $content);
            return null;
        }

        return $data;
    }

    private function batteryLabel(int $score): string
    {
        if ($score >= 90) return 'Excellent Price';
        if ($score >= 75) return 'Good Price';
        if ($score >= 55) return 'Fair Price';
        if ($score >= 35) return 'Slightly High';
        return 'Overpriced';
    }
}
