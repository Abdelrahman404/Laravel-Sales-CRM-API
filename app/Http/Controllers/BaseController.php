<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    function sendResponse($result, $message = 'success')
    {
    	$response = [
            'check' => true,
            'data'  => $result,
            'msg'   => $message,
        ];

        return response()->json($response, 200);
    }

     function sendError($error, $errorMessages = [], $code = 200)
    {
    	$response = [
            'check' => false,
            'msg' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

     function uploadFile($image){
        $filename = time() . '.' . $image->extension();

        $image->move(public_path('images'), $filename);
        
        return $filename;

    }
}
