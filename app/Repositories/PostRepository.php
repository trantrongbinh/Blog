<?php

namespace App\Repositories;

use App\Models\ {
	Post,
    Tag,
    Comment
};
use App\Services\Thumb;

class PostRepository
{
    /**
     * The Tag instance.
     *
     * @var \App\Models\Tag
     */
    protected $tag;

    /**
     * The Comment instance.
     *
     * @var \App\Models\Comment
     */
    protected $comment;

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    protected $thumb;


    /**
     * Create a new BlogRepository instance.
     *
     * @param  \App\Models\Post $post
     * @param  \App\Models\Tag $tag
     * @param  \App\Models\Comment $comment
     */
    public function __construct(Post $post, Tag $tag, Comment $comment, Thumb $thumb)
    {
        $this->model = $post;
        $this->tag = $tag;
        $this->comment = $comment;
        $this->thumb = $thumb;
    }

    /**
     * Create a query for Post.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function queryActiveOrderByDate()
    {
        return $this->model
            ->select('id', 'user_id', 'title', 'slug_title', 'description', 'url_img', 'updated_at')
            ->whereActive(true)
            ->latest();
    }

    /**
     * Get active posts.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getActiveOrderByDate()
    {
        return $this->queryActiveOrderByDate()->paginate(10);
    }

    /**
     * Store post.
     *
     * @param  \App\Http\Requests\PostRequest  $request
     * @return void
     */
    public function store($request)
    {
    	$url = $this->thumb->makeThumbPath($request->file('img'));
        // $request->merge(['user_id' => auth()->id()]);
        // $request->merge(['active' => $request->has('active')]);
        $request->merge([
            'user_id' => auth()->id(),
            'active' => $request->has('active'),
            'url_img' => $url,
        ]);
        $post = Post::create($request->all());
        $this->saveTags($post, $request);
    }

    /**
     * Update post.
     *
     * @param  \App\Models\Post  $post
     * @param  \App\Http\Requests\PostRequest  $request
     * @return void
     */
    public function update($post, $request)
    {
        $url = $this->thumb->makeThumbPath($request->file('img'));
        if ($url != null) {
            if ( $post->url_img != null && file_exists('upload/posts/' . $post->url_img)) unlink('upload/posts/' . $post->url_img);
            $request->merge(['url_img' => $url]);
        }
        $request->merge(['active' => $request->has('active')]);
        $post->update($request->all());

        $this->saveTags($post, $request);
    }

    /**
     * Save tags.
     *
     * @param  \App\Models\Post  $post
     * @param  \App\Http\Requests\PostRequest  $request
     * @return void
     */
    protected function saveTags($post, $request){
        $tags_id = $request->tag_id;
       
        if ($request->tags) {
            $tags = explode(',', $request->tags);
            foreach ($tags as $tag) {
                $tag_ref = Tag::firstOrCreate(['tag' => $tag, 'slug_tag' => changeTitle($tag)]);
                $tags_id[] = $tag_ref->id;
            }
        }

        $post->tags()->sync($tags_id);
    }
}
