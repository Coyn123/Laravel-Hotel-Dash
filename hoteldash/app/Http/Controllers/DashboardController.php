<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    $configCount = DB::table('configurations')->count();

    if($configCount === 0) {
      return view('setup');

    }

    $floors = DB::table('configurations')->orderBy('floor_number')->get();
    $floors = [
		['id' => 1, 'name' => 'Floor 1', 'start' => 101, 'end' => 114],
		['id' => 2, 'name' => 'Floor 2', 'start' => 201, 'end' => 225],
		['id' => 3, 'name' => 'Floor 3', 'start' => 301, 'end' => 327],
		['id' => 4, 'name' => 'Floor 4', 'start' => 401, 'end' => 427],
    ];
    return view('dashboard', compact('floors'));
  }

  public function storeSetup(Request $request)
  {
    foreach ($request->floors as $floor) {
      DB::table('configurations')->insert([
        'property_name' => $floor['property_name'],
        'floor_name' => $floor['floor_name'],
        'floor_number' => 0,
        'floor_count' => 0,
        'aux_property_count' => 0,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }
    return redirect()->route('dashboard');
  }
}
