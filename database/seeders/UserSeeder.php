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
        $branches = \App\Models\Branch::all();
        $exhibitions = \App\Models\Exhibition::all();
        $sungardBranches = \App\Models\SungardBranches::all();

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@sungard.com',
            'password' => Hash::make('password'),
            'branch_id' => $branches->random()->id,
            'exhibition_id' => $exhibitions->random()->id,
            'sungard_branch_id' => $sungardBranches->random()->id,
        ]);
        $superAdmin->assignRole('super_admin');

        $marketer = User::create([
            'name' => 'Marketer',
            'email' => 'marketer@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
            'branch_id' => $branches->random()->id,
            'exhibition_id' => $exhibitions->random()->id,
            'sungard_branch_id' => $sungardBranches->random()->id,
        ]);
        $marketer->assignRole('marketer');

        $customerServiceManager = User::create([
            'name' => 'Customer Service Manager',
            'email' => 'customerservicemanager@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
            'branch_id' => $branches->random()->id,
            'exhibition_id' => $exhibitions->random()->id,
            'sungard_branch_id' => $sungardBranches->random()->id,
        ]);
        $customerServiceManager->assignRole('customer service manager');

        User::create([
            'name' => 'Report Manager',
            'email' => 'reportmanager@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
            'branch_id' => $branches->random()->id,
            'exhibition_id' => $exhibitions->random()->id,
            'sungard_branch_id' => $sungardBranches->random()->id,
        ])->assignRole('report manager');

        User::create([
            'name' => 'Sungard Manager',
            'email' => 'sungardmanager@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
            'branch_id' => $branches->random()->id,
            'exhibition_id' => $exhibitions->random()->id,
            'sungard_branch_id' => $sungardBranches->random()->id,
        ])->assignRole('branch manager');

        User::create([
            'name' => 'Customer Service',
            'email' => 'customerservice@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $customerServiceManager->id,
            'branch_id' => $branches->random()->id,
            'exhibition_id' => $exhibitions->random()->id,
            'sungard_branch_id' => $sungardBranches->random()->id,
        ])->assignRole('customer service');

        User::create([
            'name' => 'Agent',
            'email' => 'agent@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $marketer->id,
            'branch_id' => $branches->random()->id,
            'exhibition_id' => $exhibitions->random()->id,
            'sungard_branch_id' => $sungardBranches->random()->id,
        ])->assignRole('agent');
    }
}
