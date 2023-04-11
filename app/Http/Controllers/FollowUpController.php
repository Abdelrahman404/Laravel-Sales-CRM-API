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
        
        if(isset($request->rows)){ $this->rows = $request->rows;}

        if(isset($request->status)){ $this->status = $request->status;}
  
        if(isset($request->word)){ $this->word = $request->word;}

        $clients = Client::where('name','like',"%{$this->word}%")
                        ->where('active', true)
                        ->where('status', $this->status)
                        ->with(['country', 'city', 'area'])
                        ->withCount('calls')
                        ->withSum('deals', 'amount')
                        ->paginate($this->rows);

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
