<?php

namespace App\Helpers;

use App\Models\Group;
use Auth;

class Permissions {

    public static function check($page, $permission = "open") {
        //dd('sss');

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            //dd($user);
            $group = Group::where('active', 1)->find($user->group_id);

            if ($group !== null) {

                $permissions = $group->permissions;
                $permissions = json_decode($permissions);

                if (isset($permissions->{$page})) {
                    if (isset($permissions->{$page}->{$permission})) {
                        if ($permissions->{$page}->{$permission} == 1) {

                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public static function user($page, $permission = "open") {
        //dd('sss');

        if (Auth::guard()->check()) {
            $user = Auth::guard()->user();


            $permissions = $user->permissions;
            $permissions = json_decode($permissions);

            if (isset($permissions->{$page})) {
                if (isset($permissions->{$page}->{$permission})) {
                    if ($permissions->{$page}->{$permission} == 1) {

                        return true;
                    }
                }
            }
        }

        return false;
    }

}
