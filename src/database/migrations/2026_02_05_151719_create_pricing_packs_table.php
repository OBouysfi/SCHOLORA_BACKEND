<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pricing_packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 8, 2)->default(0);
            $table->string('currency', 3)->default('MAD');
            $table->enum('billing_period', ['monthly', 'annual'])->default('monthly');
            
            // Limits
            $table->integer('virtual_classrooms')->default(1);
            $table->integer('sessions_per_week')->nullable(); // null = unlimited
            $table->integer('max_students')->default(10);
            
            // Features
            $table->boolean('basic_payment_collection')->default(true);
            $table->boolean('automated_invoicing')->default(false);
            $table->boolean('student_roster')->default(true);
            $table->boolean('attendance_tracking')->default(true);
            $table->boolean('full_tool_suite')->default(false);
            $table->boolean('premium_features')->default(false);
            
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // Additional features
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pricing_packs');
    }
};