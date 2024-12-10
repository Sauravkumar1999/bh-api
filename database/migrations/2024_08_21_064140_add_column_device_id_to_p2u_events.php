<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('p2u_events', 'device_id')) {
            Schema::table('p2u_events', function (Blueprint $table) {
                $table->unsignedBigInteger('device_id')->nullable(); // Set nullable instead of default(null)
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('p2u_events', 'device_id')) {
            Schema::table('p2u_events', function (Blueprint $table) {
                $table->dropColumn('device_id');
            });
        }
    }
};
