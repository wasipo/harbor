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
        Schema::create('users', function (Blueprint $table) {
            // 主キー（ULID）
            $table->ulid('id')->primary()->comment('ユーザーID（ULID）');

            // ユーザー基本情報
            $table->string('name')->comment('表示名');
            $table->string('email')->unique()->comment('ログインID兼メールアドレス');
            $table->timestamp('email_verified_at')->nullable()->comment('メール認証日時（nullは未認証）');
            $table->string('password')->comment('bcryptハッシュ済みパスワード');

            // アカウント状態管理
            $table->boolean('is_active')->default(true)->comment('アカウント有効フラグ（false=休止/退会）');

            // Laravel認証用トークン（Remember me機能）
            $table->rememberToken();

            // 監査用タイムスタンプ
            $table->timestamps();

            // パフォーマンス最適化のインデックス
            $table->index('is_active');  // アクティブユーザーのフィルタリング用
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
