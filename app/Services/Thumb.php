<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use Intervention\Image\Facades\Image;

class Thumb
{
    /**
     * Make thumb image path
     *
     * @param $img
     * @return void
     */
    public static function makeThumbPath($img)
    {
        $url = null;
        if (!is_null($img)) {
            $ext = $img->getClientOriginalExtension();
            if (!checkExtensionImage($ext)) {

                return redirect('back.posts.create')->with('warning', __('Không hỗ trợ định dạng file này!'));
            }
            $url = substr(time() . mt_rand() . '_' . $img->getClientOriginalName(), -190); 
            while (file_exists('upload/posts'  . $url)) {
                $url = substr(time() . mt_rand() . '_' . $img->getClientOriginalName(), -190);
            }
            $img->move('upload/posts', $url);
        }

        return $url;
    }

}
