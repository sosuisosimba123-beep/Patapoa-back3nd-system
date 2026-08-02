<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->index(['latitude', 'longitude']);
            $table->index('is_verified');
            $table->index('is_online');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_available');
            $table->index('price');
        });

        Schema::table('search_logs', function (Blueprint $table) {
            $table->index('query');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['is_verified']);
            $table->dropIndex(['is_online']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_available']);
            $table->dropIndex(['price']);
        });

        Schema::table('search_logs', function (Blueprint $table) {
            $table->dropIndex(['query']);
            $table->dropIndex(['latitude', 'longitude']);
        });
    }
};
