<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('exhibition_id')
                ->nullable()
                ->constrained('exhibitions')
                ->nullOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('sungard_branch_id')
                ->nullable()
                ->constrained('sungard_branches')
                ->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_email')
                ->nullable()
                ->unique();
            $table->string('customer_phone')
                ->unique();

            $table->string('coupon_link')
                ->nullable();

            $table->string('car_model')
                ->index();
            $table->string('car_brand')
                ->index();
            $table->string('plate_number');
            $table->string('plate_characters');
            $table->string('car_category')
                ->nullable()
                ->index();

            $table->boolean('is_confirmed')
                ->default(false)
                ->index();
            $table->smallInteger('status')
                ->nullable()
                ->index();

            // Composite index for your common queries like WHERE status + is_confirmed
            $table->index(['status', 'is_confirmed']);

            $table->dateTimeTz('reserved_date')
                ->nullable()
                ->index();
            $table->dateTimeTz('reached_at')
                ->nullable();

            $table->softDeletes()
                ->index(); // for faster soft delete queries
            $table->timestampsTz();
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
