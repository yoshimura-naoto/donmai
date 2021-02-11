<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'ていと',
            'email' => 'aho@gmail',
            'password' => Hash::make('ahoaho'),
        ]);

        // DB::table('users')->insert([
        //     'name' => 'うんこやろう',
        //     'email' => 'aaa@gmail',
        //     'password' => 'aaaaaa',
        // ]);

        // DB::table('users')->insert([
        //     'name' => 'あほちん',
        //     'email' => 'bbb@gmail',
        //     'password' => 'bbbbbb',
        // ]);

        // DB::table('users')->insert([
        //     'name' => 'カピバラ',
        //     'email' => 'ccc@gmail',
        //     'password' => 'cccccc',
        // ]);


    }
}
