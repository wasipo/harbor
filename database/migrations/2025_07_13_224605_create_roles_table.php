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
        Schema::create('roles', function (Blueprint $table) {
            // 主キー（ULID）
            $table->ulid('id')->primary()->comment('ロールID（ULID）');
            
            // ロール識別情報
            $table->string('name')->comment('ロール名（システム内部名：super_admin, editor等）');
            $table->string('display_name')->comment('表示名（画面表示用：スーパー管理者、編集者等）');
            
            // 監査用タイムスタンプ
            $table->timestamps();

            // 制約とインデックス
            $table->unique('name');      // ロール名の一意性保証
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
