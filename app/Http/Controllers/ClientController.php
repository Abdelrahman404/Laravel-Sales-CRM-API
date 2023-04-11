<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientFormRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Call;
use App\Models\Client;
use App\Models\Country;
use App\Models\Product;
use App\Models\Status;
use Illuminate\Http\Request;
use Validator;

class ClientController extends BaseController
{

    public $rows = 15;

    public $active = 1;

    public $word;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
  
      if(isset($request->rows)){ $this->rows = $request->rows;}

      if(isset($request->active)){ $this->active = $request->active;}

      if(isset($request->word)){ $this->word = $request->word;}

        $clients = Client::where('name','like',"%{$this->word}%")
                        ->where('active', $this->active)
                        ->with('country', 'city', 'area')
                        ->latest()
                        ->paginate($this->rows);
 
        return $this->sendResponse($clients);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];

        $data['countries'] = Country::all();

        $data['products'] = Product::all();

        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateClientFormRequest $request)
    {

        $time = strtotime($request->date);

        $newformat = date('Y-m-d',$time);

       $client = Client::create([
            'status' => 0, // brand new client
            'date' => $newformat,
            'phone' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'google_map' => $request->google_map,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'company_level' => $request->company_level,
            'company_size' => $request->company_size,
            'note' => $request->note,
            'created_by' => auth()->user()->name
        ]);

        $client->products()->attach($request->products_interest);
        
        return $this->sendResponse('success', trans('messages.added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = [];

        $client = Client::with('country', 'city', 'area', 'products','calls', 'comments', 'products')->findOrFail($request->id);
        
        $calls = Call::where('client_id', $request->id)->get();

        $data['client'] = $client;
        $data['calls'] = $calls; 
        $data['cases_count'] = $this->casesCount();

        return $this->sendResponse($data);
    }

    public function casesCount(){

        $cases = Status::all();

        $casesCollection = collect();

        foreach($cases as $case){
            
            $count = Client::where('status', $case->id)->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }

        return $casesCollection;
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request)
    {
        $time = strtotime($request->date);

        $newformat = date('Y-m-d',$time);

        Client::where('id', $request->id)->update([
            'date' => $newformat,
            'phone' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'google_map' => $request->google_map,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'products_interest' => json_encode($request->products_interest),
            'company_level' => $request->company_level,
            'company_size' => $request->company_size,
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => auth()->user()->name
        ]);

        return $this->sendResponse('success', trans('messages.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Request $request)
    {
        $user  = Client::findOrFail($request->id);
        
        if ($user->active == 0) {
            $user->active = 1;
        } else if ($user->active == 1) {
            $user->active = 0;
        }

        $user->save();

        return $this->sendResponse('success', trans('messages.success'));
    }

    public function deletedClients(){

        $data = [];

        $clients = Client::where('active', false)
                ->with('country', 'city', 'area')
                ->paginate(15);
        
        $data['clients'] = $clients;

        return $this->sendResponse($data);

    }

    public function allClients(){

        $data = [];

        $clients = Client::select('id', 'name', 'phone', 'email')->get();

        $cases = Status::all();

        $casesCollection = collect();

        foreach($cases as $case){
            
            $count = Client::where('status', $case->id)->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }

        $data['cases'] = $casesCollection;

        $data['clients'] = $clients;

        return $this->sendResponse($data);
    }

    public function updateStatus(Request $request){

        $validator =  Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|integer|between:1,7'
        ]);

        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }

        Client::where('id', $request->client_id)->update([
            'status' => $request->status
        ]);

        return $this->sendResponse(trans('messages.success'));
    }
}
