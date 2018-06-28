<?php

namespace App\Http\Controllers;

use \App\User;
use Illuminate\Http\Request;
use \App\Events\Message;


class MessageController extends Controller
{
   	public function save(Request $request){
   		// echo json_encode($request->input());exit;
   		$user = User::where('unique_token', $request->uniqueId)->first();
   		if($user){
	   		$message = $user->sendMessages()->create([
	            'message' => $request->message,
	            'receiver_id'=>$request->receiverId,
	            'sender_id'  => $user->id,
	        ]);
	   		if($message){
            	broadcast(new Message($message));
	      		$response = ['isSuccess'=>true, 'data'=>$message];
	   		}
	      	else 
	      		$response = ['isSuccess'=>false, 'data'=>'Error'];
   		} else 
	      	$response = ['isSuccess'=>false, 'data'=>'Error'];

	    echo json_encode($response);
   	}

}
