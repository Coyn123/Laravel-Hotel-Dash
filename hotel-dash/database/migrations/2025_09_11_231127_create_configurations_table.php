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
            $table->string('status_name', 50); 
        });
            DB::table('room_statuses')->insert([
            ['id' => 1, 'status_name' => 'Available'],
            ['id' => 2, 'status_name' => 'Occupied'],
            ['id' => 3, 'status_name' => 'Maintenance'],
            ['id' => 4, 'status_name' => 'Out Of Order'],
        ]);


        // --- Room Types Lookup ---
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 50);
        });
        DB::table('room_types')->insert([
            ['id' => 1, 'type_name' => 'Standard'],
            ['id' => 2, 'type_name' => 'Suite'],
        ]);
        Schema::create('room_flags', function (Blueprint $table) {
            $table->id();
            $table->string('flag_name', 50); 
        });
        DB::table('room_flags')->insert([
            ['id' => 1, 'flag_name' => 'Out Of Order'],
            ['id' => 2, 'flag_name' => 'Vacant / Available'],
            ['id' => 3, 'flag_name' => 'Vacant / Dirty'],
            ['id' => 4, 'flag_name' => 'Occupied'],
        ]);

        // --- Rooms ---
        Schema::create('rooms_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties_config')->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained('floors_config')->cascadeOnDelete();
            $table->unsignedInteger('room_number');
            $table->foreignId('room_type_id')->constrained('room_types');
            $table->foreignId('room_status_id')->constrained('room_flags');
            $table->timestamps();
        });

        // --- Auxiliary Facilities ---
        Schema::create('aux_property_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties_config')->cascadeOnDelete();
            $table->string('aux_name', 100);
            $table->string('aux_type', 50);  // could also be normalized if needed
            $table->string('aux_status', 50);
            $table->timestamps();
        });
            Schema::create('message_board', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('floor_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('flag_id')->nullable();
            $table->text('message_text');
            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('properties_config')->cascadeOnDelete();
            $table->foreign('floor_id')->references('id')->on('floors_config')->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('rooms_config')->cascadeOnDelete();
            $table->foreign('flag_id')->references('id')->on('message_flags')->nullOnDelete();

            
            Schema::create('message_flags', function (Blueprint $table) {
            $table->id();
            $table->string('flag_name', 50);
        });
            DB::table('message_flags')->insert([
            ['id' => 1, 'flag_name' => 'Message'],
            ['id' => 2, 'flag_name' => 'Work Request'],
            ['id' => 3, 'flag_name' => 'Urgent'],
            ['id' => 4, 'flag_name' => 'Resolved'],
        ]);

            // Optional index to speed up lookups by room
            $table->index(['property_id', 'floor_id', 'room_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aux_property_config');
        Schema::dropIfExists('rooms_config');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('room_flags');
        Schema::dropIfExists('floors_config');
        Schema::dropIfExists('properties_config');
    }
};
