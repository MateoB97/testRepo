<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\UserRoles;

class PermisosValidator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permiso)
    {
        $permisos = UserRoles::find(Auth::user()->user_rol_id)->permisos;

        $permisos = explode(',',$permisos);

        if(in_array($permiso, $permisos)) {
            return $next($request);
        }
        else {
            return response()->json(['error_msg' => 'No tiene permisos para acceder a esta ruta', 'error_type' => 'Unauthorized', 'user' => Auth::user()], 403);
        }
    }
}
