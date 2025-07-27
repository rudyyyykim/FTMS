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
        Schema::table('fileRequest', function (Blueprint $table) {
            if (!Schema::hasColumn('fileRequest', 'reserveDate')) {
                $table->date('reserveDate')->nullable()->after('requestDate');
            }
            if (!Schema::hasColumn('fileRequest', 'isReservation')) {
                $table->boolean('isReservation')->default(false)->after('reserveDate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fileRequest', function (Blueprint $table) {
            $table->dropColumn(['reserveDate', 'isReservation']);
        });
    }
};
