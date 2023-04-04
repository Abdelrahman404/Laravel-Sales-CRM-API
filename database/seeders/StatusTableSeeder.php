<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $json = Storage::disk('local')->get('/JSON/cases.json');
        $cases = json_decode($json, true);
        foreach ($cases as $case) {
            Status::query()->updateOrCreate([
                'id' => $case['id'],
                'name_ar' => $case['name_ar'],
                'name_en' => $case['name_en'],
            ]);

 
    }
}
}
