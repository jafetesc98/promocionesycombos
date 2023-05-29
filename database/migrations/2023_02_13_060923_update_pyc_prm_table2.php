<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycPrmTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_prmhdr', function (Blueprint $table) {
            //actualizando campos nulos 
            
            $table->string('numProm',10)->nullable()->change();
            $table->string('cte',9)->nullable()->change();
            $table->integer('retail')->nullable()->change();
            $table->string('con_pag',5)->nullable()->change();
            $table->string('seg_0',3)->nullable()->change();
            $table->string('seg_1',3)->nullable()->change();
            $table->string('seg_2',3)->nullable()->change();
            $table->string('seg_3',3)->nullable()->change();
            $table->string('seg_4',3)->nullable()->change();
            $table->integer('uds_limite')->nullable()->change();
            $table->integer('uds_vendidas')->nullable()->change();
            $table->integer('uds_por_cte')->nullable()->change();
            $table->integer('cantidad_minima')->nullable()->change();
            $table->integer('compra_minima')->nullable()->change();
            $table->string('boletin', 10)->nullable()->change();
            $table->string('autoriza',5)->nullable()->change();
            $table->integer('mostrador')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
