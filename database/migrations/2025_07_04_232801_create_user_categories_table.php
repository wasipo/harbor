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
        Schema::create('user_categories', function (Blueprint $table) {
            // 主キー（ULID）
            $table->ulid('id')->primary()->comment('カテゴリID（ULID）');
            
            // カテゴリ識別情報
            $table->string('code', 100)->unique()->comment('種別コード（engineer/accounting/sales等）');
            $table->string('name', 255)->comment('種別名（エンジニア/経理/営業等）');
            $table->text('description')->nullable()->comment('カテゴリの説明');
            
            // カテゴリ状態管理
            $table->boolean('is_active')->default(true)->comment('有効フラグ（false=廃止されたカテゴリ）');
            
            // 監査用タイムスタンプ
            $table->timestamps();

            // パフォーマンス最適化のインデックス
            $table->index('is_active');  // 有効なカテゴリのフィルタリング用
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_categories');
    }
};
