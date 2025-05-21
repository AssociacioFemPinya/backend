<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInteractionTypeCastellers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('castellers', function (Blueprint $table) {
            $table->integer('interaction_type')->nullable()->after('status');
			$table->string('language', 2)->default('ca')->after('status');
			});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('castellers', function (Blueprint $table) {
			$table->dropColumn('interaction_type');
            $table->dropColumn('language');
		});
    }
}
