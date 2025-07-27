<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('fileID'); // Auto-incrementing primary key
            $table->integer('fileCode'); // Integer for file code
            $table->integer('activityCode'); // Integer for activity code
            $table->integer('subActivityCode'); // Integer for sub activity code
            $table->integer('functionCode'); // Integer for function code
            $table->string('fileName'); // Varchar for file name
            $table->text('fileDescription')->nullable(); // Nullable text for file description
            $table->string('fileLocation')->nullable(); // Nullable string for file location
            $table->string('fileLevel'); // Varchar for file level
            $table->string('fileStatus'); // Varchar for file status
            $table->string('borrowStatus')->default('Tersedia'); // Default to "Tersedia"
            $table->timestamps(); // Timestamps for created_at and updated_at

            // Composite primary key
            $table->primary(['fileCode', 'activityCode', 'subActivityCode', 'functionCode']);

            // Foreign key constraints
            $table->foreign('activityCode')->references('activityCode')->on('activities');
            $table->foreign('subActivityCode')->references('subActivityCode')->on('sub_activities');
            $table->foreign('functionCode')->references('functionCode')->on('functions');
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
}