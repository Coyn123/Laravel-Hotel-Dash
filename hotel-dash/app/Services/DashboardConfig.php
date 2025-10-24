<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
/*

EXAMPLE OUTPUT FOR CONFIG:
[
    'properties' => [
        [
            'property_id'   => 1,
            'property_name' => 'Property A',
            'floors' => [
                [
                    'id'            => 10,
                    'property_id'   => 1,
                    'floor_num'     => 1,
                    'name'          => 'Floor 1',
                    'start'         => 101,
                    'end'           => 120,
                    'property_name' => 'Property A',
                    'rooms'         => [
                        [
                            'id'          => 1001,
                            'property_id' => 1,
                            'floor'       => 10,
                            'room'        => 101,
                            'room_type'   => 2,
                            'room_status' => 1,
                        ],
                        // ...
                    ],
                    'total_rooms'   => 20,
                ],
                // more floors...
            ],
        ],
        // more properties...
    ]
];
*/

class DashboardConfig
{
    public static function get()
    {
        if (! Session::has('dashboard_config')) {
            Session::put('dashboard_config', [
                'properties' => self::getFloorsWithRooms(),
            ]);
    }

    return Session::get('dashboard_config');
}

    protected static function getFloorsWithRooms()
    {
        // Properties
        $properties = DB::table('properties_config')
        ->select('id','property_name')
        ->get()
        ->map(fn($row) => (array) $row)
        ->all();

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
        //Lookup map
        $propertyLookup = collect($properties)->keyBy('id');

        //Group rooms by floor
        $roomsByFloor = collect($rooms)->groupBy('floor');

        foreach ($floors as $i => $floor) {
            $floors[$i]['property_name'] = $propertyLookup[$floor['property_id']]['property_name'] ?? null;
            $floorRooms = $roomsByFloor->get($floor['id'], collect());
            $floors[$i]['rooms'] = $floorRooms->all();
            $floors[$i]['total_rooms'] = $floorRooms->count();
        }

        //Group by property
        $grouped = collect($properties)->map(function ($property) use ($floors, $roomsByFloor) {
        $propertyFloors = collect($floors)
            ->where('property_id', $property['id'])
            ->map(function ($floor) use ($roomsByFloor, $property) {
                $floor['property_name'] = $property['property_name'];
                $floorRooms = $roomsByFloor->get($floor['id'], collect());
                $floor['rooms'] = $floorRooms->all();
                $floor['total_rooms'] = $floorRooms->count();
                return $floor;
            })
            ->values()
            ->all();
            
            return [
                'property_id'   => $property['id'],
                'property_name' => $property['property_name'],
                'floors'        => $propertyFloors, // always present, even if empty
            ];
        });
        return $grouped->values()->all();
    }
    public static function resolveRoomNumberByID($roomId): ?int
    {
        if(!$roomId) {
            return null;
        }
        $config = self::get();
    
        // room_id in messages_on_board should match rooms_config.id
        $room = collect($config['rooms'] ?? [])->firstWhere('id', $roomId);
        return $room['room'] ?? null;
    }
     
}
