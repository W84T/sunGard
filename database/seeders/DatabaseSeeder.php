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
            InitialUserSeeder::class,
//            SungardBranchesSeeder::class,
//            ExhibitionSeeder::class,
//            BranchSeeder::class,
//            UserSeeder::class,
//            CouponSeeder::class,
//            TicketSeeder::class,
        ]);
    }
}
