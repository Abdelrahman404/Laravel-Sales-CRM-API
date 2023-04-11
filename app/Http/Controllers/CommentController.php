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
            'client_id' => 'required|exists:clients,id',
            'comment' => 'required|string' 
        ]);
        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }


        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'client_id' => $request->client_id,
            'comment' => $request->comment,
        ]);
        
        $comment->created_by = auth()->user()->name;

        return $this->sendResponse($comment);

        
    }
}
