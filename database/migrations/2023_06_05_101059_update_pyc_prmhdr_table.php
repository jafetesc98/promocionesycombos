<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycPrmhdrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_prmhdr', function (Blueprint $table) {
            //Agregando columna cuando tiene dos promociones en una
            $table->string('numPromReg')->nullable();
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
            $table->dropColumn('numPromReg');
        });
    }
}
