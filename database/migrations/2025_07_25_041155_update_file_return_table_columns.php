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
        Schema::table('fileReturn', function (Blueprint $table) {
            // Rename existing columns to match the model expectations
            $table->renameColumn('lateReturn', 'returnTiming');
            $table->renameColumn('lateReturnDate', 'updatedReturnDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fileReturn', function (Blueprint $table) {
            // Reverse the column name changes
            $table->renameColumn('returnTiming', 'lateReturn');
            $table->renameColumn('updatedReturnDate', 'lateReturnDate');
        });
    }
};
