<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Usuarios para compras qeu tienen asignado un numero de comprador
        //Solo se estan dando de alta los usuarios que estan laborando actualmente  en 13/02/2023
        //Comprador generico si tiene cuentas, se esperan instrucciones para ver que hacer
        //NOTA:: el campo password solo se utiliza para el usuario ADMIN
        //Los demas usuarios usan la clave de Merksyst

        //Compradores
        DB::table('pyc_users')->insert([
            'name' => 'MARIA ARGELIA LOPEZ ROJAS',
            //'email' => 'admin@miniabastos.mx',
            'password' => Hash::make('maria'),
            'user_mks' => 'MARIALR',
            'cve_corta' => 'MAL'
        ]);
        DB::table('pyc_users')->insert([
            'name' => 'CARMEN MENDEZ VALLEJO',
            //'email' => 'admin@miniabastos.mx',
            'password' => Hash::make('carmen'),
            'user_mks' => 'CARMEN',
            'cve_corta' => 'CAR'
        ]);
        DB::table('pyc_users')->insert([
            'name' => 'ARACELI MARTINEZ MENDEZ',
            //'email' => 'admin@miniabastos.mx',
            'password' => Hash::make('araceli'),
            'user_mks' => 'ARACELIMM',
            'cve_corta' => 'MMA'
        ]);
        DB::table('pyc_users')->insert([
            'name' => 'ILSE RUBI ROJAS HERNANDEZ',
            //'email' => 'admin@miniabastos.mx',
            'password' => Hash::make('ilse'),
            'user_mks' => 'RUBIR',
            'cve_corta' => 'IRR'
        ]);
        DB::table('pyc_users')->insert([
            'name' => 'MIYOSHI SANTIAGO ACEVEDO',
            //'email' => 'admin@miniabastos.mx',
            'password' => Hash::make('miyoshi'),
            'user_mks' => 'MIYOSHISA',
            'cve_corta' => 'MSA'
        ]);

        //MKT
        DB::table('pyc_users')->insert([
            'name' => 'FERNANDO PALOMARES MENDOZA',
            //'email' => 'admin@miniabastos.mx',
            'password' => Hash::make('fernando'),
            'user_mks' => 'FERNANDO',
            'cve_corta' => 'FER'
        ]);

        //A la espera de nuevos usuarios de MKT

    }
}
