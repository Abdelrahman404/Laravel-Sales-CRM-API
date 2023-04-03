<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientFormRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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

        Client::create([
            'date' => $time,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'google_map' => $request->google_map,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'products_interest' => $request->products_interest,
            'company_level' => $request->company_level,
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => auth()->user()->name
        ]);

        return sendResponse('success', trans('messages.added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            'date' => $time,
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
    public function destroy($id)
    {
        //
    }
}
