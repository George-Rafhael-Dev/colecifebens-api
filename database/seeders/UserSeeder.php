<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'       => 'George Rafhael',
            'email'      => 'george@email.com',
            'password'   => bcrypt('123456'),
            'cpf'        => '000.000.000-00',
            'birth_date' => '2000-01-01',
            'phone'      => '81999999999',
        ]);

        User::create([
            'name'       => 'Edmilson Ronaldy',
            'email'      => 'edmilson@email.com',
            'password'   => bcrypt('123456'),
            'cpf'        => '111.111.111-11',
            'birth_date' => '1999-05-15',
            'phone'      => '81888888888',
        ]);
    }
}