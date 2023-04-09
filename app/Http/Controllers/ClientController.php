<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientFormRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Call;
use App\Models\Client;
use App\Models\Status;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $data = [];

        $clients = Client::where('active', true)
                        ->with('country', 'city', 'area')
                        ->paginate(15);

        $cases = Status::all();

        $casesCollection = collect();

        foreach($cases as $case){
            
            $count = Client::where('status', $case->id)->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }

        $data['cases_count'] = $casesCollection;
        $data['clients']  = $clients;
 
        return sendResponse($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'note' => $request->note,
            'created_by' => auth()->user()->name
        ]);

        $client->products()->attach($request->products_interest);
        
        return sendResponse('success', trans('messages.added_successfully'));
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

        $client = Client::with('country', 'city', 'area', 'products')->findOrFail($request->id);
        
        /*
             TO DO Many to many relationship Client-Product
        */

        $calls = Call::where('client_id', $request->id)->get();

        $data['client'] = $client;
        $data['calls'] = $calls; 
        $data['cases_count'] = $this->casesCount();

        return sendResponse($data);
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
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => auth()->user()->name
        ]);

        return sendResponse('success', trans('messages.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user  = Client::findOrFail($request->id);
        $user->active = false;
        $user->save();

        return sendResponse('success', trans('messages.removed_successfully'));
    }

    public function deletedClients(){

        $data = [];

        $clients = Client::where('active', false)
                ->with('country', 'city', 'area')
                ->paginate(15);
        
        $data['clients'] = $clients;

        return sendResponse($data);

    }

    public function allClients(){

        $data = [];

        $clients = Client::select('id', 'name')->get();
    
        return sendResponse($clients);
    }
}
