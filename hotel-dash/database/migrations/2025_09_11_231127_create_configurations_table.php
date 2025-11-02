<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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


        // --- Rooms ---
        Schema::create('rooms_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties_config')->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained('floors_config')->cascadeOnDelete();
            $table->unsignedInteger('room_number');
            $table->timestamps();
        });

        // --- Auxiliary Facilities ---
        Schema::create('aux_property_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties_config')->cascadeOnDelete();
            $table->string('aux_name', 100);
            $table->string('aux_type', 50);
            $table->string('aux_status', 50)->nullable();
            $table->timestamps();
        });

        // --- Message Flags ---
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

        Schema::create('messages_on_board', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('floor_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->enum('board_type', ['property_board', 'room_board']);
            $table->foreignId('flag_id')->nullable()->constrained('message_flags')->nullOnDelete();
            $table->boolean('completed')->nullable();
            $table->text('message_text');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties_config')->cascadeOnDelete();
            $table->foreign('floor_id')->references('id')->on('floors_config')->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('rooms_config')->cascadeOnDelete();

            $table->index(['property_id', 'floor_id', 'room_id']);
        });

        Schema::create('message_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages_on_board')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->foreignId('flag_id')->nullable()->constrained('message_flags')->nullOnDelete();
        
            $table->unique(['message_id', 'user_id']);
            $table->index('user_id');
        });


        Schema::create('aux_property_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aux_id')->constrained('aux_property_config');
            $table->json('aux_log');
            $table->timestamps();
        });  
    }

    public function down(): void
    {
        Schema::dropIfExists('messages_on_board');
        Schema::dropIfExists('message_flags');
        Schema::dropIfExists('aux_property_config');
        Schema::dropIfExists('rooms_config');
        Schema::dropIfExists('floors_config');
        Schema::dropIfExists('properties_config');
        Schema::dropIfExists('message_notifications');
        Schema::dropIfExists('aux_property_logs');
    }
};
