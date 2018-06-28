<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Validator;

class UserController extends Controller
{
    public function signup(Request $request){
    /*	$validator = Validator::make($request->signup, [
		    'name' => 'required|max:255',
		    'email'=> 'required|unique:users|max:255',
		    'pass' => 'required|max:255|same:signup[conf_pass]',
		])->validate();*/
		$errors = $req_fields = [];
		if(!isset($request->signup) || empty($request->signup))
			$errors['all'] = 'Registration Error. Try Again';

		if(!isset($request->signup['name']) || empty($request->signup['name']))
			array_push($req_fields,'name');

		if(!isset($request->signup['email']) || empty($request->signup['email']))
			array_push($req_fields,'email');

		if(!isset($request->signup['pass']) || empty($request->signup['pass']))
			array_push($req_fields,'pass');

		if(!isset($request->signup['conf_pass']) || empty($request->signup['conf_pass']))
			array_push($req_fields,'conf_pass');

		if($request->signup['pass'] != $request->signup['conf_pass'])
			$errors['conf_pass'] = 'Password do not match';

		if(!empty($errors) || !empty($req_fields))
			echo json_encode(['isSuccess'=>false,'errors'=>['msgs'=>$errors, 'required'=>$req_fields]]);

		$user = new User();
		$user->name = $request->signup['name'];
		$user->email = $request->signup['email'];
		$user->password = $request->signup['pass'];
		$user->status = 'I'; // in active
		$user->save();

		echo json_encode(['isSuccess'=>true, 'data'=>'Account Created Successfully']);
    }

    // login
    public function login(Request $request){
    	if(isset($request->login['email']) && !empty($request->login['email']) && isset($request->login['pass']) && !empty($request->login['pass'])){
    		$user = User::where(['email'=>$request->login['email'], 'password'=>$request->login['pass']])->first();
    		if($user){
    			// generate random string
    			do
		        {
		        	$token = sha1(time());
		            $user_token = User::where('unique_token', $token)->first();
		        }
		        while(!empty($user_token));
		        $user->unique_token = $token;
		        $user->save();

    			$response = ['isSuccess'=>true, 'data'=>['msg'=>'You are successfully logged in','token'=>$token,'user_id'=>$user->id]];
    		}
    		else
    			$response = ['isSuccess'=>false, 'errors'=>'Incorrect Credentials'];
    	}
    	else 
    		$response = ['isSuccess'=>false, 'errors'=>'Incorrect Credentials'];

    	echo json_encode($response);
    }

    // search user
    public function search(Request $request){
    	if(isset($request->str) && !empty($request->str)){
    		$str = $request->str;
    		$user = User::where(function($q) use ($str) {
	          $q->where('name', 'like', '%'.$str.'%')
	            ->orWhere('email','like', '%'.$str.'%');
	      	})->get();
	      	if(count($user))
	      		$response = ['isSuccess'=>true, 'data'=>$user];
	      	else 
	      		$response = ['isSuccess'=>false, 'data'=>'No Friend Found'];
    	} else 
	      	$response = ['isSuccess'=>false, 'data'=>'No Friend Found'];
    	echo json_encode($response);
    }

    public function chats(Request $request){
    	if(isset($request->uniqueId) && !empty($request->uniqueId)){
    		$user = User::where('unique_token', $request->uniqueId)->first();
    		//echo json_encode($user->sendMessages()->groupBy('receiver_id')->get(['receiver_id']));
    		if($user == null) exit;
    		
    		$users = \DB::table('users as u')
	            ->join('chats as c', function($join)
				{
				    $join->on(function($query)
		            {
		            	$query->on('c.sender_id', '=', 'u.id');
		            	$query->orOn('c.receiver_id', '=', 'u.id');
		            });

				})->join('users as f', function($join)
				{
				    $join->on(function($query)
		            {
		            	// $query->on('c.sender_id', '=', 'u.id');
		            	// $query->on('c.receiver_id', '=', 'u.id');
		            	$query->on(function($innerQuery)
			           	{
			            	$innerQuery->on('c.sender_id', '=', 'f.id');
			            	$innerQuery->on('c.receiver_id', '=', 'u.id');
			           	});
					    $query->orOn(function($innerQuery)
			            {
			            	$innerQuery->on('c.sender_id', '=', 'u.id');
			            	$innerQuery->on('c.receiver_id', '=', 'f.id');
			            }); 
		            });
				})
	            ->select('f.name as friend', 'f.photo', 'c.message', 'c.created_at', 'c.id', 'c.sender_id', 'c.receiver_id', 'f.id as friend_id')
	            ->where('u.id', $user->id);

	            if(isset($request->id) && !empty($request->id))
	            	$users = $users->where('f.id',$request->id)->get();
	            else 
	            	$users = $users->groupBy('f.id')->get();

	        if(count($users))
	      		$response = ['isSuccess'=>true, 'data'=>$users];
	      	else 
	      		$response = ['isSuccess'=>false, 'data'=>'No Message'];

            echo json_encode($response);
    	}
    }
}
