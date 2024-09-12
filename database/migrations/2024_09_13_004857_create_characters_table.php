<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // キャラクター名
            $table->string('occupation');   // 職業
            $table->integer('age');         // 年齢
            $table->string('sex');          // 性別
            $table->text('skills')->nullable(); // スキル（JSON形式で保存）
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('characters');
    }
}