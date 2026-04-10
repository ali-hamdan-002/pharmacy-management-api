<?php

namespace Database\Seeders;

use App\Models\category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
       // category::truncate();

       $categories = [
            'Oral',
            'Injectable',
            'Infusion',
            'Serums',
            'External use',
            'Antiseptics',
            'Sterilizers'
        ];

         foreach ($categories as $cat) {
            Category::create([
                'category_name' => $cat 
            ]);
        }


    }
}
