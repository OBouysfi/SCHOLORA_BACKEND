<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 2); // ISO 2-letter code
            $table->string('flag_emoji')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Seed with some initial countries
        DB::table('countries')->insert([
            ['name' => 'Morocco', 'code' => 'MA', 'flag_emoji' => '🇲🇦'],
            ['name' => 'France', 'code' => 'FR', 'flag_emoji' => '🇫🇷'],
            ['name' => 'Spain', 'code' => 'ES', 'flag_emoji' => '🇪🇸'],
            ['name' => 'Germany', 'code' => 'DE', 'flag_emoji' => '🇩🇪'],
            ['name' => 'Italy', 'code' => 'IT', 'flag_emoji' => '🇮🇹'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'flag_emoji' => '🇬🇧'],
            ['name' => 'United States', 'code' => 'US', 'flag_emoji' => '🇺🇸'],
            ['name' => 'Canada', 'code' => 'CA', 'flag_emoji' => '🇨🇦'],
            ['name' => 'Australia', 'code' => 'AU', 'flag_emoji' => '🇦🇺'],
            ['name' => 'Japan', 'code' => 'JP', 'flag_emoji' => '🇯🇵'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
};