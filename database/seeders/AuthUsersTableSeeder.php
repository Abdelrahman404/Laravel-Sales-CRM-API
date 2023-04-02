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
                'name_en' => 'admin-cloud-secret',
                'name_ar' => 'مدير شركه اسرار السحابة',
                'password' => bcrypt('123456'),
                'email' => 'admin@cloudsecrets.com',
        ]);

        User::create([
            'name_en' => 'sales-cloud-secret',
            'name_ar' => 'بائع شركة أسرار السحابة',
            'password' => bcrypt('123456'),
            'email' => 'sales@cloudsecrets.com',
    ]);

    }
}
