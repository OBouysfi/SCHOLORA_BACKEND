<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tutor_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained()->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->string('certification_name')->nullable();
            $table->boolean('is_custom_certification')->default(false);
            $table->string('custom_certification_name')->nullable();
            $table->year('year_from')->nullable();
            $table->year('year_to')->nullable();
            $table->string('certificate_file')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutor_certifications');
    }
};