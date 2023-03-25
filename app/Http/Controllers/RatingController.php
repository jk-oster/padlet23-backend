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
        $request->validate([
            'rating' => 'required',
            'post_id' => 'required',
        ]);

        $padlet = Post::findOrFail($request->post_id)->padlet;
        Gate::authorize('view', $padlet);

        $userId = \App\Models\User::getUserIdOrPublic();

        $rating = Rating::byPostId($request->post_id)->first();
        if (!$rating) {
            $rating = new Rating();
            $rating->post_id = $request->post_id;
            $rating->user_id = $userId;
        }
        $rating->rating = $request->rating;
        $rating->save();
        return response()->json($rating, 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rating $rating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $postId)
    {
        $request->validate([
            'rating' => 'required',
        ]);

        $rating = Rating::byPostId($postId)->first();
        return response()->json($rating, 200);

        if (!$rating) {
            return response()->json(['error' => 'rating not found'], 404);
        }

        $padlet = $rating->post->padlet;
        Gate::authorize('view', $padlet);
//        Gate::authorize('edit-delete-rating', $padlet, $rating);

        $rating->update(['rating' => $request->rating]);

        return response()->json($rating, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Rating $rating
     * @return \Illuminate\Http\Response
     */
    public function destroy($postId)
    {
        $rating = Rating::byPostId($postId)->first();
        $padlet = $rating->post->padlet;
        Gate::authorize('view', $padlet);
//        Gate::authorize('edit-delete-rating', $padlet, $rating);
        $rating->delete();
        return response()->json('rating (' . $postId . ') successfully deleted', 200);
    }
}
