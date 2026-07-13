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
    Schema::create('countries', function (Blueprint $table) {
        $table->id();
        $table->string('country_code',10)->unique();
        $table->string('country_name');
        $table->string('capital')->nullable();
        $table->string('region')->nullable();
        $table->string('currency',20)->nullable();
        $table->decimal('latitude',10,6)->nullable();
        $table->decimal('longitude',10,6)->nullable();
        $table->timestamps();
    });
}
};
