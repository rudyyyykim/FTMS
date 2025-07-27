<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fileReturn', function (Blueprint $table) {
            $table->bigIncrements('returnID')->unsigned();  // Primary key with auto increment
            $table->unsignedBigInteger('requestID');  // Foreign key to fileRequest table (NOT NULL)
            $table->string('userID', 100)->nullable();  // Nullable userID
            $table->date('returnDate');  // NOT NULL date field
            $table->string('returnStatus', 50);  // NOT NULL return status field
            $table->string('lateReturn', 30);  // NOT NULL late return field
            $table->date('lateReturnDate')->nullable();  // Nullable late return date
            $table->timestamps();  // This creates created_at and updated_at as timestamp with NULL default
            
            // Foreign key constraint
            $table->foreign('requestID')->references('requestID')->on('fileRequest')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fileReturn');
    }
};