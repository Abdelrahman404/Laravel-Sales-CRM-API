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
  
    // Update values based on request
    if ($request->filled('rows')) { $this->rows = $request->input('rows');}
    if ($request->filled('active')) {$this->active = $request->input('active');}
    if ($request->filled('word')) {$this->word = $request->input('word');}

        $clients = Client::where('name','like',"%{$this->word}%")
                        ->where('active', $this->active)
                        ->with('country', 'city', 'area')
                        ->latest()
                        ->paginate($this->rows);

        $data = [];

        $data['clients'] = $clients;

        $data['total'] = Client::whereActive(true)->count();

        $data['total_followup'] = Client::whereActive(true)->where('status', '!=', 0)->count(); 

        return $this->sendResponse($data);
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
        
        $calls = Call::with('possibilityOfReply')->where('client_id', $request->id)->get();

        $products = Product::all();

        $countries = Country::all();

        $data['client'] = $client;
        $data['calls'] = $calls; 
        $data['products'] = $products;
        $data['cases_count'] = $this->casesCount();
        $data['countries'] = $countries;

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
            'company_level' => $request->company_level,
            'company_size' => $request->company_size,
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => auth()->user()->name
        ]);

        $client = Client::find($request->id);

        $client->products()->sync($request->products_interest);


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
        $user = Client::findOrFail($request->id);

        $user->active = !$user->active;
        
        $user->save();
        
        return $this->sendResponse('success', trans('messages.success'));
        
    }

    public function deletedClients(){

        $data = [];

        $clients = Client::whereActive(false)
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

    public function newClients(){

        $clients = Client::where('status', 0)->get();

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

    public function sendWhatsAppMessage(Request $request){

        $validator =  Validator::make($request->all(), [
            'number' => 'required',
            'message' => 'required|string'
        ]);

        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }


        $phone= $request->number;
        $message = $request->message;
        $data['message'] = $message;
        $data['phone'] = $phone;

        $postData = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $data['phone'],
            "text" => [
                "body" => $data['message'],
                "preview_url" => true

            ]
        ];
   
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.facebook.com/v15.0/118751447862561/messages',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer EAALBzPfuiIIBAHRbjl0HZAjlUPSHH56jeZA1zs1deDvOo1r4ThwEmiOvvJq8bjksVc1acEnPRsmOTVuThDBLS4f9lcLjyvmmTrhzChjiLz8m1obBuQxIFZAlZAqZA4ZA65w80RHCnhdixbQWBtKBZA6vAP6X1xbxQzgDuXWKO1GTHYcPFq1bcE5GgsaVeKOxjp59w7TY1ZCEnA7yqW0tTS52B5fP2s1GuhEZD',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        echo $response;

        return $this->sendResponse(trans('messages.success'));
    }
}
