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
    Schema::create('settings', function (Blueprint $table) {

        $table->id();

        $table->string('app_name');

        $table->string('timezone')->default('Asia/Jakarta');

        $table->string('default_country')->nullable();

        $table->timestamps();
    });
}
};
