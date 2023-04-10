<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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

        $users = User::where('status', 1)->paginate(10);
        
        $users->makeVisible(['name_en', 'name_ar']);

        $data['users'] = $users;

        return sendResponse($users , trans('messages.success'));
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

        if($request->hasFile('image')){
            $imageName = uploadFile($request->file('image'));
        }else{
            $fileName = 'avatar.png';
        }

        $user = User::create([
                'email' => $data['email'],
                'name_en'=> $data['name_en'],
                'name_ar'=> $data['name_ar'],
                'image' => env('APP_URL') . '/storage' . '/' .$fileName,
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
    public function show(Request $request)
    {
        $data = [];
        
        $user = User::with('userInfo')->findOrFail($request->id);

        $user->makeVisible('name_ar', 'name_en', 'created_at');

        $user->api_token = substr($request->header('authorization'), 7);

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
    public function update(UpdateUserRequest $request)
    {
       $data = $request->only(['id', 'name_en', 'name_ar', 'password', 'type', 'details', 'email']);

        if($request->hasFile('image')){
            $fileName = uploadFile($request->file('image'));
            $filePath = env('APP_URL') . '/storage' . '/' .$fileName;
        }else{
            $filePath = User::find($data['id'])->image;
        }

        $user = User::where('id', $data['id'])->update([
                'email' => $data['email'],
                'name_en'=> $data['name_en'],
                'name_ar'=> $data['name_ar'],
                'image' => $filePath,
                'password' => $data['password'],
                'type' => $data['type'],
        ]);

        return sendResponse($user, trans('messages.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Request $request)
    {
        $user  = User::findOrFail($request->id);

        if ($user->status == 0) {
            $user->status = 1;
        } else if ($user->status == 1) {
            $user->status = 0;
        }

        $user->save();

        return sendResponse('success', trans('messages.success'));
    }

    public function deletedUsers(){

        $data = [];

        $users = User::where('status', 0)->paginate(10);

        $data['users'] = $users;

        return sendResponse($users , trans('messages.success'));        
    }

    public function allsellers(){

        $data = [];

        $sellers = User::where('type', 'seller')->get()->makeHidden(['image', 'type' ,'email']);

        $data['sellers'] = $sellers;

        return sendResponse($data);
    }

    
}
