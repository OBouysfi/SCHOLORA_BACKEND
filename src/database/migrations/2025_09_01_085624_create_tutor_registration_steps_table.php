<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tutor_registration_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained()->onDelete('cascade');
            $table->string('step_name');
            $table->enum('status', ['incomplete', 'complete', 'pending_review'])->default('incomplete');
            $table->json('data')->nullable(); // Store step-specific data as JSON
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['tutor_id', 'step_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutor_registration_steps');
    }
};
