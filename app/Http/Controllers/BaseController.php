<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FFMpeg;
use Str;
use Storage;
use File;
class BaseController extends Controller
{
    const FFMPEG 	    = '/vendor/php-ffmpeg/php-ffmpeg/src/FFMpeg/Driver/mac/ffmpeg';
    const FFPROBE 	    = '/vendor/php-ffmpeg/php-ffmpeg/src/FFMpeg/Driver/mac/ffprobe';
    const ALLOW_EXT		= [	"docx", "doc" ,"xlsx", "xls" , "pdf" , "epub" , "png" ,  "jpeg" ,  "jpg" ,  "tiff" ,  "tif" , "mp4", "wav", "mp3" ];
    const IMAGE_EXT     = ["png", "jpeg", "jpg", "tiff", "tif"];
    const VIDEO_EXT     = ["mp4"];
    const AUDIO_EXT     = ["mp4", "wav", "mp3"];
    const DOC_EXT 	    = ["docx", "doc", "xlsx", "xls", "pdf", "epub"];
    const EXCEL 	 	= [ "vnd.ms-excel" , "vnd.openxmlformats-officedocument.spreadsheetml.sheet" ];
    const MAX_SIZE		= 1024 * 1024 * 1024;
    const UPLOAD_PATH	= '/public/upload';

    public function uploadBase64File($file , $folder , $width = null , $height = null, $attachment_name = null){

		if( !preg_match("/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).base64,.*/", $file) ){
	        return array('check' => false , 'msg' => "File base64 incorrect");
		}

		$fileBase64        	= substr($file, strpos($file, ",")+1) ;
	    $image              = base64_decode( $fileBase64 );
	    $extension          = explode('/', mime_content_type($file))[1];
	    if(in_array($extension, self::EXCEL)){
	    	$extension = "xlsx";
	    }

        // For Attachment Name
	    if (isset($attachment_name) && !is_null($attachment_name)){
            // $safeName = $attachment_name.'.'.$extension;
            $safeName = Str::uuid().'.'.$extension;
        }else{
            $safeName = Str::uuid().'.'.$extension;
        }

	    Storage::disk('temp')->put($safeName, $image );

	    if(!in_array($extension, self::ALLOW_EXT)){
	    	Storage::disk('temp')->delete($safeName);
	        return array('check' => false , 'msg' => "Unknown File Type ".$extension );
	    }

	    if( File::size(Storage::disk('temp')->path($safeName))  > self::MAX_SIZE ){
	    	Storage::disk('temp')->delete($safeName);
	        return array( 'check' => false , 'msg' => 'Max file size is '.self::MAX_SIZE.' MB');
	    }

		Storage::put($folder.'/'.$safeName, Storage::disk('temp')->get($safeName) );
		Storage::disk('temp')->delete($safeName);

		return array( 'check' => true , 'url' => Storage::url($folder.'/'.$safeName) , 'ext' =>$extension, 'filename' => $attachment_name, 'extension' => $extension );

	}
    
  	public function uploadBase64Image($file, $folder, $width = null, $height = null, $attachment_name = null)
    {
		if( !preg_match("/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).base64,.*/", $file)){
	        return array('check' => false , 'msg' => "File base64 incorrect");
		}

		$imageBase64 = substr($file, strpos($file, ",")+1) ;
	    $image       = base64_decode( $imageBase64 );
	    $extension   = explode('/', mime_content_type($file))[1];
	    $safeName    = Str::uuid().'.'.$extension;

	    // For Attachment Name
        if (isset($attachment_name) && !is_null($attachment_name)){
            $safeName = $attachment_name.'.'.$extension;
        }else{
            $safeName = Str::uuid().'.'.$extension;
        }

	    Storage::disk('temp')->put($safeName, $image);

	    if(!in_array($extension, self::IMAGE_EXT)){
	    	Storage::disk('temp')->delete($safeName);
	        return array('check' => false, 'msg' => "Unknown File Type");
	    }

	    if(File::size(Storage::disk('temp')->path($safeName))  > self::MAX_SIZE){
	    	Storage::disk('temp')->delete($safeName);
	        return array('check' => false, 'msg' => 'Max file size is '.self::MAX_SIZE.' MB');
	    }

		Storage::put($folder.'/'.$safeName, Storage::disk('temp')->get($safeName));
		Storage::disk('temp')->delete($safeName);

		return array('check' => true, 'url' => Storage::url($folder.'/'.$safeName), 'filename' => $safeName, 'extension' => $extension);
	}

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
