<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkillsToCharactersTable extends Migration
{
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->json('skills')->nullable()->after('sex');
        });
    }

    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('skills');
        });
    }
}
