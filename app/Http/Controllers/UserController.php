<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];

        $users = User::paginate(10);

        $data['users'] = $users;

        return response()->json($users);
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
    public function store(CreateUserRequest $request)
    {
       
        $data = $request->only(['name_en', 'name_ar', 'password', 'type', 'details', 'email']);

     
        $user = User::create([
                'email' => $data['email'],
                'name_en'=> $data['name_en'],
                'name_ar'=> $data['name_ar'],
                'password' => $data['password'],
                'type' => $data['type'],
        ]);

        // If user is not admin (seller)
        if(count($data['details']) > 0){

            foreach($data['details'] as $detail)

            $info = UserInfo::create([
                'user_id' => $user->id,
                'country_id' => $detail['country_id'],
                'comission' => $detail['comission'],
                'target' => $detail['target'],
        ]);


    
        return sendResponse($user, trans('messages.added_successfully'));
    }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = [];

        $user = User::with('userInfo')->findOrFail($id);

        $data['user'] = $user;

        return response()->json($data);
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
    public function update(Request $request, $id)
    {
        //
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
