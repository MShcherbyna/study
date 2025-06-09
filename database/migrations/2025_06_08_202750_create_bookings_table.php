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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('hotel_id');
            $table->string('guest_name');
            $table->string('hotel_name');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedTinyInteger('guests_count')->default(1);
            $table->enum('booking_status', [
                'pending', 'confirmed', 'cancelled', 'completed'
            ])->default('pending');
            $table->decimal('total_price', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->bigInteger('promo_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
