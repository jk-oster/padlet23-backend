<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    // get ratings by post id
    public function getRatingsByPostId($id)
    {
        $post = Post::findOrFail($id);
        Gate::authorize('view', $post->padlet);
        return response()->json($post->ratings()->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric',
            'post_id' => 'required|numeric',
        ]);

        $post = Post::findOrFail($validated['post_id']);
        $padlet = $post->padlet;
        Gate::authorize('view', $padlet);

        $userId = \App\Models\User::getUserIdOrPublic();

        $rating = Rating::byPostId($validated['post_id'])->first();
        if (!$rating) {
            $rating = new Rating();
            $rating->post_id = $validated['post_id'];
            $rating->user_id = $userId;
        }
        $rating->rating = $validated['rating'];
        $rating->save();
        return response()->json($post, 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rating $rating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ratingId)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric',
        ]);

        $rating = Rating::findOrFail($ratingId);
        $post = $rating->post;
        $padlet = $post->padlet;
        Gate::authorize('view', $padlet);
//        Gate::authorize('edit-delete-rating', $padlet, $rating);
        $userId = \App\Models\User::getUserIdOrPublic();

        $rating->update(['rating' => $validated['rating'], 'user_id' => $userId]);

        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Rating $rating
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($ratingId): \Illuminate\Http\JsonResponse
    {
        $rating = Rating::findOrFail($ratingId);
//        $rating = Rating::byPostId($postId)->first();
        $post = $rating->post;
        $padlet = $post->padlet;
        Gate::authorize('view', $padlet);
//        Gate::authorize('edit-delete-rating', $padlet, $rating);
        $rating->delete();

        return response()->json($post, 200);
    }
}
