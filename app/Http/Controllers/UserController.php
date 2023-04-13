<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserController extends BaseController
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

        $users = User::where('name_'.app()->getLocale(),'like',"%{$this->word}%")
                ->where('status',$this->active )->latest()->paginate($this->rows);
        
        $users->makeVisible(['name_en', 'name_ar']);

        return $this->sendResponse($users , trans('messages.success'));
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

        if($request->exists('image') && $request->image != null){
            $file = $this->uploadBase64File($request->image,'public/images');
            $fileName = $file['url'];
        }else{
            $fileName = '/storage/images/avatar.png';
        }
        $user = User::create([
                'email' => $data['email'],
                'name_en'=> $data['name_en'],
                'name_ar'=> $data['name_ar'],
                'image' => $fileName,
                'password' => bcrypt($data['password']),
                'type' => $data['type'],
        ]);

        // If user is not admin (seller)
        if($request->exists('details')){

            foreach($data['details'] as $detail)

            $info = UserInfo::create([
                'user_id' => $user->id,
                'country_id' => $detail['country_id'],
                'comission' => $detail['comission'],
                'target' => $detail['target'],
        ]);

      
    }
        return $this->sendResponse($user, trans('messages.added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = User::with('userInfo')->findOrFail($request->id);

        $user->makeVisible('name_ar', 'name_en', 'created_at');

        $user->api_token = substr($request->header('authorization'), 7);

        return $this->sendResponse($user);
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

       if($request->exists('image') && $request->image != null ){
            $file = $this->uploadBase64File($request->image,'public/images');
            $fileName = $file['url'];
        }else{
            $fileName = User::find($data['id'])->image;
        }
        

        $user = User::where('id', $data['id'])->update([
                'email' => $data['email'],
                'name_en'=> $data['name_en'],
                'name_ar'=> $data['name_ar'],
                'image' => $fileName,
                'password' => bcrypt($data['password']),
                'type' => $data['type'],
        ]);

        // If user is not admin (seller)
        if($request->exists('details')){

            foreach($data['details'] as $detail){

                if(array_key_exists("id", $detail)){
                    
                    UserInfo::where('id', $detail['id'])->update([
                        'country_id' => $detail['country_id'],
                        'comission' => $detail['comission'],
                        'target' => $detail['target'],
                    ]);
            }else{

                    UserInfo::create([
                        'user_id' => $data['id'],
                        'country_id' => $detail['country_id'],
                        'comission' => $detail['comission'],
                        'target' => $detail['target'],
                    ]);

            }

       }

    }
        
      
        return $user = User::with('UserInfo')->find($data['id']);

        return $this->sendResponse($user, trans('messages.updated_successfully'));
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

        return $this->sendResponse('success', trans('messages.success'));
    }

    public function deletedUsers(){

        $data = [];

        $users = User::where('status', 0)->paginate(10);

        $data['users'] = $users;

        return $this->sendResponse($users , trans('messages.success'));        
    }

    public function allsellers(){

        $data = [];

        $sellers = User::where('type', 'seller')->whereHas('userInfo')->latest()->get()->makeHidden(['image', 'type' ,'email']);

        $data['sellers'] = $sellers;

        return $this->sendResponse($data);
    }

    public function getMyProfile(Request $request){

        $data = [];
        
        $user = User::with('userInfo')->findOrFail(auth()->user()->id);

        $user->makeVisible('name_ar', 'name_en', 'created_at');

        $user->api_token = substr($request->header('authorization'), 7);


       return $this->sendResponse($user);
    }

    
}
