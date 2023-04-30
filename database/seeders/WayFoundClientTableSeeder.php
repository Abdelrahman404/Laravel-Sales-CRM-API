<?php

namespace Database\Seeders;

use App\Models\WayFoundClient;
use Illuminate\Database\Seeder;

class WayFoundClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        WayFoundClient::truncate();

        WayFoundClient::create([
            'id' => 1 ,
            'name_ar' => 'مجهود شخصي',
            'name_en' => 'Personal effort'
        ]);

        WayFoundClient::create([
            'id' => 2 ,
            'name_ar' => 'علاقات',
            'name_en' => 'Relationships'
        ]);

        WayFoundClient::create([
            'id' => 3 ,
            'name_ar' => 'عن طريق حملة ترويجية علي وسائل التواصل',
            'name_en' => 'Through a promotional campaign on scoial media'
        ]);

        WayFoundClient::create([
            'id' => 4 ,
            'name_ar' => 'عن طريق إعلان',
            'name_en' => 'Through an advertisement'
        ]);

        WayFoundClient::create([
            'id' => 5 ,
            'name_ar' => 'هو الذي سأل عن خدمة',
            'name_en' => 'He/she is the one who inquired about the service'
        ]);

    }
}
