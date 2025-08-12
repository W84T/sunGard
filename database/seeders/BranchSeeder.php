<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::where('email', 'superadmin@sungard.com')->first();
        $exhibitions = \App\Models\Exhibition::all();

        $branches = [
            ['name' => 'Riyadh Branch', 'created_by' => $superAdmin->id, 'exhibition_id' => $exhibitions->random()->id],
            ['name' => 'Jeddah Branch', 'created_by' => $superAdmin->id, 'exhibition_id' => $exhibitions->random()->id],
            ['name' => 'Dammam Branch', 'created_by' => $superAdmin->id, 'exhibition_id' => $exhibitions->random()->id],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}