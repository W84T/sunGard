<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Role::where('name', 'agent')->exists()) {
            Role::create(['name' => 'agent']);
        }
        if (! Role::where('name', 'employee')->exists()) {
            Role::create(['name' => 'employee']);
        }
    }
}