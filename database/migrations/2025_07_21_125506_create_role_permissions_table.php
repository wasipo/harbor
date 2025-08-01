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
        Schema::create('role_permissions', function (Blueprint $table) {
            // 外部キー
            $table->foreignUlid('role_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('permission_id')->constrained()->cascadeOnDelete();
            
            // 監査用タイムスタンプ
            $table->timestamps();
            
            // 複合主キー設定
            $table->primary(['role_id', 'permission_id']);
            
            // 外部キー制約なし（方針に従う）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
