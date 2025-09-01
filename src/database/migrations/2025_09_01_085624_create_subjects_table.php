<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Seed with initial subjects
        DB::table('subjects')->insert([
            ['name' => 'English', 'slug' => 'english', 'category' => 'Languages'],
            ['name' => 'French', 'slug' => 'french', 'category' => 'Languages'],
            ['name' => 'Spanish', 'slug' => 'spanish', 'category' => 'Languages'],
            ['name' => 'German', 'slug' => 'german', 'category' => 'Languages'],
            ['name' => 'Arabic', 'slug' => 'arabic', 'category' => 'Languages'],
            ['name' => 'Chinese', 'slug' => 'chinese', 'category' => 'Languages'],
            ['name' => 'Mathematics', 'slug' => 'mathematics', 'category' => 'Sciences'],
            ['name' => 'Physics', 'slug' => 'physics', 'category' => 'Sciences'],
            ['name' => 'Chemistry', 'slug' => 'chemistry', 'category' => 'Sciences'],
            ['name' => 'Biology', 'slug' => 'biology', 'category' => 'Sciences'],
            ['name' => 'History', 'slug' => 'history', 'category' => 'Humanities'],
            ['name' => 'Geography', 'slug' => 'geography', 'category' => 'Humanities'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
};