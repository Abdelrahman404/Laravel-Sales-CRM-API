<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();

        Product::create([
            'id' => 1 ,
            'name_ar' => 'قيراط',
            'name_en' => 'Quirat',
            'name' => 'Quirat'
        ]);
        Product::create([
            'id' => 2 ,
            'name_ar' => 'قوائم',
            'name_en' => 'Qwaeem erp',
            'name' => 'Qwaeem erp'
        ]);
        Product::create([
            'id' => 3 ,
            'name_ar' => 'كادر',
            'name_en' => 'Kader',
            'name' => 'Kader'
        ]);
    }
}
