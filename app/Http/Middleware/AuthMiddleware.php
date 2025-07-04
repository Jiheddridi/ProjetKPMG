<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!AuthController::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
            
            return redirect('/login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier la validité de la session
        $user = AuthController::user();
        if (!$user || !isset($user['login_time'])) {
            Session::flush();
            return redirect('/login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }

        // Vérifier l'expiration de la session (24 heures)
        $loginTime = \Carbon\Carbon::parse($user['login_time']);
        if ($loginTime->diffInHours(now()) > 24) {
            Session::flush();
            return redirect('/login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }

        // Ajouter les informations utilisateur à la requête
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
