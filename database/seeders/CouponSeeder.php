<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Coupon;
use App\Models\Exhibition;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $agents = User::whereHas('roles', fn($q) => $q->where('name', 'agent'))->get();
        $employees = User::whereHas('roles', fn($q) => $q->where('name', 'customer service'))->get();
        $branches = Branch::all();
        $exhibitions = Exhibition::all();
        $sungardBranches = \App\Models\SungardBranches::all();

        for ($i = 0; $i < 100; $i++) {
            $employeeId = null;
            $status = null;

            // 70% of coupons will have employee_id and status as null
            if ($faker->boolean(70)) {
                $employeeId = null;
                $status = null;
            } else {
                $employeeId = $employees->random()->id;
                $status = $faker->numberBetween(1, 5);
            }

            Coupon::create([
                'agent_id' => $agents->first()->id,
                'branch_id' => $branches->random()->id,
                'exhibition_id' => $exhibitions->random()->id,
                'sungard_branch_id' => $sungardBranches->random()->id,
                'employee_id' => $employeeId,
                'customer_name' => $faker->name,
                'customer_email' => $faker->unique()->safeEmail,
                'customer_phone' => $faker->unique()->phoneNumber,
                'car_model' => $faker->word,
                'car_brand' => $faker->word,
                'car_category' => $faker->word,
                'plate_number' => $faker->bothify('####'),
                'plate_characters' => $faker->bothify('???'),
                'is_confirmed' => $faker->boolean,
                'status' => $status,
                'reserved_date' => $faker->dateTimeBetween('-1 month', '+1 month'),
                'reached_at' => $faker->optional()->dateTimeBetween('-1 month', '+1 month'),
            ]);
        }
    }
}