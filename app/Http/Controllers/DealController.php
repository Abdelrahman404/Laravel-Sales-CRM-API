<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Deal;
use Illuminate\Http\Request;
use Validator;

class DealController extends BaseController
{
    public function store(Request $request){

        $validator =  Validator::make($request->all(), [
          
            'client_id' => 'required|exists:clients,id',
            'products' => 'required|array|min:1',
            'products.*' => 'required|integer|exists:products,id',
            'amount' => 'required|between:0,999.999',
            
        ]);

        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }

        // Getting client country that deal has been done at.
        $client = Client::find($request->client_id);
        $country_id = $client->country->id;

        $deal = Deal::create([
            'user_id' => auth()->user()->id,
            'client_id' => $request->client_id,
            'amount' => $request->amount,
            'country_id' => $country_id
        ]);

        $deal->products()->attach($request->products);

        return $this->sendResponse(trans('messages.success'));
    }
}
