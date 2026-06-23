<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['buyer','seller','dealer','agency','investor'])->default('seller');
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->string('currency', 5)->default('GBP');
            $table->integer('max_listings')->default(5);
            $table->integer('max_photos_per_listing')->default(10);
            $table->boolean('ai_price_analysis')->default(false);
            $table->boolean('ai_trust_score')->default(false);
            $table->boolean('ai_investment_score')->default(false);
            $table->boolean('featured_listing')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('plan_id')->constrained('subscription_plans');
            $table->string('status'); // active, cancelled, expired, trial
            $table->string('billing_cycle'); // monthly, yearly
            $table->decimal('amount', 10, 2);
            $table->string('currency', 5)->default('GBP');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_subscription_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->string('payment_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 5)->default('GBP');
            $table->string('status'); // pending, completed, failed, refunded
            $table->string('gateway'); // stripe, paypal, manual
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_receipt')->nullable();
            $table->json('gateway_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 5)->default('GBP');
            $table->string('status'); // draft, issued, paid, void
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
