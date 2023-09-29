<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       //Compradores
       DB::table('pyc_users')->insert([
        'name' => 'ALMA DELIA CRUZ HERNANDEZ',
        //'email' => 'admin@miniabastos.mx',
        'password' => Hash::make('alma'),
        'user_mks' => 'ALMDE',
        'cve_corta' => 'AD5'
    ]);
    DB::table('pyc_users')->insert([
        'name' => 'EDGAR JONATHAN BAUTISTA VENTURA',
        //'email' => 'admin@miniabastos.mx',
        'password' => Hash::make('edgar'),
        'user_mks' => 'EDGARJ',
        'cve_corta' => 'JBV'
    ]);
    DB::table('pyc_users')->insert([
        'name' => 'KARLA FERNANDA REYES GONZALES',
        //'email' => 'admin@miniabastos.mx',
        'password' => Hash::make('karla'),
        'user_mks' => 'KARLAR',
        'cve_corta' => 'KAF'
    ]);
    DB::table('pyc_users')->insert([
        'name' => 'YAZMINE GARCIA LEON',
        //'email' => 'admin@miniabastos.mx',
        'password' => Hash::make('yazmine'),
        'user_mks' => 'YAZMINE',
        'cve_corta' => 'YZM'
    ]);
    DB::table('pyc_users')->insert([
        'name' => 'FABIAN HERNANDEZ GOMEZ',
        //'email' => 'admin@miniabastos.mx',
        'password' => Hash::make('fabian'),
        'user_mks' => 'HFABIAN',
        'cve_corta' => 'FBI'
    ]);
    DB::table('pyc_users')->insert([
        'name' => 'CLAUDIA LORENA MENDOZA HERNANDEZ',
        //'email' => 'admin@miniabastos.mx',
        'password' => Hash::make('claudia'),
        'user_mks' => 'CLAUDIAL',
        'cve_corta' => 'CLO'
    ]);

    }
}
