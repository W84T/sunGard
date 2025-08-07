<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
            SungardBranchesSeeder::class,
            InitialUserSeeder::class,
            ExhibitionSeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            CouponSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
