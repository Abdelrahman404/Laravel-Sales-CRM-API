<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends BaseController
{
    
    public function areas(Request $request){

        $data = [];

        $areas = Area::where('city_id', $request->id)->get();

        $data['areas'] = $areas;

        return $this->sendResponse($data);
    }
}
