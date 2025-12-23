<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('page_url')->nullable();
            $table->string('page_title')->nullable();
            $table->string('referrer')->nullable();
            $table->string('referrer_domain')->nullable();
            
            // Device Information
            $table->enum('device_type', ['desktop', 'mobile', 'tablet', 'unknown'])->default('unknown');
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            
            // GeoIP Information
            $table->string('country_code', 5)->nullable();
            $table->string('country_name')->nullable();
            $table->string('city')->nullable();
            
            // Visitor Classification
            $table->boolean('is_new_visitor')->default(true);
            $table->boolean('is_bot')->default(false);
            
            // For tracking unique visitors
            $table->string('visitor_hash')->nullable()->index();
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('created_at');
            $table->index('is_bot');
            $table->index('device_type');
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
