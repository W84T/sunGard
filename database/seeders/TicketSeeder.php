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
        $customerServiceManager = User::whereHas('roles', fn($q) => $q->where('name', 'customer service manager'))->first();

        for ($i = 0; $i < 20; $i++) {
            $status = $faker->randomElement(['open', 'closed']);
            $closedBy = null;
            $closedAt = null;

            if ($status === 'closed') {
                $closedBy = $customerServiceManager->id;
                $closedAt = now();
            }

            Ticket::create([
                'coupon_id' => $coupons->random()->id,
                'created_by' => $users->random()->id,
                'title' => $faker->sentence,
                'description' => $faker->sentence,
                'status' => $status,
                'priority' => $faker->randomElement(['low', 'medium', 'high']),
                'submitted_to' => 'customer service manager',
                'closed_by' => $closedBy,
                'closed_at' => $closedAt,
            ]);
        }
    }
}
