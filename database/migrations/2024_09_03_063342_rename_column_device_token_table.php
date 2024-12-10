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
         // Rename 'device_token' to 'fcm_token'
         DB::statement('ALTER TABLE device_tokens CHANGE COLUMN device_token fcm_token VARCHAR(255) NULL');

         // Rename 'device_id' to 'uuid'
         DB::statement('ALTER TABLE device_tokens CHANGE COLUMN device_id uuid VARCHAR(255) NULL'); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         // Rename 'fcm_token' back to 'device_token'
         DB::statement('ALTER TABLE device_tokens CHANGE COLUMN fcm_token device_token VARCHAR(255) NULL');

         // Rename 'uuid' back to 'device_id'
         DB::statement('ALTER TABLE device_tokens CHANGE COLUMN uuid device_id VARCHAR(255) NULL');
    }
};
