<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoginAdminSeeder extends Seeder
{

    public function run(): void
    {
     //   User::truncate();

        User::create([
            'name'=>'ali',
            'phone'=>'1122334455',
            'pharmacy_name' => 'al hamdan',
            'password'=>'hamdan12345678',
            'role'=>'admin',
        ]);
    }
}
