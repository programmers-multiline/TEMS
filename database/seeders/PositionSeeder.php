<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $data = [
            [
                'position' => 'HR Manager',
            ],
            [
                'position' => 'HR Manager 2',
            ],
            [
                'position' => 'HR Manager 3',
            ],
        ];

        DB::table('positions')->insert($data);
    }
}
