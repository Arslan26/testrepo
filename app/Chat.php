<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    
	protected $fillable = ['message','receiver_id','sender_id', 'file', 'status'];

    public function sender(){
    	return $this->belongsTo('App\User', 'sender_id');
    }
	
    public function receiver(){
    	return $this->belongsTo('App\User', 'receiver_id');
    }


}
