<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function getClientComments(Request $request){

        $data = [];

        $comments = Comment::with(['user' => function ($q){
        
        }])->where('client_id', $request->client_id)->get();

        return $comments;

    }

    public function store(Request $request){

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'comment' => 'required|string' 
        ]);

        Comment::create([
            'user_id' => $request->user_id,
            'client_id' => $request->client_id,
            'comment' => $request->comment,
        ]);

        return sendResponse(trans('messages.success'));

        
    }
}
