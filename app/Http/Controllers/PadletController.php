<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonRequest;
use App\Models\Padlet;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PadletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth()->user();

        $publicPadlets = Padlet::accessiblePadlets($user)->with(['padletUser', 'user'])->get();

        return response()->json($publicPadlets, 200);
    }

    // get padlet_users by padlet id
    public function getPadletUsersByPadletId(int $padletId)
    {
        $padlet = Padlet::findOrFail($padletId);
        Gate::authorize('view', $padlet);
        $padletUsers = $padlet->padletUser()->get();
        return response()->json($padletUsers, 200);
    }

    public function getSharedPadlets()
    {
        $user = auth()->user();

        $sharedPadlets = Padlet::sharedPadlets($user)->get();

        return response()->json($sharedPadlets, 200);
    }

    public function sharePadlet(Request $request, int $id)
    {
        $padlet = Padlet::findOrFail($id);

        Gate::authorize('admin', $padlet);

        $data = $request->all();
        // [
        //	{
        //		"user_id": 1,
        //		"permission_level": 2
        //	},
        //	{
        //		"user_id": 2,
        //		"permission_level": 3
        //	}
        // ]

        $coloumn = array_column($data, 'permission_level', 'user_id');
        // [
        //	1 => 2,
        //	2 => 3
        // ]

        $mappedSharedUserData = array_map(static function ($value) {
            return ['permission_level' => $value];
        }, $coloumn);
        // [1 => ['permission_level' => 1], 2 => ['permission_level' => 2]];

        $padlet->padletUser()->sync($mappedSharedUserData);

        return response()->json($padlet->padletUser()->get(), 200);
    }

    public function getPrivatePadlets()
    {
        $user = auth()->user();

        $privatePadlets = Padlet::privatePadlets($user)->get();

        return response()->json($privatePadlets, 200);
    }

    public function acceptPadlet(int $id)
    {
        $user = auth()->user();
        $padlet = Padlet::findOrFail($id);
        $padlet->padletUser()->updateExistingPivot($user->id, ['accepted' => true]);
        return response()->json($user->padletUser()->where('padlet_id', $padlet->id)->first(), 200);
    }

    public function declinePadlet(int $id)
    {
        $user = auth()->user();
        $padlet = Padlet::findOrFail($id);
        $padlet->padletUser()->detach($user->id);
        return response()->json($padlet->padletUser()->get(), 200);
    }

    public function getPadletUsers()
    {
        $user = auth()->user();
        $padletUsers = $user->padletUser()->get();
        return response()->json($padletUsers, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'cover' => 'nullable|string',
            'public' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $userId = User::getUserIdOrPublic();
        if (!$user) {
            $validated["public"] = true;
        }

        $padlet = Padlet::create([
            'name' => $validated["name"],
            'cover' => $validated["cover"],
            'user_id' => $userId,
            'public' => $validated["public"] ?? true,
        ]);

        return response()->json($padlet, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show($id)
    {
        $padlet = Padlet::with(['posts', 'padletUser', 'user'])->findOrFail($id);
        Gate::authorize('view', $padlet);
        return response()->json($padlet, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'cover' => 'nullable|string',
            'public' => 'nullable|boolean',
        ]);

        $padlet = Padlet::findOrFail($id);
        Gate::authorize('admin', $padlet);
        $padlet->update($validated);
        return response()->json($padlet, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $padlet = Padlet::findOrFail($id);
        Gate::authorize('admin', $padlet);
        Post::where('padlet_id', $id)->delete();
        $padlet->delete();
        return response()->json('padlet (' . $id . ') successfully deleted', 200);
    }

    // a search function that searches for users and padlet text
    public function search($search)
    {
        $user = auth()->user();
        $padlets = Padlet::accessiblePadlets($user)
            ->where('name', 'like', '%' . $search . '%')
            ->orWhereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->with(['posts', 'padletUser'])
            ->get();

        return response()->json($padlets, 200);
    }

    public function toggle($id)
    {
        $padlet = Padlet::findOrFail($id);
        Gate::authorize('admin', $padlet);
        $padlet->update(['public' => !$padlet->public]);
    }
}
