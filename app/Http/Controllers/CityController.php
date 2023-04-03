<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    
    public function cities(Request $request){

        $data = [];

        $cities = City::where('country_id', $request->id)->get();

        $data['cities'] = $cities;

        return response()->json($data);
    }
}
