<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\ {
    Http\Controllers\Controller,
    Http\Requests\CommentRequest,
    Repositories\CommentRepository,
    Models\Post,
    Models\Comment
};

class CommentController extends Controller
{
    /**
     * Create a new CommentController instance.
     *
     * @param  \App\Repositories\CommentRepository $commentRepository
     */
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;

        $this->middleware('auth')->only('store', 'destroy', 'update');
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param  \App\http\requests\CommentRequest $request
     * @param  \App\Models\Post  $post
     * @param  integer $comment_id
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request, Post $post, $comment_id = null)
    {
        Comment::create ([
            'content_cmt' => $request->input('message' . $comment_id),
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'parent_id' => $comment_id,
        ]);

        return back();
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \App\Http\requests\CommentRequest $request
     * @param  \App\Models\Comment  $comment
     * @return array
     */
    public function update(Request $request, $comment)
    {
        // $this->authorize('manage', $comment);
        Comment::where('id', $comment)->update(['content_cmt'=> $request->content_cmt]);

        return response ()->json($request);
    }

    /**
     * Remove the specified comment from storage.
     * @param Comment $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('manage', $comment);

        $comment->delete();

        return back();
    }

    /**
     * Get the next comments for the specified post.
     *
     * @param  \App\Models\Post  $post
     * @param  integer $page
     * @return array
     */
    public function comments(Post $post, $page)
    {
        $comments = $this->commentRepository->getNextComments($post, $page);
        $count = $post->parentComments()->count();
        $level = 0;

        return [
            'html' => view('front/comments/comments', compact('post', 'comments', 'level'))->render(),
            'href' => $count <= config('app.numberParentComments') * ++$page ?
                'none'
                : route('posts.comments', [$post->id, $page]),
        ];
    }
}
