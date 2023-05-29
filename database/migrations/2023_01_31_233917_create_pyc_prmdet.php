<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePycPrmdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pyc_prmdet', function (Blueprint $table) {
            //$table->id();
            $table->integer('id_pyc_prom');
            $table->integer('status');
            $table->string('cve_art',10);
            $table->string('des_art',60);
            $table->string('sin_cargo',1);
            $table->integer('cobradas');
            $table->integer('regaladas');
            $table->string('art_reg',10);
            $table->string('emp_reg',3);
            $table->integer('fac_min_reg');
            $table->double('precio_reg', 8, 2);
            $table->double('precio_0', 8, 2);
            $table->double('precio_1', 8, 2);
            $table->double('precio_2', 8, 2);
            $table->double('precio_3', 8, 2);
            $table->double('precio_4', 8, 2);

            $table->double('p_dsc_0', 8, 2);
            $table->double('p_dsc_1', 8, 2);
            $table->double('p_dsc_2', 8, 2);
            $table->double('Monto_Dsc', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pyc_prmdet');
    }
}
