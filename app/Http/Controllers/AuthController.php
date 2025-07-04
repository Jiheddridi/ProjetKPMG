<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');
        
        // Comptes de test prédéfinis
        $testAccounts = [
            'admin' => [
                'password' => 'password',
                'role' => 'admin',
                'name' => 'Administrateur KPMG',
                'email' => 'admin@kpmg.com'
            ],
            'consultant' => [
                'password' => 'password',
                'role' => 'consultant',
                'name' => 'Consultant KPMG',
                'email' => 'consultant@kpmg.com'
            ],
            'auditor' => [
                'password' => 'password',
                'role' => 'auditor',
                'name' => 'Auditeur KPMG',
                'email' => 'auditor@kpmg.com'
            ],
            'system' => [
                'password' => 'system',
                'role' => 'system',
                'name' => 'Utilisateur Système',
                'email' => 'system@kpmg.com'
            ]
        ];

        $username = $credentials['username'];
        $password = $credentials['password'];

        // Vérifier les comptes de test
        if (isset($testAccounts[$username]) && $testAccounts[$username]['password'] === $password) {
            // Créer une session utilisateur
            Session::put('user', [
                'username' => $username,
                'role' => $testAccounts[$username]['role'],
                'name' => $testAccounts[$username]['name'],
                'email' => $testAccounts[$username]['email'],
                'logged_in' => true,
                'login_time' => now()->toISOString()
            ]);

            Session::put('authenticated', true);

            // Log de connexion
            \Log::info("Connexion réussie pour l'utilisateur: {$username}");

            return redirect()->intended('/cobit/home')->with('status', 'Connexion réussie !');
        }

        return back()->withErrors([
            'username' => 'Les informations de connexion sont incorrectes.',
        ])->withInput($request->except('password'));
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $user = Session::get('user');
        if ($user) {
            \Log::info("Déconnexion de l'utilisateur: {$user['username']}");
        }

        Session::forget('user');
        Session::forget('authenticated');
        Session::flush();

        return redirect('/login')->with('status', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    public static function check()
    {
        return Session::get('authenticated', false);
    }

    /**
     * Obtenir l'utilisateur connecté
     */
    public static function user()
    {
        return Session::get('user');
    }

    /**
     * Middleware pour vérifier l'authentification
     */
    public static function requireAuth()
    {
        if (!self::check()) {
            return redirect('/login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }
        return null;
    }

    /**
     * Vérifier le rôle de l'utilisateur
     */
    public static function hasRole($role)
    {
        $user = self::user();
        return $user && $user['role'] === $role;
    }

    /**
     * Vérifier si l'utilisateur a l'un des rôles spécifiés
     */
    public static function hasAnyRole($roles)
    {
        $user = self::user();
        return $user && in_array($user['role'], $roles);
    }

    /**
     * Obtenir les informations de session pour l'affichage
     */
    public static function getSessionInfo()
    {
        $user = self::user();
        if (!$user) {
            return null;
        }

        return [
            'username' => $user['username'],
            'name' => $user['name'],
            'role' => $user['role'],
            'email' => $user['email'],
            'login_time' => $user['login_time'],
            'session_duration' => now()->diffInMinutes($user['login_time']) . ' minutes'
        ];
    }
}
