<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 50; $i++) {
            $string = Str::random(10);
            $user = User::create([
                'name' => $string,
                'email' => $string . '@gmail.com',
                'password' => Hash::make('password'),
                'api_token' => Str::random(16)
            ]);

            for ($j = 0; $j < 5; $j++) {
                Todo::create([
                    'name' => $string . ' task',
                    'url'   => '#',
                    'day' => array_rand([0, 1, 2, 3, 4, 5, 6]),
                    'uid' => $user->id
                ]);
            }
        }
    }
}
