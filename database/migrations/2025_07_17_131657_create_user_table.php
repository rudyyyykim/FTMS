<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->string('userID', 100)->primary();  // Primary key
            $table->string('username', 100)->unique();  // Unique constraint on username
            $table->string('icNumber', 12)->unique();  // Unique constraint on icNumber
            $table->string('email', 100)->unique();  // Unique constraint on email
            $table->string('role', 255);  // Role column
            $table->string('password', 255);  // Password column
            $table->string('userStatus', 100);  // User status column
            $table->timestamps();  // Automatically adds created_at and updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('user');
    }
};