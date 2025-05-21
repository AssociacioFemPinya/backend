<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CastellersLanguageNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('castellers', function (Blueprint $table) {
			$table->string('language', 2)->nullable()->change();
        });

        DB::table('castellers')
            ->update([
                "language" => null
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('castellers', function (Blueprint $table) {
            $table->string('language', 2)->default('ca')->change();
        });

        DB::table('castellers')
            ->where('language',null)
            ->update([
                "language" => "ca"
        ]);
    }
}
