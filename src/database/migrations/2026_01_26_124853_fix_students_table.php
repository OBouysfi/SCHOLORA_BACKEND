<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {

            // Remove auth junk
            if (Schema::hasColumn('students', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('students', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('students', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('students', 'name')) {
                $table->dropColumn('name');
            }

            // Add profile fields
            if (!Schema::hasColumn('students', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'last_name')) {
                $table->string('last_name')->nullable();
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
