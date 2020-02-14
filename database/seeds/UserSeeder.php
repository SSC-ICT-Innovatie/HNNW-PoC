<?php

use App\Models\User;

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
        DB::table("users")->delete();

        //
        // Admin account
        //

        $nick = User::create([
            "annotation" => "Dhr.",
            "first_name" => "Nick",
            "last_name" => "Verheijen",
            "email" => "nick.verheijen@minbzk.nl",
            "password" => bcrypt("engeland"),
            "is_admin" => true,
        ]);

        $nick2 = User::create([
            "annotation" => "Dhr.",
            "first_name" => "Nick",
            "last_name" => "Verheijen",
            "email" => "verheijen.webdevelopment@gmail.com",
            "password" => bcrypt("engeland"),
            "is_admin" => true,
        ]);
    }
}
