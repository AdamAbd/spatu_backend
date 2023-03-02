<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductColorType;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Product Metcon 8 Superblack
        if (!Product::where("name", "Metcon 8 Superblack")->first()) {
            $product = Product::firstOrCreate([
                "brand_id" => 3,
                "name" => "Metcon 8 Superblack",
                "rating" => 5,
                "reviews_total" => 6828,
                "solds_total" => 12512,
                "description" => "You chase the clock, challenging & encouraging each other all in the name of achieving goals & making gains",
            ]);

            for ($x = 0; $x < 3; $x++) {
                ProductImage::create([
                    "product_id" => $product->id,
                    "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                ]);
            }

            $colorType = array(
                50000 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                0 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            );

            foreach ($colorType as $price => $image) {
                ProductColorType::create([
                    "product_id" => $product->id,
                    "image" => $image,
                    "price" => $price,
                ]);
            }

            $sizes = array(5 => 3000000, 5.5 => 3025000, 6 => 3050000, 7 => 3100000, 8.5 => 3150000,);

            foreach ($sizes as $size => $price) {
                ProductSize::create([
                    "product_id" => $product->id,
                    "size" => $size,
                    "price" => $price,
                ]);
            }
        }

        // Product Air Zoom SuperRep
        if (!Product::where("name", "Air Zoom SuperRep")->first()) {
            $product = Product::firstOrCreate([
                "brand_id" => 3,
                "name" => "Air Zoom SuperRep",
                "rating" => 5,
                "reviews_total" => 10212,
                "solds_total" => 52214,
                "description" => "You chase the clock, challenging & encouraging each other all in the name of achieving goals & making gains",
            ]);

            for ($x = 0; $x < 3; $x++) {
                ProductImage::create([
                    "product_id" => $product->id,
                    "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                ]);
            }

            $colorType = array(
                0 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                0 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            );

            foreach ($colorType as $price => $image) {
                ProductColorType::create([
                    "product_id" => $product->id,
                    "image" => $image,
                    "price" => $price,
                ]);
            }

            $sizes = array(5 => 1000000, 5.5 => 1025000, 6 => 1050000, 6.5 => 1100000, 7 => 1150000, 8 => 1200000, 8.5 => 1250000,);

            foreach ($sizes as $size => $price) {
                ProductSize::create([
                    "product_id" => $product->id,
                    "size" => $size,
                    "price" => $price,
                ]);
            }
        }

        // Product Metcon 7
        if (!Product::where("name", "Metcon 7")->first()) {
            $product = Product::firstOrCreate([
                "brand_id" => 3,
                "name" => "Metcon 7",
                "rating" => 5,
                "reviews_total" => 7342,
                "solds_total" => 15003,
                "description" => "You chase the clock, challenging & encouraging each other all in the name of achieving goals & making gains",
            ]);

            for ($x = 0; $x < 3; $x++) {
                ProductImage::create([
                    "product_id" => $product->id,
                    "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                ]);
            }

            $colorType = array(
                0 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                0 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            );

            foreach ($colorType as $price => $image) {
                ProductColorType::create([
                    "product_id" => $product->id,
                    "image" => $image,
                    "price" => $price,
                ]);
            }

            $sizes = array(5 => 1000000, 5.5 => 1550000, 6 => 1600000, 6.5 => 1750000, 7 => 1800000, 8 => 1900000, 8.5 => 1950000,);

            foreach ($sizes as $size => $price) {
                ProductSize::create([
                    "product_id" => $product->id,
                    "size" => $size,
                    "price" => $price,
                ]);
            }
        }

        // Product Defy All Day
        if (!Product::where("name", "Defy All Day")->first()) {
            $product = Product::firstOrCreate([
                "brand_id" => 3,
                "name" => "Defy All Day",
                "rating" => 5,
                "reviews_total" => 7342,
                "solds_total" => 24412,
                "description" => "You chase the clock, challenging & encouraging each other all in the name of achieving goals & making gains",
            ]);

            for ($x = 0; $x < 3; $x++) {
                ProductImage::create([
                    "product_id" => $product->id,
                    "image" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                ]);
            }

            $colorType = array(
                0 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
                0 => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            );

            foreach ($colorType as $price => $image) {
                ProductColorType::create([
                    "product_id" => $product->id,
                    "image" => $image,
                    "price" => $price,
                ]);
            }

            $sizes = array(5 => 1000000, 5.5 => 1550000, 6 => 1600000, 6.5 => 1750000, 7 => 1800000, 8 => 1900000, 8.5 => 1950000,);

            foreach ($sizes as $size => $price) {
                ProductSize::create([
                    "product_id" => $product->id,
                    "size" => $size,
                    "price" => $price,
                ]);
            }
        }
    }
}
