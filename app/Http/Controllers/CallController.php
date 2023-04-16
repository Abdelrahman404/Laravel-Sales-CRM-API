<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class CallController extends BaseController
{
    public function getClientCalls(Request $request){

            $calls = Call::where('client_id',$request->client_id)->get();
            
            $data['calls'] = $calls;

            $this->sendResponse($data);
    }

    public function store(Request $request){

        $validator =  Validator::make($request->all(), [
            'date' => 'required|date',
            'hour' => 'required|string',
            'possibility_reply_id' => 'required',
            'duration' => [
                Rule::requiredIf(function() use ($request){
                    return in_array($request->possibility_reply_id, [1, 5]);
                })
            ],
            'client_id' => 'required|exists:clients,id'
        ]);

        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }

        $time = strtotime($request->date);

        $newformat = date('Y-m-d',$time);

        $call = Call::create([
            'date' =>  $newformat,
            'client_id' => $request->client_id,
            'hour' => $request->hour,
            'duration' => $request->duration,
            'possibility_reply_id' => $request->possibility_reply_id,
            'created_by' => auth()->user()->name,
        ]);

        return $this->sendResponse($call);
        

    }
}
