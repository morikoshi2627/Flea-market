<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = ['Rolax', '西芝', 'Starbacks','なし'];

        foreach ($brands as $brand) {
            Brand::create(['name' => $brand]);
        }
    }
}
