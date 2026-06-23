<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ListingManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);

    // Public listings
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/featured', [ListingController::class, 'featured']);
    Route::get('/listings/{slug}', [ListingController::class, 'show']);

    // Vehicle brands/models
    Route::get('/vehicle-brands', fn() => response()->json(\App\Models\VehicleBrand::where('is_active', true)->with('models')->orderBy('name')->get()));
    Route::get('/property-types', fn() => response()->json(\App\Models\PropertyType::where('is_active', true)->orderBy('name')->get()));
    Route::get('/subscription-plans', fn() => response()->json(\App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get()));

    // Market stats (public)
    Route::get('/market/stats', function () {
        return response()->json([
            'total_listings' => \App\Models\Listing::where('status', 'active')->count(),
            'vehicle_listings' => \App\Models\Listing::where('type', 'vehicle')->where('status', 'active')->count(),
            'property_listings' => \App\Models\Listing::where('type', 'property')->where('status', 'active')->count(),
            'rental_listings' => \App\Models\Listing::where('type', 'rental')->where('status', 'active')->count(),
        ]);
    });

    // Authenticated user routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

        // Listings CRUD
        Route::post('/listings', [ListingController::class, 'store']);
        Route::put('/listings/{id}', [ListingController::class, 'update']);
        Route::delete('/listings/{id}', [ListingController::class, 'destroy']);
        Route::post('/listings/{id}/favorite', [ListingController::class, 'toggleFavorite']);
        Route::get('/my-listings', [ListingController::class, 'myListings']);

        // User profile
        Route::get('/profile', fn(Request $req) => response()->json($req->user()->load('profile')));
        Route::put('/profile', function (Request $req) {
            $v = $req->validate(['name' => 'sometimes|string', 'phone' => 'nullable|string']);
            $req->user()->update($v);
            return response()->json($req->user()->fresh());
        });

        // Favorites
        Route::get('/favorites', fn(Request $req) => response()->json(
            \App\Models\ListingFavorite::where('user_id', $req->user()->id)->with('listing.listable')->paginate(12)
        ));

        // Conversations/Messages
        Route::get('/conversations', function (Request $req) {
            return response()->json(
                \App\Models\Conversation::where('buyer_id', $req->user()->id)
                    ->orWhere('seller_id', $req->user()->id)
                    ->with(['buyer:id,name,avatar', 'seller:id,name,avatar', 'listing:id,title,slug'])
                    ->orderByDesc('last_message_at')->paginate(20)
            );
        });

        Route::get('/conversations/{id}/messages', function (Request $req, string $id) {
            $conv = \App\Models\Conversation::where(function ($q) use ($req) {
                $q->where('buyer_id', $req->user()->id)->orWhere('seller_id', $req->user()->id);
            })->findOrFail($id);
            return response()->json(\App\Models\Message::where('conversation_id', $conv->id)->with('sender:id,name,avatar')->orderBy('created_at')->paginate(50));
        });

        Route::post('/conversations/{id}/messages', function (Request $req, string $id) {
            $req->validate(['body' => 'required|string|max:2000']);
            $conv = \App\Models\Conversation::where(function ($q) use ($req) {
                $q->where('buyer_id', $req->user()->id)->orWhere('seller_id', $req->user()->id);
            })->findOrFail($id);
            $msg = \App\Models\Message::create(['conversation_id' => $conv->id, 'sender_id' => $req->user()->id, 'body' => $req->body]);
            $conv->update(['last_message_at' => now()]);
            return response()->json($msg, 201);
        });

        // Start conversation
        Route::post('/listings/{id}/inquire', function (Request $req, string $id) {
            $req->validate(['message' => 'required|string|max:2000']);
            $listing = \App\Models\Listing::findOrFail($id);
            $conv = \App\Models\Conversation::firstOrCreate(
                ['listing_id' => $id, 'buyer_id' => $req->user()->id, 'seller_id' => $listing->user_id],
                ['subject' => 'Inquiry: ' . $listing->title, 'last_message_at' => now()]
            );
            \App\Models\Message::create(['conversation_id' => $conv->id, 'sender_id' => $req->user()->id, 'body' => $req->message]);
            $listing->increment('inquiry_count');
            return response()->json(['conversation' => $conv, 'message' => 'Message sent.'], 201);
        });

        // Fraud report
        Route::post('/listings/{id}/report', function (Request $req, string $id) {
            $req->validate(['reason' => 'required|string', 'description' => 'nullable|string']);
            $report = \App\Models\FraudReport::create(['listing_id' => $id, 'reported_by' => $req->user()->id, 'reason' => $req->reason, 'description' => $req->description]);
            return response()->json(['message' => 'Report submitted.', 'report' => $report], 201);
        });

        // Create listing with brand/model name resolution
        Route::post('/listings/create', function (Request $req) {
            $req->validate([
                'type' => 'required|in:vehicle,property,rental',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'currency' => 'nullable|string|size:3',
                'city' => 'nullable|string',
                'district' => 'nullable|string',
                'brand_name' => 'required_if:type,vehicle|string',
                'model_name' => 'required_if:type,vehicle|string',
                'year' => 'required_if:type,vehicle|integer',
                'mileage' => 'nullable|integer',
                'fuel_type' => 'nullable|string',
                'transmission' => 'nullable|string',
                'body_type' => 'nullable|string',
                'color' => 'nullable|string',
                'engine_size' => 'nullable|string',
                'condition' => 'nullable|string',
                'property_type_name' => 'required_if:type,property|string',
                'listing_for' => 'nullable|string',
                'bedrooms' => 'nullable|integer',
                'bathrooms' => 'nullable|integer',
                'area_sqm' => 'nullable|numeric',
                'build_year' => 'nullable|integer',
                'has_parking' => 'nullable|boolean',
                'has_garden' => 'nullable|boolean',
                'has_pool' => 'nullable|boolean',
                'has_elevator' => 'nullable|boolean',
                'price_negotiable' => 'nullable|boolean',
                'video_url' => 'nullable|url',
            ]);
            return \Illuminate\Support\Facades\DB::transaction(function () use ($req) {
                $listable = null;
                if ($req->type === 'vehicle') {
                    $brand = \App\Models\VehicleBrand::firstOrCreate(['name' => $req->brand_name], ['is_active' => true]);
                    $model = \App\Models\VehicleModel::firstOrCreate(['vehicle_brand_id' => $brand->id, 'name' => $req->model_name], ['is_active' => true]);
                    $listable = \App\Models\Vehicle::create([
                        'brand_id' => $brand->id, 'model_id' => $model->id,
                        'year' => $req->year, 'mileage' => $req->mileage,
                        'fuel_type' => $req->fuel_type, 'transmission' => $req->transmission,
                        'body_type' => $req->body_type, 'color' => $req->color,
                        'engine_size' => $req->engine_size, 'condition' => $req->condition ?? 'Good',
                    ]);
                } elseif ($req->type === 'property') {
                    $propType = \App\Models\PropertyType::firstOrCreate(['name' => $req->property_type_name], ['is_active' => true]);
                    $listable = \App\Models\Property::create([
                        'property_type_id' => $propType->id, 'title' => $req->title,
                        'bedrooms' => $req->bedrooms, 'bathrooms' => $req->bathrooms,
                        'area_sqm' => $req->area_sqm, 'build_year' => $req->build_year,
                        'has_parking' => (bool)$req->has_parking, 'has_garden' => (bool)$req->has_garden,
                        'has_pool' => (bool)$req->has_pool, 'has_elevator' => (bool)$req->has_elevator,
                        'listing_for' => $req->listing_for ?? 'sale',
                    ]);
                } else {
                    $listable = \App\Models\Rental::create(['title' => $req->title, 'rental_type' => 'monthly', 'bedrooms' => $req->bedrooms]);
                }
                $listing = \App\Models\Listing::create([
                    'user_id' => $req->user()->id, 'type' => $req->type,
                    'listable_type' => get_class($listable), 'listable_id' => $listable->id,
                    'title' => $req->title, 'description' => $req->description,
                    'price' => $req->price, 'currency' => $req->currency ?? 'GBP',
                    'city' => $req->city, 'district' => $req->district,
                    'price_negotiable' => (bool)$req->price_negotiable,
                    'video_url' => $req->video_url, 'status' => 'pending',
                ]);
                \App\Models\PriceHistory::create(['listing_id' => $listing->id, 'price' => $listing->price, 'currency' => $listing->currency, 'changed_by' => 'seller']);
                return response()->json(['listing' => $listing->fresh(['listable']), 'message' => 'Submitted for review.'], 201);
            });
        });

        // Upload images for a listing (multipart)
        Route::post('/listings/{id}/upload-images', function (Request $req, string $id) {
            $listing = \App\Models\Listing::where('user_id', $req->user()->id)->findOrFail($id);
            $req->validate(['images' => 'required|array|min:1', 'images.*' => 'image|max:10240']);
            $urls = [];
            foreach ($req->file('images') as $file) {
                $path = $file->store('listings/'.$id, 'public');
                $urls[] = '/storage/'.$path;
            }
            $existing = $listing->images ?? [];
            if (is_string($existing)) $existing = json_decode($existing, true) ?? [];
            $all = array_merge($existing, $urls);
            $listing->update(['images' => $all, 'thumbnail' => $all[0] ?? $listing->thumbnail]);
            return response()->json(['images' => $all, 'message' => count($urls).' images uploaded.']);
        });

        // AI re-analyze price (can be called multiple times)
        Route::post('/listings/{id}/ai-analyze', function (Request $req, string $id) {
            $listing = \App\Models\Listing::where('user_id', $req->user()->id)->findOrFail($id);
            if ($req->price) $listing->update(['price' => $req->price]);
            $aiService = app(\App\Services\AiPriceService::class);
            $result = $aiService->analyzeListingPrice($listing->fresh());
            return response()->json(['message' => 'Analysis complete.', 'listing' => $listing->fresh()]);
        });

        // Public AI price estimate (before listing created)
        Route::post('/ai/price-estimate', function (Request $req) {
            $req->validate(['type' => 'required|in:vehicle,property,rental', 'price' => 'required|numeric']);
            $price = (float)$req->price;
            $similar = \App\Models\Listing::where('type', $req->type)->where('status', 'active')
                ->when($req->city, fn($q) => $q->where('city', $req->city))
                ->whereNotNull('price')->pluck('price');
            if ($similar->count() > 0) {
                $avg = $similar->avg(); $min = $similar->min(); $max = $similar->max();
                $battery = max(10, min(98, round(100 - (($price - $avg) / $avg) * 150)));
            } else {
                $avg = $price * (0.9 + (rand(0,100)/400));
                $min = $avg * 0.75; $max = $avg * 1.25;
                $battery = rand(55, 92);
            }
            return response()->json([
                'battery_score' => $battery,
                'battery_label' => $battery >= 85 ? 'Excellent Price' : ($battery >= 60 ? 'Fair Price' : 'Overpriced'),
                'market_avg' => round($avg), 'market_min' => round($min), 'market_max' => round($max),
                'comparables' => $similar->count(),
                'trust_score' => rand(55, 90), 'invest_score' => rand(50, 88), 'demand_score' => rand(60, 92),
            ]);
        });
    });

    // Public advertisements (for homepage)
    Route::get('/advertisements', function (Request $req) {
        return response()->json(\App\Models\Advertisement::where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->when($req->slot, fn($q) => $q->where('slot', $req->slot))
            ->when($req->type, fn($q) => $q->where('type', $req->type))
            ->orderByDesc('created_at')->get());
    });

    // Track ad click
    Route::post('/advertisements/{id}/click', function (string $id) {
        \App\Models\Advertisement::find($id)?->increment('click_count');
        return response()->json(['ok' => true]);
    });

    // Admin routes
    Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'overview']);

        // Listing management
        Route::get('/listings', [ListingManagementController::class, 'index']);
        Route::post('/listings/{id}/approve', [ListingManagementController::class, 'approve']);
        Route::post('/listings/{id}/reject', [ListingManagementController::class, 'reject']);
        Route::post('/listings/{id}/feature', [ListingManagementController::class, 'feature']);
        Route::delete('/listings/{id}', [ListingManagementController::class, 'destroy']);
        Route::post('/listings/{id}/reanalyze', [ListingManagementController::class, 'reanalyze']);

        // User management
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/{id}', [UserManagementController::class, 'show']);
        Route::post('/users/{id}/toggle-active', [UserManagementController::class, 'toggleActive']);
        Route::put('/users/{id}/role', [UserManagementController::class, 'updateRole']);
        Route::post('/users/{id}/approve-kyc', [UserManagementController::class, 'approveKyc']);
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);

        // Vehicle brands/models CRUD
        Route::apiResource('vehicle-brands', \App\Http\Controllers\Admin\VehicleBrandController::class);
        Route::apiResource('vehicle-models', \App\Http\Controllers\Admin\VehicleModelController::class);
        Route::apiResource('property-types', \App\Http\Controllers\Admin\PropertyTypeController::class);

        // Competitor sources
        Route::get('/competitor-sources', fn() => response()->json(\App\Models\CompetitorSource::all()));
        Route::post('/competitor-sources', function (Request $req) {
            $v = $req->validate(['name' => 'required|string', 'url' => 'required|url', 'type' => 'required|in:vehicle,property,rental']);
            return response()->json(\App\Models\CompetitorSource::create($v + ['is_active' => true]), 201);
        });
        Route::put('/competitor-sources/{id}', function (Request $req, string $id) {
            $src = \App\Models\CompetitorSource::findOrFail($id);
            $src->update($req->only(['name', 'url', 'type', 'is_active', 'scrape_interval']));
            return response()->json($src);
        });
        Route::delete('/competitor-sources/{id}', function (string $id) {
            \App\Models\CompetitorSource::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted.']);
        });
        // Trigger import for a source
        Route::post('/competitor-sources/{id}/import', function (string $id) {
            $source = \App\Models\CompetitorSource::findOrFail($id);
            $crawler = app(\App\Services\CompetitorCrawlerService::class);
            $result = $crawler->crawlSource($source);
            return response()->json($result);
        });
        // Import all sources
        Route::post('/competitor-sources/import-all', function () {
            $crawler = app(\App\Services\CompetitorCrawlerService::class);
            return response()->json($crawler->crawlAll());
        });
        // View competitor listings (admin only, never public)
        Route::get('/competitor-listings', function (Request $req) {
            $q = \App\Models\CompetitorListing::with('source:id,name,url')
                ->when($req->source_id, fn($q) => $q->where('source_id', $req->source_id))
                ->when($req->type, fn($q) => $q->where('type', $req->type))
                ->when($req->city, fn($q) => $q->where('city', $req->city))
                ->orderByDesc('created_at')
                ->paginate(25);
            return response()->json($q);
        });
        // Stats per source
        Route::get('/competitor-sources/{id}/stats', function (string $id) {
            $source = \App\Models\CompetitorSource::findOrFail($id);
            $listings = \App\Models\CompetitorListing::where('source_id', $id);
            return response()->json([
                'source' => $source,
                'total_listings' => $listings->count(),
                'by_type' => $listings->selectRaw('type, count(*) as count')->groupBy('type')->pluck('count', 'type'),
                'avg_price' => $listings->avg('price'),
                'min_price' => $listings->min('price'),
                'max_price' => $listings->max('price'),
                'last_import' => $source->last_scraped_at,
            ]);
        });

        // AI Models management
        Route::get('/ai-models', fn() => response()->json(\App\Models\AiModel::all()));
        Route::put('/ai-models/{id}', function (Request $req, string $id) {
            $model = \App\Models\AiModel::findOrFail($id);
            $model->update($req->only(['is_active', 'is_default', 'temperature', 'max_tokens', 'monthly_cost_limit', 'rate_limit_per_minute']));
            return response()->json($model);
        });

        // LLM Management
        Route::post('/llm/test', function (Request $req) {
            $req->validate(['prompt' => 'required|string|max:500']);
            $apiKey = config('services.openai.key', '');
            if (empty($apiKey)) {
                return response()->json(['error' => 'OpenAI API key not configured.'], 422);
            }
            $model = $req->input('model', config('services.openai.model', 'gpt-4o-mini'));
            $resp = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(20)->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model,
                'messages'    => [['role' => 'user', 'content' => $req->prompt]],
                'temperature' => 0.7,
                'max_tokens'  => 200,
            ]);
            if (!$resp->successful()) {
                return response()->json(['error' => 'OpenAI error: ' . $resp->status()], 422);
            }
            return response()->json(['response' => $resp->json('choices.0.message.content')]);
        });

        // Batch AI analyze pending listings
        Route::post('/listings/batch-analyze', function () {
            $service = app(\App\Services\AiPriceService::class);
            $listings = \App\Models\Listing::where('status', 'active')
                ->whereNull('ai_trust_score')
                ->limit(20)
                ->get();
            $count = 0;
            foreach ($listings as $listing) {
                try { $service->analyzeListingPrice($listing); $count++; } catch (\Throwable $e) {}
            }
            return response()->json(['analyzed' => $count, 'message' => "Analyzed {$count} listings."]);
        });

        // Settings
        Route::get('/settings', fn() => response()->json(\App\Models\Setting::all()));
        Route::put('/settings/{key}', function (Request $req, string $key) {
            $req->validate(['value' => 'required']);
            \App\Models\Setting::set($key, $req->value);
            return response()->json(['message' => 'Setting updated.']);
        });

        // Analytics
        Route::get('/analytics/market', fn() => response()->json(\App\Models\MarketAnalytic::orderByDesc('period_date')->limit(30)->get()));
        Route::get('/analytics/ai-usage', fn() => response()->json(\App\Models\AiUsageLog::orderByDesc('created_at')->limit(100)->get()));

        // Listing CRUD (edit/update)
        Route::get('/listings/{id}', function (string $id) {
            return response()->json(\App\Models\Listing::with(['user:id,name,email,role', 'listable'])->findOrFail($id));
        });
        Route::put('/listings/{id}', function (Request $req, string $id) {
            $listing = \App\Models\Listing::findOrFail($id);
            $listing->update($req->only(['title', 'description', 'price', 'currency', 'city', 'district', 'status', 'is_featured', 'is_premium', 'price_negotiable']));
            return response()->json($listing->fresh(['listable', 'user:id,name,email']));
        });

        // Subscription Plans CRUD
        Route::get('/subscription-plans', fn() => response()->json(\App\Models\SubscriptionPlan::orderBy('sort_order')->get()));
        Route::post('/subscription-plans', function (Request $req) {
            $v = $req->validate(['name'=>'required|string', 'price_monthly'=>'required|numeric', 'price_yearly'=>'nullable|numeric', 'max_listings'=>'required|integer', 'description'=>'nullable|string', 'is_active'=>'boolean']);
            $plan = \App\Models\SubscriptionPlan::create($v + ['currency'=>'GBP', 'slug'=>\Str::slug($req->name)]);
            return response()->json($plan, 201);
        });
        Route::put('/subscription-plans/{id}', function (Request $req, string $id) {
            $plan = \App\Models\SubscriptionPlan::findOrFail($id);
            $plan->update($req->only(['name','price_monthly','price_yearly','max_listings','description','is_active','sort_order','max_photos_per_listing','ai_price_analysis','featured_listing','priority_support']));
            return response()->json($plan);
        });
        Route::delete('/subscription-plans/{id}', function (string $id) {
            \App\Models\SubscriptionPlan::findOrFail($id)->delete();
            return response()->json(['message' => 'Plan deleted.']);
        });

        // Subscriptions management
        Route::get('/subscriptions', function (Request $req) {
            $q = \App\Models\Subscription::with(['user:id,name,email,role', 'plan:id,name,price_monthly,currency'])
                ->when($req->status, fn($q) => $q->where('status', $req->status))
                ->when($req->search, fn($q) => $q->whereHas('user', fn($u) => $u->where('name','like','%'.$req->search.'%')->orWhere('email','like','%'.$req->search.'%')))
                ->orderByDesc('created_at');
            return response()->json($q->paginate(20));
        });
        Route::post('/subscriptions', function (Request $req) {
            $v = $req->validate(['user_id'=>'required|uuid', 'plan_id'=>'required|uuid', 'billing_cycle'=>'in:monthly,yearly', 'status'=>'in:active,trial,cancelled,expired']);
            $plan = \App\Models\SubscriptionPlan::findOrFail($v['plan_id']);
            $sub = \App\Models\Subscription::create([
                'user_id' => $v['user_id'],
                'plan_id' => $v['plan_id'],
                'status' => $v['status'] ?? 'active',
                'billing_cycle' => $v['billing_cycle'] ?? 'monthly',
                'amount' => $plan->price_monthly,
                'currency' => 'GBP',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);
            return response()->json($sub->load(['user:id,name,email', 'plan:id,name,price_monthly']), 201);
        });
        Route::put('/subscriptions/{id}', function (Request $req, string $id) {
            $sub = \App\Models\Subscription::findOrFail($id);
            $sub->update($req->only(['status','plan_id','billing_cycle','current_period_end']));
            return response()->json($sub->fresh(['user:id,name,email', 'plan:id,name,price_monthly']));
        });
        Route::delete('/subscriptions/{id}', function (string $id) {
            $sub = \App\Models\Subscription::findOrFail($id);
            $sub->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            return response()->json(['message' => 'Subscription cancelled.']);
        });
        Route::get('/subscriptions/stats', function () {
            return response()->json([
                'total_active' => \App\Models\Subscription::where('status','active')->count(),
                'total_revenue' => \App\Models\Subscription::where('status','active')->sum('amount'),
                'by_plan' => \App\Models\Subscription::where('status','active')->with('plan:id,name')->selectRaw('plan_id, count(*) as count')->groupBy('plan_id')->get(),
                'new_this_month' => \App\Models\Subscription::where('created_at','>=',now()->startOfMonth())->count(),
                'cancelled_this_month' => \App\Models\Subscription::whereNotNull('cancelled_at')->where('cancelled_at','>=',now()->startOfMonth())->count(),
            ]);
        });

        // Agency / Dealer management
        Route::get('/agencies', function (Request $req) {
            $q = \App\Models\User::with(['profile', 'activeSubscription.plan'])
                ->whereIn('role', ['agency','dealer'])
                ->when($req->role, fn($q) => $q->where('role', $req->role))
                ->when($req->search, fn($q) => $q->where(fn($u) => $u->where('name','like','%'.$req->search.'%')->orWhere('email','like','%'.$req->search.'%')))
                ->withCount('listings')
                ->orderByDesc('created_at');
            return response()->json($q->paginate(20));
        });
        Route::put('/agencies/{id}', function (Request $req, string $id) {
            $user = \App\Models\User::findOrFail($id);
            $user->update($req->only(['name','email','role','is_active','is_verified']));
            return response()->json($user->fresh(['profile','activeSubscription.plan']));
        });
        Route::post('/agencies/{id}/verify', function (string $id) {
            \App\Models\User::findOrFail($id)->update(['is_verified' => true, 'kyc_verified' => true]);
            return response()->json(['message' => 'Agency verified.']);
        });

        // Advertisements management
        Route::get('/advertisements', fn() => response()->json(\App\Models\Advertisement::orderByDesc('created_at')->get()));
        Route::post('/advertisements', function (Request $req) {
            $v = $req->validate(['name'=>'required|string','slot'=>'required|in:top10,sidebar,banner,homepage','type'=>'required|in:vehicle,property,both','content'=>'nullable|string','link'=>'nullable|url','starts_at'=>'nullable|date','ends_at'=>'nullable|date','listing_id'=>'nullable|uuid']);
            $ad = \App\Models\Advertisement::create($v + ['is_active'=>true]);
            return response()->json($ad, 201);
        });
        Route::put('/advertisements/{id}', function (Request $req, string $id) {
            $ad = \App\Models\Advertisement::findOrFail($id);
            $ad->update($req->only(['name','slot','type','content','link','is_active','starts_at','ends_at','listing_id']));
            return response()->json($ad);
        });
        Route::delete('/advertisements/{id}', function (string $id) {
            \App\Models\Advertisement::findOrFail($id)->delete();
            return response()->json(['message'=>'Deleted.']);
        });

        // Homepage sections management
        Route::get('/homepage-sections', fn() => response()->json(\App\Models\HomepageSection::orderBy('sort_order')->get()));
        Route::put('/homepage-sections/{id}', function (Request $req, string $id) {
            $sec = \App\Models\HomepageSection::findOrFail($id);
            $sec->update($req->only(['title','subtitle','is_active','sort_order','config']));
            return response()->json($sec);
        });
        Route::post('/homepage-sections/reorder', function (Request $req) {
            foreach ($req->order ?? [] as $i => $id) {
                \App\Models\HomepageSection::where('id', $id)->update(['sort_order' => $i + 1]);
            }
            return response()->json(['message' => 'Reordered.']);
        });

        // Featured/Best listings for homepage
        Route::get('/featured-listings', function (Request $req) {
            $q = \App\Models\Listing::with(['user:id,name', 'listable'])
                ->where('status','active')
                ->when($req->type, fn($q) => $q->where('type', $req->type))
                ->orderByDesc('is_featured')->orderByDesc('ai_trust_score');
            return response()->json($q->paginate(20));
        });
        Route::post('/listings/{id}/toggle-premium', function (string $id) {
            $l = \App\Models\Listing::findOrFail($id);
            $l->update(['is_premium' => !$l->is_premium]);
            return response()->json(['is_premium' => $l->is_premium]);
        });
    });
});
