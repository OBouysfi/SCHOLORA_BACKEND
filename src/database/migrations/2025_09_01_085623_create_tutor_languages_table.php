<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tutor_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained()->onDelete('cascade');
            $table->string('language');
            $table->enum('level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'Native']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutor_languages');
    }
};
