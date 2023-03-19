<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Padlet;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getPostsByPadletId(int $padletId)
    {
        $padlet = Padlet::findOrFail($padletId);
        Gate::authorize('view', $padlet);
        $posts = Post::byPadletId($padletId)->get();
        return response()->json($posts, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'padlet_id' => 'required',
            'cover' => 'nullable',
        ]);

        $user = auth()->user();
        $userId = $user ? $user->id : \App\Models\User::PUBLIC_USER_ID;

        $padlet = Padlet::findOrFail($request->padlet_id);
        Gate::authorize('edit', $padlet);

        $post = new Post();
        $post->user_id = $userId;
        $post->padlet_id = $request->padlet_id;
        $post->content = $request->content;
        $post->cover = $request->cover;
        $post->save();

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        Gate::authorize('view', $post->padlet);
        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'cover' => 'nullable',
            'public' => 'nullable|boolean',
        ]);

        $post = Post::findOrFail($id);
        Gate::authorize('edit', $post->padlet);
        $post->update($request->all());
        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        Gate::authorize('admin', $post->padlet);
        if ($post) {
            $post->delete();
            return response()->json('post (' . $id . ') successfully deleted', 200);
        }
    }

    // search function which searches the post by user and text
    public function search(string $padletId, string $search)
    {
        $posts = Post::byPadletId($padletId)
            ->where('content', 'like', '%' . $search . '%')
            ->get();
        return response()->json($posts, 200);
    }
}
