<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $seederClasses = [
            PadletSeeder::class,
//            PostSeeder::class,
        ];
        if (App::Environment() == 'local') {
            $seederClasses = [
                UserSeeder::class,
                ...$seederClasses,
            ];
        }

        $this->call($seederClasses);
    }
}
