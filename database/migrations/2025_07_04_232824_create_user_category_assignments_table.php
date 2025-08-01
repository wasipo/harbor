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
        Schema::create('user_category_assignments', function (Blueprint $table) {
            // 主キー（履歴管理のため独自ID）
            $table->id();
            
            // 外部キー関連（ユーザーとカテゴリの紐付け）
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUlid('category_id')->constrained('user_categories')->onDelete('cascade');
            
            // 割り当て属性
            $table->boolean('is_primary')->default(false)->comment('主種別フラグ（ユーザーの主たる職種）');
            
            // 履歴管理（同一ユーザー・カテゴリでも期間違いで複数レコード可能）
            $table->date('effective_from')->comment('有効開始日（この日からカテゴリが有効）');
            $table->date('effective_until')->nullable()->comment('有効終了日（nullは無期限）');
            
            // 監査用タイムスタンプ
            $table->timestamps();

            // パフォーマンス最適化のインデックス
            $table->index(['user_id', 'effective_from', 'effective_until'], 'idx_user_effective_period');  // 期間検索用
            $table->index(['user_id', 'is_primary'], 'idx_user_primary_category');  // 主種別検索用
            $table->index(['category_id', 'effective_from'], 'idx_category_assignments');  // カテゴリ別検索用
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_category_assignments');
    }
};
