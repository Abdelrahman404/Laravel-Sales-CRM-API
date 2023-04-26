<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Status;
use Illuminate\Http\Request;

class FollowUpController extends BaseController
{

    public $rows = 15;

    public $word;

    public $status = 1;

    public function index(Request $request){
        
    // Update values based on request
    if ($request->filled('rows')) { $this->rows = $request->input('rows');}
    if ($request->filled('active')) {$this->status = $request->input('active');}
    if ($request->filled('word')) {$this->word = $request->input('word');}

        $clients = Client::where('name','like',"%{$this->word}%")
                        ->where('active', true)
                        ->where('status', $this->status)
                        ->with(['country', 'city', 'area', 'calls'])
                        ->withCount('calls')
                        ->withSum('deals', 'amount')
                        ->latest()
                        ->paginate($this->rows);

        $cases = Status::all();

        $casesCollection = collect();

        foreach($cases as $case){
            
            $count = Client::where('status', $case->id)->whereActive(true)->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
            
        $data = [];
        $data['total'] = $clients->count();
        $data['clients']  = $clients;
        $data['cases_count'] = $casesCollection;

        return $this->sendResponse($data);
        
    }
}
