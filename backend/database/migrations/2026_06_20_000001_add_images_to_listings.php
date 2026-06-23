<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'images')) {
                $table->json('images')->nullable()->after('country');
            }
            if (!Schema::hasColumn('listings', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('images');
            }
            if (!Schema::hasColumn('listings', 'video_url')) {
                $table->string('video_url')->nullable()->after('thumbnail');
            }
        });
    }
    public function down(): void {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['images', 'thumbnail', 'video_url']);
        });
    }
};