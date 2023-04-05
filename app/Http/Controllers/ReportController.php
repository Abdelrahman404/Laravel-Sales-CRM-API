<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function clientReport(Request $request){

        $data = [];

        $client = Client::with('status','country', 'city', 'area')
                        ->withCount('calls')
                        ->findOrFail($request->client_id);

        $data['client'] = $client;

        return sendResponse($data);
    }
}
