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
        Schema::create('tasks', function (Blueprint $table) {
        $table->id();                                // id（自動インクリメント、主キー）
        $table->string('title');                    // title（string, 必須想定）
        $table->text('description')->nullable();    // description（text, nullable）
        $table->boolean('is_done')->default(false); // is_done（boolean, デフォルト false）
        $table->date('due_date')->nullable();       // due_date（date, nullable）
        $table->timestamps();                       // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
