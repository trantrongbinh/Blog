<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    //
    protected $fillable = [
        'name_topic', 
        'slug_topic',
    ];

    public function Post(){
    	return $this->hasMany('App\Models\Post', 'post_id');
    }
}
