<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AuthUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
                'name' => 'admin-cloud-secret',
                'password' => bcrypt('123456'),
                'email' => 'admin@cloudsecrets.com',
        ]);

        User::create([
            'name' => 'sales-cloud-secret',
            'password' => bcrypt('123456'),
            'email' => 'sales@cloudsecrets.com',
    ]);

    }
}
