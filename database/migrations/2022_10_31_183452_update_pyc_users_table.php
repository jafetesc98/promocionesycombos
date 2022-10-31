<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePycUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pyc_users', function (Blueprint $table) {
            //Haciendo nullable campo email
            $table->string('email')->nullable()->change();
            
            // Eliminando indice email
            $table->dropIndex('pyc_users_email_unique');

            //actualizando longitud de campos 
            $table->string('name', 100)->change();
            

            //$table->string('password', 20)->change();
            //No se actualiza la longitud ya que es necesaria la default por
            //La encriptacion

            //$table->string('email', 50)->change();
            //No se puede cambiar la longitud en una sola llamada al método
            //Cambiar longitud directamente en SQLServer manualmente (OPCIONAL)

            //Agregando campos requeridos
            $table->string('user_mks', 20);
            $table->string('cve_corta', 5);
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
            $table->dropColumn('user_mks');
            $table->dropColumn('cve_corta');
        });
    }
}
