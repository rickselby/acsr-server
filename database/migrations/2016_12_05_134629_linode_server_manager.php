<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LinodeServerManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linode_servers', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('linode_id');
            $table->integer('event_id');
            $table->integer('datacenter_id');
            $table->string('ip');
            $table->string('password');

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
        Schema::drop('linode_servers');
    }
}
