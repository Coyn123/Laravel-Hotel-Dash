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

        $floors = DB::table('floors_config')
            ->select('id', 'property_id', 'floor_num', 'floor_range_bot as start', 'floor_range_top as end')
            ->get()
            ->map(fn($row) => (array) $row)
            ->all();
    
        $rooms = DB::table('rooms_config')
            ->select('id', 'property_id', 'room_type', 'room', 'room_status', 'floor')
            ->get()
            ->map(fn($row) => (array) $row)
            ->all();
    
        $roomsByFloor = collect($rooms)->groupBy('floor');
    
        foreach ($floors as $i => $floor) {
            $floorRooms = $roomsByFloor->get($floor['floor_num'], collect());
            $floors[$i]['rooms'] = $floorRooms->all();
            $floors[$i]['total_rooms'] = $floorRooms->count();
        }
    
        return $floors;
    }
    
    
    
    
    /*

    protected static function getNotifications()
    {
        // Example: notifications table
        return DB::table('notifications')
            ->latest()
            ->limit(20)
            ->get()
            ->toArray();
    }

    protected static function getNav()
    {
        // Static nav config
        return [
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Rooms', 'route' => 'rooms.index'],
            ['label' => 'Reports', 'route' => 'reports.index'],
        ];
    }
    */
}