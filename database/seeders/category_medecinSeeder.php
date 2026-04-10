<?php

namespace Database\Seeders;

use App\Models\category;
use App\Models\drug;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class category_medecinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $category=[

            ['category_name'=>'injectable'],
            ['category_name'=>'infusion'],
            ['category_name'=>'serums'],
            ['category_name'=>'External use'],
            ['category_name'=>'antiseptics'],
            ['category_name'=>'Streilizers'],
          ];
          category::insert($category);

          $drug=[
            [
                'scientific_name' => 'Norgel',
                'commercial_name' => 'Norgel',
                'category_id'     => 1,
                'manufacturer'    => 'Syria',
                'quantity'        => 123,
                'expiry_date'     => '2026-11-14', 
                'price'           => 129,
            ],
            [
                'scientific_name' => 'Paracetamol',
                'commercial_name' => 'Ali-Pan',
                'category_id'     => 1,
                'manufacturer'    => 'Syria',
                'quantity'        => 50,
                'expiry_date'     => '2026-12-14',
                'price'           => 150,
            ],
          ];
          drug::insert($drug);
    }
}
