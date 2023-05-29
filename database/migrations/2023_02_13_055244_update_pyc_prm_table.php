<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycPrmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_prmhdr', function (Blueprint $table) {
            $table->string('autoriza',5);
            $table->integer('mostrador');
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
            $table->dropColumn('autoriza');
            $table->dropColumn('mostrador');
        });
    }
}
