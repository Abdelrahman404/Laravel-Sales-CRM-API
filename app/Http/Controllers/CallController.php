<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function getClientCalls(Request $request){

            $calls = Call::where('client_id',$request->client_id)->get();
            
            $data['calls'] = $calls;

            return sendResponse($data);
    }

    public function store(Request $request){


        return 'To be done!';
    }
}
