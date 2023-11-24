<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePycInvdcm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pyc_invdcm', function (Blueprint $table) {
            //$table->id();
            $table->integer('id_pyc_cmb');
            $table->integer('status');
            $table->string('cve_art',10);
            $table->string('des_art',60);
            $table->string('emp',3);
            $table->integer('fac_sal');
            $table->double('precio_0', 8, 2);
            $table->double('precio_1', 8, 2)->nullable();
            $table->double('precio_2', 8, 2)->nullable();
            $table->double('precio_3', 8, 2)->nullable();
            $table->double('precio_4', 8, 2)->nullable();
            $table->integer('cantidad');
            $table->double('dsc', 8, 2)->nullable();
            $table->integer('tpoEmp')->nullable();
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pyc_invdcm');
    }
}
