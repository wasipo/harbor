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
        Schema::create('user_roles', function (Blueprint $table) {
            // 外部キー（ユーザーとロールの紐付け）
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('role_id')->constrained()->cascadeOnDelete();
            
            // 割り当て情報
            $table->timestamp('assigned_at')->useCurrent()->comment('割当日時（いつこのロールが付与されたか）');
            $table->foreignUlid('assigned_by')->nullable()->constrained('users')->nullOnDelete()->comment('割当者ID（誰がこのロールを付与したか）');
            
            // 監査用タイムスタンプ
            $table->timestamps();

            // 複合主キー：同じユーザーが同じロールを重複して持てない
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
