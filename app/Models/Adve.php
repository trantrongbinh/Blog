<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adve extends Model
{
     //
    protected $fillable = [
        'des_adve', 
        'slug_adve',
        'url_adve', 
        'url_img',
    ];

}
