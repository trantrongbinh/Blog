<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'name_topic', 
        'slug_topic',
    ];

    /**
	* One to Many relation
	*
	* @return \Illuminate\Database\Eloquent\Relations\HasMany
	*/
    public function posts()
    {
    	return $this->hasMany(Post::class);
    }
}
