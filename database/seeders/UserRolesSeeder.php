<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $key => $user) {
            if ($key % 2 == 0) {
                $user->assignRole('agent');
            } else {
                $user->assignRole('employee');
            }
        }
    }
}
