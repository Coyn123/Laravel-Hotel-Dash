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
                        [
                            'id'          => 1002,
                            'property_id' => 1,
                            'floor'       => 10,
                            'room'        => 102,
                            'room_type'   => 1,
                            'room_status' => 2,
                        ],
                        // more rooms...
                    ],
                    'total_rooms'   => 20,
                ],
                [
                    'id'            => 11,
                    'property_id'   => 1,
                    'floor_num'     => 2,
                    'name'          => 'Floor 2',
                    'start'         => 201,
                    'end'           => 220,
                    'property_name' => 'Property A',
                    'rooms'         => [
                        // rooms for floor 2...
                    ],
                    'total_rooms'   => 20,
                ],
            ],
        ],
        [
            'property_id'   => 2,
            'property_name' => 'Property B',
            'floors' => [
                [
                    'id'            => 20,
                    'property_id'   => 2,
                    'floor_num'     => 1,
                    'name'          => 'Floor 1',
                    'start'         => 101,
                    'end'           => 110,
                    'property_name' => 'Property B',
                    'rooms'         => [
                        // rooms for Property B, Floor 1...
                    ],
                    'total_rooms'   => 10,
                ],
            ],
        ],
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
    // Main properties
    $properties = DB::table('properties_config')
        ->select('id', 'property_name')
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
            'room_number as room'
        )
        ->get()
        ->map(fn($row) => (array) $row)
        ->all();

    // Aux Properties (always have property_id foreign key)
    $auxProperties = DB::table('aux_property_config')
        ->select('id', 'property_id', 'aux_name', 'aux_type')
        ->get()
        ->map(fn($row) => (array) $row)
        ->all();

    // Lookup tables
    $propertyLookup = collect($properties)->keyBy('id');
    $roomsByFloor = collect($rooms)->groupBy('floor');
    $auxByParent = collect($auxProperties)->groupBy('property_id');

    // Populate floors with room data
    foreach ($floors as $i => $floor) {
        $floors[$i]['property_name'] = $propertyLookup[$floor['property_id']]['property_name'] ?? null;
        $floorRooms = $roomsByFloor->get($floor['id'], collect());
        $floors[$i]['rooms'] = $floorRooms->all();
        $floors[$i]['total_rooms'] = $floorRooms->count();
    }

    // Group properties, attach floors and aux properties
    $grouped = collect($properties)->map(function ($property) use ($floors, $roomsByFloor, $auxByParent) {
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
            'property_id'    => $property['id'],
            'property_name'  => $property['property_name'],
            'floors'         => $propertyFloors,
            'aux_properties' => $auxByParent->get($property['id'], collect())->values()->all(), // attach aux props by foreign key
        ];
    });

    return $grouped->values()->all();
}

}

