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
        Schema::create('event_names', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
        Schema::create('p2u_events', function (Blueprint $table) {
            $table->id();

            $table->float('p2u_amount')->nullable(true);
            $table->unsignedBigInteger('event_name_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('expires_at')->nullable(true);
            $table->enum('transfer_status',['pending','completed','confirmed','cancel', 'expire'])->default('pending');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('event_name_id')->references('id')->on('event_names');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p2u_events');
        Schema::dropIfExists('event_names');
    }
};
