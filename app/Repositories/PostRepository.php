<?php

namespace App\Repositories;

use App\Models\ {
	Post,
    Tag,
    Comment,
    Topic
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
            ->select('id', 'user_id', 'title', 'slug_title', 'description', 'content_post', 'url_img', 'updated_at')
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
     * Get active posts for specified tag.
     *
     * @param  int  $nbrPages
     * @param  int  $tag_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getActiveOrderByDateForTag($nbrPages, $tag_id)
    {
        return $this->queryActiveOrderByDate()
            ->whereHas('tags', function ($q) use ($tag_id) {
                $q->where('tags.id', $tag_id);
            })->paginate($nbrPages);
    }

    /**
     * Get posts with comments page home.
      *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getPost()
    {
        // Post with user, tags and topics
        $posts = $this->model->with([
            'user' => function ($q) {
                $q->select('id', 'name', 'email');
            },
            'tags' => function ($q) {
                $q->select('tags.id', 'tag');
            },
            'topic' => function ($q) {
                $q->select('id', 'name_topic', 'slug_topic');
            },
        ])
        ->with(['parentComments' => function ($q) {
            $q->with('user')
                ->latest()
                ->get();
        }])
        ->withCount('validComments')
        ->withCount('parentComments')
        ->whereActive(true)
        ->latest()
        ->get();
        
        return $posts;
    }

    /**
     * Get post by slug.
     *
     * @param  string  $slug
     * @return array
     */
    public function getPostBySlug($slug)
    {
        // Post for slug with user, tags and categories
        $post = $this->model->with([
            'user' => function ($q) {
                $q->select('id', 'name', 'email');
            },
            'tags' => function ($q) {
                $q->select('tags.id', 'tag');
            },
            'topic' => function ($q) {
                $q->select('name_topic', 'slug_topic');
            }
        ])
        ->with(['parentComments' => function ($q) {
            $q->with('user')
                ->latest()
                ->take(config('app.numberParentComments'));
        }])
        ->withCount('validComments')
        ->withCount('parentComments')
        ->whereSlugTitle($slug)
        ->firstOrFail();

        // Previous post
        // Next post

        return compact('post');
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
