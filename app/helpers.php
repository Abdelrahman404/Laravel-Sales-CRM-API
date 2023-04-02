<?php
     function sendResponse($result, $message = '')
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