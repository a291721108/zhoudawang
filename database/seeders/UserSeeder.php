<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $characters = ['张', '王', '李', '赵', '刘', '陈', '杨', '黄', '周', '吴'];

        $name = [];
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $randomIndex = array_rand($characters);
                $name[] .= $characters[$randomIndex];
            }
        }

        for ($i = 1; $i < 15; $i++) {

            DB::table('user')->insert([
                'name' => $name[$i],
                'created_at' => time(),
                'updated_at' => time()
            ]);
        }
    }
}
