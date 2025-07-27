<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fileRequest', function (Blueprint $table) {
            $table->bigIncrements('requestID')->unsigned();  // Primary key with auto increment
            $table->unsignedBigInteger('fileID');
            $table->unsignedBigInteger('staffID');
            $table->timestamp('requestDate')->useCurrent();  // Default to current timestamp
            $table->timestamps();  // Automatically creates created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('staffID')->references('staffID')->on('staff')->onDelete('cascade');
            $table->foreign('fileID')->references('fileID')->on('files')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fileRequest');
    }
};