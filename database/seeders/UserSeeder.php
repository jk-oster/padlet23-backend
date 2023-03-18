<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addSpecialUsers();

        \App\Models\User::factory(10)->create();
        \App\Models\User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
            'role' => 'user',
        ]);

    }

    private function addSpecialUsers()
    {
        $adminUsers = [
            [
                'name' => 'smdm-jakob',
                'email' => 'jo@studiomitte.com',
                'password' => bcrypt('2Nv3wmh8v^oVHh'),
                'role' => 'admin',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ],
        ];

        foreach ($adminUsers as $user) {
            $newUser = User::create($user);
            $newUser->save();
        }
    }
}
