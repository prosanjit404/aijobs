<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpaPageController extends Controller
{
    private function page(string $component, string $title, array $props): JsonResponse
    {
        return response()->json([
            'component' => $component,
            'title' => $title,
            'props' => $props,
        ])->header('Cache-Control', 'private, no-store');
    }

    public function home(): JsonResponse
    {
        return $this->page('Home', 'Home', [
            'site_name' => config('app.name', 'AI Jobs'),
            'total_users' => User::query()->count(),
            'server_time' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function login(): JsonResponse
    {
        return $this->page('Login', 'Login', [
            'site_name' => config('app.name', 'AI Jobs'),
        ]);
    }

    public function register(): JsonResponse
    {
        return $this->page('Register', 'Register', [
            'site_name' => config('app.name', 'AI Jobs'),
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user('web');

        return $this->page('Dashboard', 'Dashboard', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'joined_at' => $user->created_at?->format('d M Y'),
            ],
        ]);
    }
}
