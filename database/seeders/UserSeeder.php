<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create a Marketer
        $marketer = User::create([
            'name' => 'Marketer',
            'email' => 'marketer@sungard.com',
            'password' => Hash::make('password'),
        ]);
        $marketer->assignRole('marketer');

        // Create Agents
        $branches = \App\Models\Branch::all();
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'Agent ' . $i,
                'email' => 'agent' . $i . '@sungard.com',
                'password' => Hash::make('password'),
                'created_by' => $marketer->id,
                'branch_id' => $branches->random()->id,
            ])->assignRole('agent');
        }

        // Create Employees
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@sungard.com',
                'password' => Hash::make('password'),
            ])->assignRole('customer service');
        }

        // Create an Observer
        User::create([
            'name' => 'Observer',
            'email' => 'observer@sungard.com',
            'password' => Hash::make('password'),
        ])->assignRole('observer');
    }
}
