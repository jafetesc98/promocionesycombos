<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycPrmdetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_prmdet', function (Blueprint $table) {
            $table->string('art_reg',10)->nullable()->change();
            $table->string('emp_reg',3)->nullable()->change();
            $table->float('cobradas', 8, 2)->change();
            $table->float('regaladas', 8, 2)->change();
            $table->float('fac_min_reg', 8, 2)->change();
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
