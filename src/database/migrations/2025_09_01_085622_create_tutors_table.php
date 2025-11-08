<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            
            // About Step
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('country')->nullable();
            $table->string('main_subject')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_over_18')->default(false);
            
            // Photo Step
            $table->string('profile_photo')->nullable();
            
            // Description Step
            $table->text('description')->nullable();
            
            // Video Step
            $table->string('intro_video')->nullable();
            $table->string('video_link')->nullable();
            $table->string('video_thumbnail')->nullable();
            
            // Pricing Step
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('currency', 3)->default('MAD');
            
            // Status & Verification
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Profile Stats
            $table->integer('total_hours')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('total_reviews')->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutors');
    }
};