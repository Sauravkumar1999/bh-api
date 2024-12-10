<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('p2u_events', function (Blueprint $table) {
            DB::statement('ALTER TABLE p2u_events MODIFY device_id VARCHAR(255)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p2u_events', function (Blueprint $table) {
            DB::statement('ALTER TABLE p2u_events MODIFY device_id INT');
        });
    }
};
