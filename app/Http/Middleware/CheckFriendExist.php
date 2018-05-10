<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class CheckFriendExist {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $friend_id = $request->input('friend_id');
        $friend=User::find($friend_id);
        if ($friend == null) {
            return _api_json(false, '', 400, ['message' => 'friend not found']);
        }
        return $next($request);
    }

}
