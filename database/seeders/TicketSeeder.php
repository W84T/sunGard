<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $coupons = Coupon::all();
        $users = User::all();

        for ($i = 0; $i < 20; $i++) {
            Ticket::create([
                'coupon_id' => $coupons->random()->id,
                'created_by' => $users->random()->id,
                'description' => $faker->sentence,
                'status' => $faker->randomElement(['open', 'closed']),
                'priority' => $faker->randomElement(['low', 'medium', 'high']),
                'submitted_to' => $faker->randomElement(['admin', 'customer service manager']),
            ]);
        }
    }
}
