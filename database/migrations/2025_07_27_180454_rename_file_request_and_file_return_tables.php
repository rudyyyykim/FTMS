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
        // Rename fileRequest table to filerequest
        Schema::rename('fileRequest', 'filerequest');
        
        // Rename fileReturn table to filereturn
        Schema::rename('fileReturn', 'filereturn');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the renaming
        Schema::rename('filerequest', 'fileRequest');
        Schema::rename('filereturn', 'fileReturn');
    }
};
