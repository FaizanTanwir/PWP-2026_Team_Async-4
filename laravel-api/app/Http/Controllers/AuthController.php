<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Login User
     *
     * Authenticate a user via email and password to receive a Sanctum Bearer token.
     * @status 200 { "user": { "id": 1, "name": "Mahtab", "email": "m@oulu.fi", "roles": ["Student"] }, "token": "1|secret_token" }
     * @status 401 { "message": "Invalid credentials" }
     * @status 422 { "message": "The email field is required.", "errors": { "email": ["The email field is required."] } }
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /**
         * @var User $user
         */
        $user = Auth::user();

        // This creates the plain text token the user will use
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Register Student
     *
     * Create a new student account. By default, the 'Student' role is assigned.
     * @status 201 { "user": { ... }, "token": "2|secret_token" }
     * @status 422 { "message": "The email has already been taken.", "errors": { "email": ["The email has already been taken."], "password": ["The password confirmation does not match."] } }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole(UserRole::STUDENT->value);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Logout
     *
     * Invalidate the current Bearer token.
     * @status 200 { "message": "Successfully logged out" }
     * @status 401 { "message": "Unauthenticated." }
     */
    public function logout(Request $request)
    {
        // Revoke (delete) the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }
}
