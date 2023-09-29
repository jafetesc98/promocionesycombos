<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreacionColumnasCreatedYUpdatedPycPrmhdr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_prmhdr', function (Blueprint $table) {
            //
            $table->dateTime('created_at',0)->nullable();
            $table->dateTime('updated_at',0)->nullable();
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
            //
        });
    }
}
