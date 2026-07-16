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
        $table->string('country_name');
        $table->string('country_code',10);
        $table->string('capital')->nullable();
        $table->string('region')->nullable();
        $table->bigInteger('population')->nullable();
        $table->string('currency')->nullable();
        $table->string('flag')->nullable();
        $table->timestamps();
    });
}
};
