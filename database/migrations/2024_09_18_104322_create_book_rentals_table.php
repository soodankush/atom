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
        Schema::create('book_rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('from_date');
            $table->dateTime('till_date');
            $table->dateTime('return_date')->nullable()->comment('Date time at which book is returned');
            $table->enum('status', [1,2])->comment('1: Book is booked, 2: Book has been returned on return date');
            $table->boolean('is_overdue')->comment('1: Book is overdue, 0: Not overdue');
            $table->timestamps();
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_rentals');
    }
};
