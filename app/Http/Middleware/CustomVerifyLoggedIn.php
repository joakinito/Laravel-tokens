<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomVerifyLoggedIn
{
    public function handle(Request $request, Closure $next)
    {

        if ($request->is('checkLoggedIn') || $request->is('customLogout')) {
            return $next($request);
        }

        $token = $request->bearerToken();

        if (!$token) {

            return response()->json([
                'status' => false,
                'message' => 'Token no proporcionado.',
            ], 401);
        }


        $isSanctumToken = DB::table('personal_access_tokens')
            ->where('token', $token)
            ->exists();


        $isCustomToken = DB::table('users')
            ->where('remember_token', $token)
            ->exists();

        if ($isSanctumToken || $isCustomToken) {

            $user = $isSanctumToken
                ? DB::table('personal_access_tokens')->where('token', $token)->value('tokenable_id')
                : DB::table('users')->where('remember_token', $token)->value('id');

            Auth::onceUsingId($user);

            return $next($request);
        }


        return response()->json([
            'status' => false,
            'message' => 'Token no válido.',
        ], 401);
    }
}
