<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Validator;

class CommentController extends BaseController
{
    public function getClientComments(Request $request){

        $data = [];

        $comments = Comment::with('user')->where('client_id', $request->client_id)->get();

        return $this->sendResponse($comments);

    }

    public function store(Request $request){

        $validator =  Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'comment' => 'required|string' 
        ]);
        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }


        Comment::create([
            'user_id' => $request->user_id,
            'client_id' => $request->client_id,
            'comment' => $request->comment,
        ]);

        return $this->sendResponse(trans('messages.success'));

        
    }
}
