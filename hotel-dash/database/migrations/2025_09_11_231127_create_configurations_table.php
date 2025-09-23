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
        Schema::create('properties_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('property_name', 100);
            $table->string('property_address', 100);
            $table->integer('num_of_floors');
            $table->integer('num_of_rooms');
            $table->timestamps();
        });
        Schema::create('floors_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('property_id', 100);
            $table->string('floor_num', 100);
            $table->integer('floor_range_bot');
            $table->integer('floor_range_top');
            $table->timestamps();
        });
        Schema::create('rooms_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('property_id', 100);
            $table->string('room_type', 100);
            $table->integer('room');
            $table->string('room_status', 100);
            $table->timestamps();
        });
        Schema::create('aux_property_conifg', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('property_id');
            $table->string('aux_name', 100);
            $table->integer('aux_type');
            $table->string('aux_status', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
