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
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'nullable|string',
            'padlet_id' => 'required|numeric',
            'cover' => 'nullable|string',
        ]);

        $userId = \App\Models\User::getUserIdOrPublic();

        $padlet = Padlet::findOrFail($validated["padlet_id"]);
        Gate::authorize('edit', $padlet);

        $post = new Post();
        $post->user_id = $userId;
        $post->padlet_id = $validated["padlet_id"];
        $post->title = $validated["title"];
        $post->content = $validated["content"];
        $post->cover = $validated["cover"];
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
        $post = Post::with(['comments', 'padlet', 'user'])->findOrFail($id);
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
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'nullable|string',
            'cover' => 'nullable|string',
            'public' => 'nullable|boolean',
        ]);

        $post = Post::findOrFail($id);
        Gate::authorize('edit', $post->padlet);
        $post->update($validated);
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
        Gate::authorize('edit', $post->padlet);
        $post->delete();
        return response()->json('post (' . $id . ') successfully deleted', 200);
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
