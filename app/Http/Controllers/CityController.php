<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends BaseController
{
    
    public function cities(Request $request){

        $data = [];

        $cities = City::where('country_id', $request->id)->get();

        return $this->sendResponse($cities);
    }
}
