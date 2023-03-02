<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin Role
        if (!User::where("email", "adam2802002@gmail.com")->first()) {
            User::create([
                "username" => "Adam Abd",
                "email" => "adam2802002@gmail.com",
                "email_verified_at" => Carbon::now(),
                "password" => bcrypt("abcd1234"),
                "avatar" => "",
                "roles" => "admin",
            ]);
        }

        // User Role
        if (!User::where("email", "adamabd14113@gmail.com")->first()) {
            User::create([
                "username" => "Mada Abd",
                "email" => "adamabd14113@gmail.com",
                "email_verified_at" => Carbon::now(),
                "password" => bcrypt("abcd1234"),
                "avatar" => "https://fastly.picsum.photos/id/1072/600/600.jpg?hmac=a8schvBOVW0iuGzhxL-jKpwj2pc4pC4fJtctuxbGoUw",
            ]);
        }
    }
}
