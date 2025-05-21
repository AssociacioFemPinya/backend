<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory;


class AddApiTokenFieldOnCastellerConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('casteller_config', function ($table) {
            $table->string('api_token',32)->nullable();
            $table->boolean('api_token_enabled')->default(0);
        });

        $faker = Factory::create();

        $castellerConfigs = DB::table('casteller_config')->select('id_casteller_config')->get();

        foreach($castellerConfigs as $castellerConfig){
            DB::table('casteller_config')
                ->where('id_casteller_config', $castellerConfig->id_casteller_config)
                ->update([
                "api_token" => $faker->regexify('[A-Za-z0-9]{32}')
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('casteller_config', function ($table) {
            $table->dropColumn('api_token');
            $table->dropColumn('api_token_enabled');

        });
    }
}
