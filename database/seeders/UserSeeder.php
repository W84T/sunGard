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
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@sungard.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('super_admin');

        $marketer = User::create([
            'name' => 'Marketer',
            'email' => 'marketer@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
        ]);
        $marketer->assignRole('marketer');

        $customerServiceManager = User::create([
            'name' => 'Customer Service Manager',
            'email' => 'customerservicemanager@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
        ]);
        $customerServiceManager->assignRole('customer service manager');

        User::create([
            'name' => 'Report Manager',
            'email' => 'reportmanager@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
        ])->assignRole('report manager');

        User::create([
            'name' => 'Sungard Manager',
            'email' => 'sungardmanager@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $superAdmin->id,
        ])->assignRole('branch manager');

        User::create([
            'name' => 'Customer Service',
            'email' => 'customerservice@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $customerServiceManager->id,
        ])->assignRole('customer service');

        User::create([
            'name' => 'Agent',
            'email' => 'agent@sungard.com',
            'password' => Hash::make('password'),
            'created_by' => $marketer->id,
        ])->assignRole('agent');
    }
}