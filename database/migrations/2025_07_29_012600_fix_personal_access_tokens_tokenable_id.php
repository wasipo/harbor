<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // tokenable_idをULIDに対応できるようにstring(26)に変更
            $table->string('tokenable_id', 26)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ULIDからBigIntegerへの変換はデータロスが発生するため実装しない
        throw new RuntimeException('This migration cannot be rolled back due to data loss concerns.');
    }
};
