<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->foreignId('pricing_pack_id')->nullable()->after('currency')->constrained()->nullOnDelete();
            $table->timestamp('pack_subscribed_at')->nullable();
            $table->timestamp('pack_expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropForeign(['pricing_pack_id']);
            $table->dropColumn(['pricing_pack_id', 'pack_subscribed_at', 'pack_expires_at']);
        });
    }
};