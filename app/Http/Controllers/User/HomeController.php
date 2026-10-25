<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'website_name' => config('app.name'),
                'total_users' => User::query()->count(),
                'server_time' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function session(Request $request): JsonResponse
    {
        return response()->json([
            'authenticated' => $request->user() !== null,
        ])->header('Cache-Control', 'no-store, private');
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

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
}