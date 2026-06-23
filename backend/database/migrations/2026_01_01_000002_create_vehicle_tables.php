<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained('vehicle_brands')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('body_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained('vehicle_brands');
            $table->foreignUuid('model_id')->constrained('vehicle_models');
            $table->string('variant')->nullable();
            $table->year('year');
            $table->string('fuel_type')->nullable(); // petrol, diesel, electric, hybrid
            $table->string('transmission')->nullable(); // manual, automatic, cvt
            $table->string('body_type')->nullable();
            $table->string('color')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('engine_size')->nullable();
            $table->integer('horsepower')->nullable();
            $table->string('vin')->nullable()->unique();
            $table->string('registration')->nullable();
            $table->string('condition')->default('used'); // new, used, certified
            $table->json('features')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_models');
        Schema::dropIfExists('vehicle_brands');
    }
};
