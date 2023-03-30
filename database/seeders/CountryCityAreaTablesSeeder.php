<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;


class CountryCityAreaTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Country::create([
            'id' => 1,
            'name_ar' => 'المملكة العرية السعودية',
            'name_en' => 'Kingdom of Saudi Arabia',
        ]);

        $json = Storage::disk('local')->get('/JSON/cities.json');
        $cities = json_decode($json, true);
        foreach ($cities as $city) {
            City::query()->updateOrCreate([
                'id' => $city['city_id'],
                'country_id' => 1,
                'name_ar' => $city['name_ar'],
                'name_en' => $city['name_en'],
            ]);
        
         $json = Storage::disk('local')->get('/JSON/districts.json');
         $areas = json_decode($json, true);
         foreach ($areas as $area) {
            Area::query()->updateOrCreate([
                'city_id' => $area['city_id'],
                'name_ar' => $area['name_ar'],
                'name_en' => $area['name_en'],
            ]);
        


    }
}
    }}
