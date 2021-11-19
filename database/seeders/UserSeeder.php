<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'mahdiodesk2015@gmail.com',
            'user_name' => 'admin2021',
            'user_role' => 'admin',
            'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ5JaGZEmPu4AUFMf6LWB5YDwiIGmRgk2oIOcu4EZV7c1w4TvOXkVt7nk8keeuWrYYZ1UU&usqp=CAU',
            'password' => Hash::make('123456'),
            'is_registered' => 'Yes'
        ]);
    }
}
