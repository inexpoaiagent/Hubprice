<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('property_type_id')->constrained('property_types');
            $table->string('title');
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->integer('floors')->nullable();
            $table->integer('floor_number')->nullable();
            $table->integer('build_year')->nullable();
            $table->string('furnishing')->nullable(); // furnished, unfurnished, semi-furnished
            $table->string('condition')->nullable(); // new, good, needs-renovation
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_garden')->default(false);
            $table->boolean('has_pool')->default(false);
            $table->boolean('has_elevator')->default(false);
            $table->json('amenities')->nullable();
            $table->json('nearby_locations')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
        Schema::dropIfExists('property_types');
    }
};
