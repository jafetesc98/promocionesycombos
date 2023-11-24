<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePycInvhcm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pyc_invhcm', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->string('numProm',10)->nullable();
            $table->char('desProm',60);
            $table->string('fec_ini',8);
            $table->string('fec_fin',8);
            $table->string('hra_ini',8);
            $table->string('hra_fin',8);
            $table->string('inc_similares',1);
            $table->integer('tpoProm');
            $table->string('cte',9)->nullable();
            $table->integer('retail');
            $table->string('con_pag',5)->nullable();
            $table->string('seg_0',3)->nullable();
            $table->string('seg_1',3)->nullable();
            $table->string('seg_2',3)->nullable();
            $table->string('seg_3',3)->nullable();
            $table->string('seg_4',3)->nullable();
            $table->integer('NumArtCom');
            $table->integer('NumArtReg');/* 
            $table->string('giro_1',3)->nullable();
            $table->string('giro_2',3)->nullable();
            $table->string('giro_3',3)->nullable();
            $table->string('giro_4',3)->nullable(); */
            $table->integer('uds_limite')->nullable();
            $table->integer('uds_vendidas')->nullable();
            $table->integer('uds_por_cte')->nullable();
            $table->string('u_alt',3);
            $table->string('proveedor', 9);
            $table->integer('paga');
            $table->string('folio_ac', 10);
            $table->string('boletin', 20)->nullable();
            $table->string('autoriza',5)->nullable();
            $table->string('giro_0',3)->nullable();
            $table->string('suc_prec_base',3);
            $table->string('usa_limite',1)->nullable();
            $table->string('indicador')->nullable();
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
        Schema::dropIfExists('pyc_invhcm');
    }
}
