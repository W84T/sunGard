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
        $exhibitions = \App\Models\Exhibition::all();
        $sungardBranches = \App\Models\SungardBranches::all();

        for ($i = 1; $i <= 10; $i++) { // Increased agents
            User::create([
                'name' => 'Agent ' . $i,
                'email' => 'agent' . $i . '@sungard.com',
                'password' => Hash::make('password'),
                'created_by' => $marketer->id,
                'branch_id' => $branches->random()->id,
                'exhibition_id' => $exhibitions->random()->id,
            ])->assignRole('agent');
        }

        // Create Employees
        for ($i = 1; $i <= 5; $i++) { // Increased employees
            User::create([
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@sungard.com',
                'password' => Hash::make('password'),
                'branch_id' => $branches->random()->id,
                'exhibition_id' => $exhibitions->random()->id,
            ])->assignRole('customer service');
        }

        // Create Branch Managers
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'name' => 'Branch Manager ' . $i,
                'email' => 'branchmanager' . $i . '@sungard.com',
                'password' => Hash::make('password'),
                'sungard_branch_id' => $sungardBranches->random()->id,
            ])->assignRole('branch manager');
        }

        // Create an Observer
        User::create([
            'name' => 'Observer',
            'email' => 'observer@sungard.com',
            'password' => Hash::make('password'),
        ])->assignRole('observer');
    }
}
