<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Events extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamp('start');
            $table->tinyInteger('drivers_per_heat');
            $table->tinyInteger('heats_per_driver');
            $table->tinyInteger('drivers_per_final');
            $table->tinyInteger('advance_per_final');
            $table->tinyInteger('laps_per_heat');
            $table->tinyInteger('laps_per_final');
            $table->string('car_model');
            $table->boolean('automate')->default(false);
            $table->text('config')->nullable();

            $table->timestamps();
        });

        Schema::create('event_admins', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('user_id');

            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });

        Schema::create('event_signups', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('user_id');

            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('RESTRICT');
        });

        Schema::create('races', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->string('name');
            $table->boolean('heat');
            $table->tinyInteger('session')->nullable();
            $table->boolean('active')->default(false);
            $table->unsignedInteger('server_id')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('CASCADE');
        });

        Schema::create('race_entrants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('race_id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('grid');
            $table->unsignedTinyInteger('position')->nullable();
            $table->unsignedInteger('time')->nullable();
            $table->unsignedTinyInteger('laps')->nullable();
            $table->unsignedInteger('fastest_lap')->nullable();

            $table->foreign('race_id')->references('id')->on('races')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('race_entrants');
        Schema::drop('races');
        Schema::drop('event_signups');
        Schema::drop('events');
    }
}
