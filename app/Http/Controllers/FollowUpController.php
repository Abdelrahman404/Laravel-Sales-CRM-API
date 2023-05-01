<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PossibilityOfReply;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FollowUpController extends BaseController
{

    public $rows = 15;

    public $word;

    public $status = 1;

    public $from;

    public $to;

    public $call_response_type_id;

    public $seller_id;

    public function index(Request $request){
    
    // Update values based on request
    if ($request->filled('rows')) { $this->rows = $request->input('rows');}
    if ($request->filled('word')) {$this->word = $request->input('word');}
    if ($request->filled('status')) {$this->status = $request->input('status');}
    if ($request->filled('from')) {$this->from = $request->input('from');}
    if ($request->filled('to')) {$this->to = $request->input('to');}
    if ($request->filled('call_response_type_id')) {$this->call_response_type_id = $request->input('call_response_type_id');}
    if ($request->filled('seller_id')) {$this->seller_id = $request->input('seller_id');}
    
        $clients = Client::where('active', true)
                        ->where('status', $this->status)
                        ->with(['country', 'city', 'area', 'calls', 'seller'])
                        ->withCount('calls')
                        ->withSum('deals', 'amount');
            
        $clients = $request->word ? $clients->where('name', 'LIKE', "%{$request->word}%"): $clients;

        $clients = $request->from ? $clients->where('created_at', '>=', Carbon::parse($request->from)): $clients;

        $clients = $request->to ? $clients->where('created_at', '<=', Carbon::parse($request->to)): $clients;

        $clients = ($request->from && $request->to) ? $clients->whereBetween('created_at', [Carbon::parse($request->from), Carbon::parse($request->to)]) : $clients;
    
        // Filter by seller and result will by clients added by this seller
       if($request->filled('seller_id')){

        $user = User::find($request->seller_id);

        $clients = $clients->where('created_by', $user->name_ar)->orWhere('created_by', $user->name_ar);

   }
   
        // Get clients which thier last call status matches given request status
        $clients = $request->call_response_type_id ? $clients->whereHas('calls', function($query) use ($request){
    
            $query->where('possibility_reply_id', $request->call_response_type_id)
                ->where('created_at', function ($subQuery) {
                    $subQuery->selectRaw('MAX(created_at)')
                            ->from('calls')
                            ->whereColumn('client_id', 'clients.id');
                });
        }) : $clients;
          
        $clients = $clients->latest()->paginate($this->rows);

        // Adding last call status for each client حالة الرد
        $clients->getCollection()->map(function ($client) {

            // Check if client has calls before already
            if ($client->calls->count() > 0){

                $client['last_call_status'] = $client->calls->sortByDesc('created_at')->first()->possibilityOfReply->name;

            }else{
                $client['last_call_status'] = null;
            }
        
              return $client;
        });

        $cases = Status::all();

        // Client cases collection (Tabs)
        $casesCollection = collect();

        foreach($cases as $case){
            
            $count = Client::where('status', $case->id)->whereActive(true)->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
            
        $data = [];
        $data['total'] = Client::whereActive(true)->where('status', '!=', 0)->count();
        $data['clients']  = $clients;
        $data['cases_count'] = $casesCollection;

        return $this->sendResponse($data);
        
    }

    public function filter(){

        $sellers = User::whereStatus(true)->whereType('seller')->get();

        $callResponseTypes = PossibilityOfReply::all();

        $data['sellers'] = $sellers;

        $data['call_response_types'] = $callResponseTypes;

        return $this->sendResponse($data);

    }

 
}
