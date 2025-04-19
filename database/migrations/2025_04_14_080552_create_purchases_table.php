<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('pay_date')->nullable();
            $table->bigInteger('total_price');
            $table->bigInteger('total_pay');
            $table->bigInteger('total_return');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->integer('poin')->nullable();
            $table->integer('used_point')->nullable();
            $table->timestamps();

            $table->string('member_type')->default('non_member'); // Default 'non_member'
            $table->decimal('payment_amount', 10, 2); // Decimal untuk uang
            $table->decimal('change', 10, 2)->default(0); // Default 0 untuk change

            // Relasi ke member dan user
            $table->foreign('member_id')->references('id')->on('members')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
