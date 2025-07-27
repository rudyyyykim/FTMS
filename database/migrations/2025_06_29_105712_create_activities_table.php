<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->integer('activityCode');
            $table->integer('functionCode');
            $table->string('activityName');
            $table->primary(['activityCode', 'functionCode']);  // Composite primary key
            $table->foreign('functionCode')->references('functionCode')->on('functions');  // Foreign key constraint
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
