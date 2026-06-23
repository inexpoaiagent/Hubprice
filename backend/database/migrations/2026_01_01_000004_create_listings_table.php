<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('listing_number')->unique();
            $table->enum('type', ['vehicle', 'property', 'rental'])->index();
            $table->string('listable_type');
            $table->uuid('listable_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('currency', 5)->default('GBP');
            $table->boolean('price_negotiable')->default(false);
            $table->string('country', 3)->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['draft','pending','active','rejected','sold','rented','expired','archived'])->default('draft')->index();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            // AI scores
            $table->decimal('ai_price_score', 5, 2)->nullable();
            $table->decimal('ai_trust_score', 5, 2)->nullable();
            $table->decimal('ai_investment_score', 5, 2)->nullable();
            $table->string('ai_price_status')->nullable(); // fair, slightly_overpriced, overpriced
            $table->decimal('market_min_price', 15, 2)->nullable();
            $table->decimal('market_max_price', 15, 2)->nullable();
            $table->decimal('market_avg_price', 15, 2)->nullable();
            $table->decimal('price_battery_percent', 5, 2)->nullable();
            $table->decimal('ai_confidence_score', 5, 2)->nullable();
            $table->json('ai_analysis')->nullable();
            $table->json('ai_comparable_ids')->nullable();
            $table->json('meta')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['listable_type', 'listable_id']);
            $table->index(['type', 'status']);
            $table->index(['country', 'city']);
        });

        Schema::create('listing_favorites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'listing_id']);
        });

        Schema::create('listing_views', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('rentals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->enum('rental_type', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->string('furnishing')->nullable();
            $table->json('amenities')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_views');
        Schema::dropIfExists('listing_favorites');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('rentals');
    }
};
