<?php

namespace Database\Seeders;

use App\Models\SungardBranches;
use Illuminate\Database\Seeder;

class SungardBranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = \App\Models\User::where('email', 'superadmin@sungard.com')->first();

        SungardBranches::create(['name' => 'Main Sungard Branch', 'created_by' => $superAdmin->id, 'color' => '#FF0000', 'address' => '123 Main St']);
        SungardBranches::create(['name' => 'Downtown Sungard Branch', 'created_by' => $superAdmin->id, 'color' => '#00FF00', 'address' => '456 Downtown Ave']);
        SungardBranches::create(['name' => 'Uptown Sungard Branch', 'created_by' => $superAdmin->id, 'color' => '#0000FF', 'address' => '789 Uptown Blvd']);
    }
}
