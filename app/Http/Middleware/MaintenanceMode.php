<?php

namespace App\Http\Middleware;

use Closure;

class MaintenanceMode
{
    public function handle($request, Closure $next)
    {
        $getMaintenanceMode = 0;
        if ($getMaintenanceMode == 1) {

            if ($request->is('api/*')) {
                if($request->is('api/section-data/maintenance')){
                    return $next($request);
                }
                $notify[] = 'Our application is currently in maintenance mode';
                return response()->json(['remark'=>'maintenance_mode','status'=>'error','message'=>['error'=>$notify]]);
            }

            if ($request->is('user/*') && (
                $request->header('X-User-Spa') === '1'
                || $request->headers->has('X-Inertia')
                || $request->expectsJson()
            )) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Our application is currently in maintenance mode.',
                ], 503);
            }

            return to_route('maintenance');
        }
        return $next($request);
    }
}
