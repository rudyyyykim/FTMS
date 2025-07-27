<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunctionsTable extends Migration
{
    public function up()
    {
        Schema::create('functions', function (Blueprint $table) {
            $table->integer('functionCode')->primary();  // Primary key inserted by user
            $table->string('functionName');  // Function name
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('functions');
    }
}