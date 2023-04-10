<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Status;
use Illuminate\Http\Request;

class FollowUpController extends BaseController
{
    public function index(Request $request){
        
        // If not status sent from client default will be 1 (Lead & عميل محتمل)
        $status = ($request->status) ? $request->status : 1;

        $clients = Client::where('active', true)
                        ->where('status', $status)
                        ->with(['country', 'city', 'area'])
                        ->withCount('calls')
                        ->paginate(15);

        $cases = Status::all();

        $casesCollection = collect();

        foreach($cases as $case){
            
            $count = Client::where('status', $case->id)->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }

        $data = [];
        $data['clients']  = $clients;
        $data['cases_count'] = $casesCollection;

        return $this->sendResponse($data);
        
    }
}
