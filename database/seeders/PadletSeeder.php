<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PadletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Padlet::factory(10)->create();

        \App\Models\Post::factory(10)->create();

        \Illuminate\Support\Facades\DB::table('padlet_user')->insert([
            'accepted' => true,
            'permission_level' => 1,
            'padlet_id' => 1,
            'user_id' => 2,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        \Illuminate\Support\Facades\DB::table('padlet_user')->insert([
            'accepted' => true,
            'permission_level' => 2,
            'padlet_id' => 1,
            'user_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        \Illuminate\Support\Facades\DB::table('padlet_user')->insert([
            'accepted' => true,
            'permission_level' => 3,
            'padlet_id' => 1,
            'user_id' => 4,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }
}
