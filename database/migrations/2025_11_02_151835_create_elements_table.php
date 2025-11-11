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

            // 🔗 projectsテーブルとのリレーション
            $table->foreignId('project_id')
                  ->constrained()
                  ->onDelete('cascade');

            // 🧩 JSで生成した要素を保存
            $table->string('keyword', 255);
            $table->string('env', 50);
            $table->string('laravel_version', 50);
            $table->string('table_name', 255);
            $table->string('model_name', 255);
            $table->string('controller_name', 255);
            $table->string('db_name', 255);
            $table->timestamps();
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
