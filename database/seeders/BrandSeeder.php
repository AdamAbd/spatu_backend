<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Brand Adidas
        Brand::firstOrCreate([
            "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            "name" => "Adidas",
        ]);

        // Brand Puma
        Brand::firstOrCreate([
            "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            "name" => "Puma",
        ]);

        // Brand Nike
        Brand::firstOrCreate([
            "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            "name" => "Nike",
        ]);

        // Brand Reebok
        Brand::firstOrCreate([
            "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            "name" => "Reebok",
        ]);
    }
}
