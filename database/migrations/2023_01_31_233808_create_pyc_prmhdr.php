<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePycPrmhdr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pyc_prmhdr', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->string('numProm',10);
            $table->char('desProm',60);
            $table->string('fec_ini',8);
            $table->string('fec_fin',8);
            $table->string('hra_ini',8);
            $table->string('hra_fin',8);
            $table->string('inc_similares',1);
            $table->integer('tpoProm');
            $table->string('cte',9);
            $table->integer('retail');
            $table->string('con_pag',5);
            $table->string('seg_0',3);
            $table->string('seg_1',3);
            $table->string('seg_2',3);
            $table->string('seg_3',3);
            $table->string('seg_4',3);
            $table->integer('uds_limite');
            $table->integer('uds_vendidas');
            $table->integer('uds_por_cte');
            $table->integer('cantidad_minima');
            $table->integer('compra_minima');
            $table->string('u_alt',3);

            $table->string('proveedor', 9);
            $table->integer('paga');
            $table->string('folio_ac', 10);
            $table->string('boletin', 10);


            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pyc_prmhdr');
    }
}
