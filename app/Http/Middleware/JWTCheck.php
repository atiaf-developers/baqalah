<?php

namespace App\Http\Middleware;

use App\Helpers\AUTHORIZATION;
use App\Models\User;
use Closure;

class JWTCheck {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $token = $request->header('authorization');
        if ($token != null) {
            $token = Authorization::validateToken($token);
            if ($token) {
                $user_id = $token->id;
                $expire = $token->expire;
                if ($expire > strtotime('now')) {
                    $find = User::where('id',$user_id)->where('active',true)->first();
                    if ($find==null) {
                        return _api_json('', ['message' => 'user not found'],401);
                    }
                } else {
                    return _api_json('',['message' => 'token expire'],401);
                }
            } else {
                return _api_json('',['message' => 'invalid token'],401);
            }
        } else {
            return _api_json('', ['message' => 'token not provided'],401);
        }
        return $next($request);
    }

}
