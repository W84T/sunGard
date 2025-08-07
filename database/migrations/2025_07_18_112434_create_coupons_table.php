<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete(); // the agent who made that record
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('exhibition_id')->nullable()->constrained('exhibitions')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();  // the customer service employee that handled that record
            $table->foreignId('sungard_branch_id')->nullable()->constrained('sungard_branches')->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_email')->nullable()->unique();
            $table->string('customer_phone')->unique();

            $table->string('coupon_link')->nullable();

            $table->string('car_model');
            $table->string('car_brand');
            $table->string('plate_number');
            $table->string('plate_characters');
            $table->string('car_category')->nullable();

            $table->boolean('is_confirmed')->default(false);
            $table->smallInteger('status')->nullable();

            $table->datetime('reserved_date')->nullable();
            $table->datetime('reached_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
