<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- Properties ---
        Schema::create('properties_config', function (Blueprint $table) {
            $table->id();
            $table->string('property_name', 100);
            $table->string('property_address', 255);
            $table->timestamps();
        });

        // --- Floors ---
        Schema::create('floors_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties_config')->cascadeOnDelete();
            $table->unsignedInteger('floor_number');
            $table->unsignedInteger('range_start')->nullable();
            $table->unsignedInteger('range_end')->nullable();
            $table->timestamps();
        });

        // --- Room Status Lookup ---
        Schema::create('room_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 50); // e.g. Available, Occupied, Maintenance
        });

        // --- Room Types Lookup ---
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 50); // e.g. Single, Double, Suite
        });

        // --- Rooms ---
        Schema::create('rooms_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties_config')->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained('floors_config')->cascadeOnDelete();
            $table->unsignedInteger('room_number');
            $table->foreignId('room_type_id')->constrained('room_types');
            $table->foreignId('room_status_id')->constrained('room_statuses');
            $table->timestamps();
        });

        // --- Auxiliary Facilities ---
        Schema::create('aux_property_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties_config')->cascadeOnDelete();
            $table->string('aux_name', 100); // e.g. Pool, Gym
            $table->string('aux_type', 50);  // could also be normalized if needed
            $table->string('aux_status', 50); // Active, Inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aux_property_config');
        Schema::dropIfExists('rooms_config');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('room_statuses');
        Schema::dropIfExists('floors_config');
        Schema::dropIfExists('properties_config');
    }
};
