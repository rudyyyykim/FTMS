<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staffID');
            $table->string('staffName', 255);
            $table->string('staffPosition', 100)->nullable();
            $table->string('staffPhone', 20)->nullable();
            $table->string('staffEmail', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
};