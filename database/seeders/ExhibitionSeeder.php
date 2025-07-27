<?php

namespace Database\Seeders;

use App\Models\Exhibition;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::where('email', 'superadmin@sungard.com')->first();

        $exhibitions = [
            ['name' => 'Riyadh Motor Show', 'created_by' => $superAdmin->id],
            ['name' => 'Jeddah International Motor Show', 'created_by' => $superAdmin->id],
            ['name' => 'Saudi International Motor Show (Dammam)', 'created_by' => $superAdmin->id],
        ];

        foreach ($exhibitions as $exhibition) {
            Exhibition::create($exhibition);
        }
    }
}
