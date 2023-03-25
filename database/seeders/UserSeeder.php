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
            'avatar' => 'https://i.pravatar.cc/150?u=test2@example.com',
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
                'avatar' => 'https://i.pravatar.cc/150?u=anonymous@anonymous.xyz',
                'role' => 'user',
            ],
            [
                'name' => 'smdm-jakob',
                'email' => 'jo@studiomitte.com',
                'password' => bcrypt('2Nv3wmh8v^oVHh'),
                'avatar' => 'https://i.pravatar.cc/150?u=1010118',
                'role' => 'admin',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'avatar' => 'https://i.pravatar.cc/150?u=test@example.com',
                'role' => 'admin',
            ],
        ];

        foreach ($adminUsers as $user) {
            $newUser = User::create($user);
            $newUser->save();
        }
    }
}
