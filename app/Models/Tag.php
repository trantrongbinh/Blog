<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    // 
    protected $fillable = [
        'tag', 
        'slug_tag', 
    ];

    public $timestamps = true;

    public function Post(){
      return $this->belongsToMany('App\Models\Post', 'post_tag', 'post_id', 'tag_id');
    }
}
