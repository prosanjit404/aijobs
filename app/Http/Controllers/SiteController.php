<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SiteController extends Controller
{
    public function home(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'site_name' => config('app.name'),
                'total_users' => User::query()->count(),
                'server_time' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function session(): JsonResponse
    {
        $user = Auth::guard('web')->user();

        return response()->json([
            'authenticated' => $user !== null,
            'user' => $user?->only([
                'id',
                'name',
                'email',
            ]),
            'csrf_token' => csrf_token(),
        ])->header('Cache-Control', 'no-store, private');
    }

    public function login(Request $request): JsonResponse
    {
        if (Auth::guard('web')->check()) {
            return response()->json([
                'message' => 'Already logged in.',
            ], 409);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('web')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'csrf_token' => csrf_token(),
            'user' => Auth::guard('web')->user()->only([
                'id',
                'name',
                'email',
            ]),
        ])->header('Cache-Control', 'no-store, private');
    }

    public function register(Request $request): JsonResponse
    {
        if (Auth::guard('web')->check()) {
            return response()->json([
                'message' => 'Already logged in.',
            ], 409);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'csrf_token' => csrf_token(),
            'user' => $user->only([
                'id',
                'name',
                'email',
            ]),
        ], 201)->header('Cache-Control', 'no-store, private');
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user('web');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'joined_at' => $user->created_at?->format('d M Y'),
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
            'csrf_token' => csrf_token(),
        ])->header('Cache-Control', 'no-store, private');
    }
}