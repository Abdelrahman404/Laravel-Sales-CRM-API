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

        $request->validate([
            'date' => 'required|date',
            'hour' => 'required|string',
            'duration' => 'required|string',
            'possibility_reply_id' => 'required',
            'client_id' => 'required|exists:clients,id'
        ]);

        $time = strtotime($request->date);

        $newformat = date('Y-m-d',$time);

        Call::create([
            'date' =>  $newformat,
            'client_id' => $request->client_id,
            'hour' => $request->hour,
            'duration' => $request->duration,
            'possibility_reply_id' => $request->possibility_reply_id,
            'created_by' => auth()->user()->name,
        ]);

        return sendResponse('success');

    }
}
