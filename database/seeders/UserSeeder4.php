<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserSeeder4 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pyc_users')->insert([
            'name' => 'KARLA MARIANA VELAZQUEZ SANCHEZ',
            //'email' => 'admin@miniabastos.mx',
            'password' => Hash::make('karla'),
            'user_mks' => 'KARLAV',
            'cve_corta' => 'KVS'
        ]);
    }
}
