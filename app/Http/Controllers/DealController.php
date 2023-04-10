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
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|string',
        ]);
        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }

        // Getting client country that deal has been done at.
        $client = Client::find($request->client_id);
        $country_id = $client->country->id;

        Deal::create([
            'user_id' => $request->user_id,
            'client_id' => $request->client_id,
            'product_id' => $request->product_id,
            'amount' => $request->amount,
            'country_id' => $country_id
        ]);

        return $this->sendResponse(trans('messages.success'));
    }
}
