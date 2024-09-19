<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            ['name' => 'Test User 1', 'email'     => 'test1@gmail.com', 'password'  => bcrypt('abcdefghij')],
            ['name' => 'Test User 2', 'email'     => 'test2@gmail.com', 'password'  => bcrypt('abcdefghij')],
            ['name' => 'Test User 3', 'email'     => 'test3@gmail.com', 'password'  => bcrypt('abcdefghij')],
        ];

        User::insert($usersData);
    }
}
