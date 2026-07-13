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
    Schema::create('exchange_rates', function (Blueprint $table) {

        $table->id();

        $table->string('currency',20);

        $table->decimal('exchange_rate',15,4);

        $table->dateTime('updated_at_api')->nullable();

        $table->timestamps();
    });
}
};
