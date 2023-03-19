<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonRequest;
use App\Models\Padlet;
use App\Models\User;
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

        $publicPadlets = Padlet::accessiblePadlets($user)->with(['padletUser'])->get();

        return response()->json($publicPadlets, 200);
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
            return [ 'permission_level' => $value];
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'cover' => 'nullable',
            'public' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $userId = $user ? $user->id : User::PUBLIC_USER_ID;
        if (!$user) {
            $request->public = true;
        }

        $padlet = Padlet::create([
            'name' => $request->name,
            'cover' => $request->cover,
            'user_id' => $userId,
            'public' => $request->public ?? true,
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
        $padlet = Padlet::findOrFail($id)->with(['posts', 'padletUser'])->get();
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

        $request->validate([
            'name' => 'required',
            'cover' => 'nullable',
            'public' => 'nullable|boolean',
        ]);

        $padlet = Padlet::findOrFail($id);
        Gate::authorize('admin', $padlet);
        $padlet->update($request->all());
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
        if ($padlet) {
            $padlet->delete();
            return response()->json('padlet (' . $id . ') successfully deleted', 200);
        }
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
}
