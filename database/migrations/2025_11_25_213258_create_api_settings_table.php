<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('api_settings')->insert([
            ['key' => 'api_provider', 'value' => 'freecurrencyapi', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'api_key', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'cache_duration', 'value' => '60', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'api_enabled', 'value' => 'true', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_settings');
    }
};
