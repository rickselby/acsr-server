<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventTweaks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('race_entrants', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::create('points_sequences', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('points', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('points_sequence_id')->index();
            $table->integer('position');
            $table->integer('points');
            $table->timestamps();

            $table->foreign('points_sequence_id')->references('id')->on('points_sequences')
                ->onDelete('CASCADE');
        });

        Schema::table('events', function(Blueprint $table) {
            $table->unsignedInteger('points_sequence_id')->index();
            $table->dateTime('started')->nullable();
            $table->dateTime('finished')->nullable();

            $table->foreign('points_sequence_id')->references('id')->on('points_sequences')
                ->onDelete('RESTRICT');
        });

        Schema::table('races', function (Blueprint $table) {
            $table->boolean('complete')->default(false);
            $table->dropColumn('server_id');
            $table->string('group_id')->default('');
            $table->string('channel_id')->default('');
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->unsignedInteger('race_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn(['race_id']);
        });

        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn(['complete', 'group_id', 'channel_id']);
            $table->unsignedInteger('server_id')->nullable();
        });

        Schema::table('events', function(Blueprint $table) {
            $table->dropForeign(['points_sequence_id']);
            $table->dropColumn(['points_sequence_id', 'started', 'finished']);
        });

        Schema::drop('points');
        Schema::drop('points_sequences');

        Schema::table('race_entrants', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}
