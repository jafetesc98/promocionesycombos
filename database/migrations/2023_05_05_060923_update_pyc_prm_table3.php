<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycPrmTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_prmhdr', function (Blueprint $table) {
            //Agregando columna para guardar la suc de donde se toman los precios base
            $table->string('suc_prec_base')->default('001');
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
            $table->dropColumn('suc_prec_base');
        });
    }
}
