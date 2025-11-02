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
        Schema::create('elements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            $table->string('name');               // 要素名
            $table->string('db')->nullable();     // DB名
            $table->string('model')->nullable();  // モデル名
            $table->string('table')->nullable();  // 関連テーブル名
            $table->timestamps();                 // created_at / updated_at（こちらもこれでOK）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elements');
    }
};
