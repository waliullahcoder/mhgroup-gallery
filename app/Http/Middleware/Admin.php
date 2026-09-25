<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use App\Models\AdminMenuAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $currentRouteName = $request->route()->getName();
            $menu = AdminMenu::where('route', $currentRouteName)->first();
            if (!is_null($menu)) {
                $currentRoutePermission = Permission::findById($menu->permission_id);
            } else {
                $menuAction = AdminMenuAction::where('route', $currentRouteName)->first();
                if ($menuAction) {
                    $currentRoutePermission = Permission::findById($menuAction->permission_id);
                } else {
                    $currentRoutePermission = NULL;
                }
            }

            if (!is_null($currentRoutePermission)) {
                if (Auth::user()->can($currentRoutePermission->name)) {
                    return $next($request);
                } else {
                    if ($request->ajax()) {
                        return response()->json(['status' => 'error', 'data' => "You don't have any Authority to do this action"]);
                    }
                    return redirect()->back()->withErrors('You do not have Access to do this action!');
                }
            }

            return $next($request);
        } else {
            if ($request->method() === 'GET' && !$request->ajax()) {
                Session::put('url.intended', $request->fullUrl());
            }

            return redirect()->route('admin.login.index');
        }
    }
}
