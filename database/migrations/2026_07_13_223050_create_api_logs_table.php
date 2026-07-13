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
    Schema::create('api_logs', function (Blueprint $table) {

        $table->id();

        $table->string('api_name');

        $table->string('status');

        $table->float('response_time')->nullable();

        $table->timestamps();
    });
}
};
