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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('payment_status')->default('Unpaid'); // Unpaid, Partial/DP, Paid
            $table->decimal('deposit', 15, 2)->default(0);
            $table->decimal('late_fee_amount', 15, 2)->default(0);
            $table->boolean('is_returned')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
