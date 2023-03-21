<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class CommentController extends Controller
{

    // get comments by post id
    public function getCommentsByPostId($id)
    {
        $post = Post::findOrFail($id);
        Gate::authorize('view', $post->padlet);
        return response()->json($post->comments()->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required',
            'post_id' => 'required',
        ]);

        $padlet = Post::findOrFail($request->post_id)->padlet;
        Gate::authorize('comment', $padlet);

        $userId = \App\Models\User::getUserIdOrPublic();

        $comment = new Comment();
        $comment->text = $request->text;
        $comment->user_id = $userId;
        $comment->post_id = $request->post_id;
        $comment->save();
        return response()->json($comment, 201);
    }

    // search comments of post by text
    public function search($id, $text)
    {
        $post = Post::findOrFail($id);
        Gate::authorize('view', $post->padlet);
        $comments = $post->comments()->where('text', 'like', '%' . $text . '%')->get();
        return response()->json($comments, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'text' => 'required',
        ]);

        $comment = Comment::findOrFail($id);
        $padlet = $comment->post()->padlet();
        Gate::authorize('comment', $padlet);
        Gate::authorize('edit-delete-comment', $padlet, $comment);

        $comment->update(['text' => $request->text]);

        return response()->json($comment, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $padlet = $comment->post()->padlet();
        Gate::authorize('comment', $padlet);
        Gate::authorize('edit-delete-comment', $padlet, $comment);
        $comment->delete();
        return response()->json('comment (' . $id . ') successfully deleted', 200);
    }
}
