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
        // Drop the existing foreign key constraint on filereturn table
        Schema::table('filereturn', function (Blueprint $table) {
            $table->dropForeign(['requestID']);
        });
        
        // Recreate the foreign key constraint with the new table name
        Schema::table('filereturn', function (Blueprint $table) {
            $table->foreign('requestID')->references('requestID')->on('filerequest')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint
        Schema::table('filereturn', function (Blueprint $table) {
            $table->dropForeign(['requestID']);
        });
        
        // Recreate the original foreign key constraint
        Schema::table('filereturn', function (Blueprint $table) {
            $table->foreign('requestID')->references('requestID')->on('filerequest')->onDelete('cascade');
        });
    }
};
