<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_users', function (Blueprint $table) {
            $table->integer('sexo')->default(1);
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
        Schema::table('pyc_users', function (Blueprint $table) {
            $table->dropColumn('sexo');
        });
    }
}
