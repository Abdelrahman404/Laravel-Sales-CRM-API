<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInfo;
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
        $admin = User::create([
                'username' => 'admin',
                'name_en' => 'admin-cloud-secret',
                'name_ar' => 'مدير شركه اسرار السحابة',
                'password' => bcrypt('123456'),
                'email' => 'admin@cloudsecrets.com', 
                'type' => 'admin'
        ]);

       $seller = User::create([
            'username' => 'sales',
            'name_en' => 'sales-cloud-secret',
            'name_ar' => 'بائع شركة أسرار السحابة',
            'password' => bcrypt('123456'),
            'email' => 'sales@cloudsecrets.com',
            'type' => 'seller'
    ]);

        UserInfo::create([
            'user_id' => $seller->id,
            'country_id' => 1,
            'target' => '5000',
            'comission' => '20'
        ]);


    }
}
