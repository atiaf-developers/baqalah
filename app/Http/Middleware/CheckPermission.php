<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class CheckPermission {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $page, $permission = "open") {
        //abort(403);

        if (!\Permissions::check($page, $permission)) {
            //dd('sss');
            if ($request->ajax()) {
                App()->abort(403, 'Access denied');
            } else {
                return redirect()->route('admin.error');
            }

            //return view('main_content/backend/err404');
        }
        return $next($request);
    }

}
