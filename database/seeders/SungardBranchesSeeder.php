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
        SungardBranches::create(['name' => 'Main Sungard Branch']);
        SungardBranches::create(['name' => 'Downtown Sungard Branch']);
        SungardBranches::create(['name' => 'Uptown Sungard Branch']);
    }
}
