<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('passenger_id')->constrained()->onDelete('cascade');
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->foreignId('booked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('seat_number')->nullable();
            $table->enum('class', ['economy', 'business', 'first'])->default('economy');
            $table->dateTime('booking_date');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['confirmed', 'pending', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};