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
        Schema::create('permissions', function (Blueprint $table) {
            // 主キー（ULID）
            $table->ulid('id')->primary()->comment('権限ID（ULID）');

            // 権限識別情報
            $table->string('key')->unique()->comment('権限キー（user.read, sales.manage等）');
            $table->string('resource')->comment('リソース名（user, sales, post等）');
            $table->string('action')->comment('アクション（read, write, delete等）');

            // 表示情報
            $table->string('display_name')->comment('表示名');
            $table->text('description')->nullable()->comment('権限の説明');

            // 監査用タイムスタンプ
            $table->timestamps();

            // インデックス
            $table->index(['resource', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
