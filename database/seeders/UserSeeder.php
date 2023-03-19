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
            'password' => bcrypt('awdawdawdawdaw'),
            'email' => 'test2@example.com',
            'avatar' => 'https://avatars.githubusercontent.com/u/1010118?v=4',
            'role' => 'user',
        ]);

    }

    private function addSpecialUsers()
    {
        $adminUsers = [
            [
                'id' => \App\Models\User::PUBLIC_USER_ID,
                'name' => 'anonymous',
                'email' => 'anonymous@anonymous.xyz',
                'password' => bcrypt('anonymous'),
                'avatar' => 'https://avatars.githubusercontent.com/u/1010118?v=4',
                'role' => 'user',
            ],
            [
                'name' => 'smdm-jakob',
                'email' => 'jo@studiomitte.com',
                'password' => bcrypt('2Nv3wmh8v^oVHh'),
                'avatar' => 'https://avatars.githubusercontent.com/u/1010118?v=4',
                'role' => 'admin',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'avatar' => 'https://avatars.githubusercontent.com/u/1010118?v=4',
                'role' => 'admin',
            ],
        ];

        foreach ($adminUsers as $user) {
            $newUser = User::create($user);
            $newUser->save();
        }
    }
}
