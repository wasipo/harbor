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
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('category_id')->constrained('user_categories')->cascadeOnDelete();
            $table->string('attribute_key')->comment('属性キー');
            $table->json('attribute_value')->comment('属性値');
            $table->timestamps();

            // 同一カテゴリ内で同じ属性キーは重複禁止
            $table->unique(['category_id', 'attribute_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_attributes');
    }
};
