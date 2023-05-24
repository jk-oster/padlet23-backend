<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Check if the honeypot field is empty
        if (!empty($request->input('name'))) {
            abort(403, 'Spam detected');
        }

        $validated = $request->validate([
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|confirmed',
        ]);

        $credentials = request($validated['email'], $validated['password']);
        $token = auth()->attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me() : \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function search($searchTerm) : \Illuminate\Http\JsonResponse
    {
        $possibleUsers = User::where('name', 'like', '%' . $searchTerm . '%')->get();
        return response()->json($possibleUsers, 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() : \Illuminate\Http\JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() : \Illuminate\Http\JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token) : \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Check if the honeypot field is empty
        if (!empty($request->input('email_confirmation'))) {
            abort(403, 'Spam detected');
        }

        $validated = $request->validate([
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'avatar' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'avatar' => $validated['avatar'],
        ]);
        $token = auth()->login($user);
        return $this->respondWithToken($token);
    }
}
