<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fileRequest', function (Blueprint $table) {
            $table->string('reserveStatus')->default('Tiada')->after('requestDate');
            $table->date('reserveDate')->nullable()->after('reserveStatus');
            $table->boolean('isReservation')->default(false)->after('reserveDate');
        });
    }

    public function down()
    {
        Schema::table('fileRequest', function (Blueprint $table) {
            $table->dropColumn(['reserveStatus', 'reserveDate', 'isReservation']);
        });
    }
};
