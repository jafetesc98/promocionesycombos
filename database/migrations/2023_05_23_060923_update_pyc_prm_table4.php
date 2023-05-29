<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycPrmTable4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_prmdet', function (Blueprint $table) {
            //Agregando columna para guardar la suc de donde se toman los precios base
            $table->string('desc_reg')->nullable();
            //$table->integer('mostrador');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pyc_prmhdr', function (Blueprint $table) {
            $table->dropColumn('desc_reg');
        });
    }
}
