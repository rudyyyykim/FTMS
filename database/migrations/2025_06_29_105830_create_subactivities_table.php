<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_activities', function (Blueprint $table) {
            $table->integer('subActivityCode');
            $table->integer('activityCode');
            $table->integer('functionCode');
            $table->string('subActivityName');
            $table->primary(['subActivityCode', 'activityCode', 'functionCode']);  // Composite primary key
            $table->foreign('activityCode')->references('activityCode')->on('activities');  // Foreign key constraint
            $table->foreign('functionCode')->references('functionCode')->on('functions');  // Foreign key constraint
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_activities');
    }
}