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
    }
}
