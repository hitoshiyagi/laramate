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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // ユーザーID
            $table->string('name');                // プロジェクト名
            $table->string('repo')->nullable();    // リポジトリ名
            $table->string('database_name')->nullable(); //データベース名
            $table->unique(['user_id', 'name']);  //重複チェク
            $table->timestamps();                  // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
