<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('provider'); // openai, anthropic, gemini, xai, custom
            $table->string('model_id');
            $table->string('purpose'); // price_analysis, trust_score, investment, fraud, chat
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->decimal('temperature', 3, 2)->default(0.7);
            $table->integer('max_tokens')->default(2000);
            $table->decimal('top_p', 3, 2)->default(1.0);
            $table->decimal('cost_per_1k_tokens', 8, 6)->nullable();
            $table->decimal('monthly_cost_limit', 10, 2)->nullable();
            $table->integer('rate_limit_per_minute')->nullable();
            $table->json('fallback_chain')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ai_prompt_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('purpose');
            $table->text('system_prompt');
            $table->text('user_prompt_template');
            $table->boolean('is_active')->default(true);
            $table->json('variables')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('model_id')->nullable()->constrained('ai_models')->nullOnDelete();
            $table->string('purpose');
            $table->string('entity_type')->nullable();
            $table->uuid('entity_id')->nullable();
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->integer('latency_ms')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('price_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->decimal('price', 15, 2);
            $table->string('currency', 5)->default('GBP');
            $table->string('changed_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('market_analytics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // vehicle, property, rental
            $table->string('segment')->nullable();
            $table->string('country', 3)->nullable();
            $table->string('city')->nullable();
            $table->decimal('avg_price', 15, 2)->nullable();
            $table->decimal('min_price', 15, 2)->nullable();
            $table->decimal('max_price', 15, 2)->nullable();
            $table->decimal('median_price', 15, 2)->nullable();
            $table->integer('listing_count')->default(0);
            $table->decimal('demand_index', 5, 2)->nullable();
            $table->decimal('supply_index', 5, 2)->nullable();
            $table->decimal('price_trend', 5, 2)->nullable(); // % change
            $table->json('breakdown')->nullable();
            $table->date('period_date');
            $table->timestamps();
        });

        Schema::create('competitor_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('url');
            $table->string('type'); // vehicle, property, rental
            $table->boolean('is_active')->default(true);
            $table->string('scrape_interval')->default('daily');
            $table->timestamp('last_scraped_at')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('competitor_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('source_id')->constrained('competitor_sources')->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('type'); // vehicle, property, rental
            $table->string('title')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('currency', 5)->nullable();
            $table->string('country', 3)->nullable();
            $table->string('city')->nullable();
            $table->json('attributes')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('listed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('fraud_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('listing_id')->nullable()->constrained('listings')->nullOnDelete();
            $table->foreignUuid('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason');
            $table->text('description')->nullable();
            $table->decimal('risk_score', 5, 2)->nullable();
            $table->json('ai_analysis')->nullable();
            $table->enum('status', ['pending', 'investigating', 'confirmed', 'dismissed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_reports');
        Schema::dropIfExists('competitor_listings');
        Schema::dropIfExists('competitor_sources');
        Schema::dropIfExists('market_analytics');
        Schema::dropIfExists('price_histories');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_prompt_templates');
        Schema::dropIfExists('ai_models');
    }
};
