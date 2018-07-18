<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = [
        'topic_id', 
        'user_id', 
        'title', 
        'slug_title', 
        'seo_title', 
        'description', 
        'content_post', 
        'meta_des', 
        'meta_keyword', 
        'active', 
        'url_img', 
        'view', 
        'type', 
        'parent', 
        'rate', 
        'created_at', 
        'updated_at',
    ];

    public function Topic(){
        return $this->belongsTo('App\Models\Topic', 'topic_id');
    }

    public function User(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function Comment(){
        return $this->hasMany('App\Models\Comment', 'post_id');
    }

    public function Tag(){
        return $this->belongsToMany('App\Models\Tag', 'post_tag', 'post_id', 'tag_id')->withTimestamps();
    }
}
