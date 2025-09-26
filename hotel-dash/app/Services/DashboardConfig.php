<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardConfig
{
    public static function get()
    {
        return Cache::remember('dashboard_config', 3600, function () {
            return [
                'floors' => self::getFloorsWithRooms(),
                //'notifications' => self::getNotifications(),
                //'nav' => self::getNav(),
            ];
        });
    }

    protected static function getFloorsWithRooms()
    {
        // Floors
        $floors = DB::table('floors_config')
            ->select(
                'id',
                'property_id',
                'floor_number as floor_num',
                DB::raw("CONCAT('Floor ', floor_number) as name"),
                'range_start as start',
                'range_end as end'
            )
            ->get()
            ->map(fn($row) => (array) $row)
            ->all();

        // Rooms
        $rooms = DB::table('rooms_config')
            ->select(
                'id',
                'property_id',
                'floor_id as floor',
                'room_number as room',
                'room_type_id as room_type',
                'room_status_id as room_status'
            )
            ->get()
            ->map(fn($row) => (array) $row)
            ->all();

        // Group rooms by floor_id
        $roomsByFloor = collect($rooms)->groupBy('floor');

        // Attach rooms to each floor
        foreach ($floors as $i => $floor) {
            $floorRooms = $roomsByFloor->get($floor['id'], collect());
            $floors[$i]['rooms'] = $floorRooms->all();
            $floors[$i]['total_rooms'] = $floorRooms->count();
        }

        return $floors;
    }
}
