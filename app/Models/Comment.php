<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $fillable = [
        'user_id', 
        'post_id', 
        'content_cmt', 
        'parent',
        'like', 
        'dislike',
        'status',
        'created_at', 
        'updated_at',
    ];

    public function Post(){
    	return $this->belongsTo('App\Models\Comment', 'post_id');
    }

    public function User(){
    	return $this->belongsTo('App\Models\Comment', 'user_id');
    }
}
