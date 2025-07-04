<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPMG COBIT 2019 - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .kmpg-blue { color: #00338D; }
        .kmpg-bg { background-color: #00338D; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .login-container {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .kpmg-logo {
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="login-container">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header avec logo KPMG -->
            <div class="text-center">
                <div class="animate-float">
                    <!-- Logo KPMG Principal -->
                    <div class="mx-auto mb-6">
                        <svg class="kpmg-logo" width="200" height="80" viewBox="0 0 400 160" xmlns="http://www.w3.org/2000/svg">
                            <!-- Logo KPMG stylisé -->
                            <rect x="0" y="0" width="400" height="160" fill="#00338D" rx="10"/>
                            <text x="50" y="70" font-family="Arial, sans-serif" font-size="48" font-weight="bold" fill="white">KPMG</text>
                            <text x="50" y="100" font-family="Arial, sans-serif" font-size="16" fill="#E0E7FF">Advisory Services</text>
                            <text x="50" y="120" font-family="Arial, sans-serif" font-size="14" fill="#C7D2FE">Digital Governance</text>
                            
                            <!-- Icône moderne -->
                            <circle cx="320" cy="50" r="25" fill="white"/>
                            <path d="M305 50 L335 35 L335 65 Z" fill="#00338D"/>
                            
                            <!-- Éléments décoratifs -->
                            <circle cx="350" cy="30" r="8" fill="#E0E7FF" opacity="0.7"/>
                            <circle cx="370" cy="45" r="6" fill="#C7D2FE" opacity="0.5"/>
                            <circle cx="360" cy="70" r="4" fill="#A5B4FC" opacity="0.3"/>
                        </svg>
                    </div>
                </div>
                
                <h2 class="text-3xl font-bold kmpg-blue mb-2">
                    Plateforme COBIT 2019
                </h2>
                <p class="text-gray-600 mb-8">
                    Système d'évaluation des Design Factors
                    <br>
                    <span class="text-sm text-gray-500">Accès sécurisé KPMG</span>
                </p>
            </div>

            <!-- Formulaire de connexion -->
            <div class="bg-white rounded-xl shadow-2xl p-8 border border-gray-200">
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span class="text-red-800 text-sm">{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-green-800 text-sm">{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 kmpg-blue"></i>
                            Nom d'utilisateur
                        </label>
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            required 
                            value="{{ old('username') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Entrez votre nom d'utilisateur"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 kmpg-blue"></i>
                            Mot de passe
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pr-12"
                                placeholder="Entrez votre mot de passe"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="#" class="text-sm kmpg-blue hover:underline">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full kmpg-bg text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-105"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Se connecter
                    </button>
                </form>

                <!-- Informations de test -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Comptes de test disponibles
                    </h4>
                    <div class="text-xs text-blue-700 space-y-1">
                        <div><strong>Admin:</strong> admin / password</div>
                        <div><strong>Consultant:</strong> consultant / password</div>
                        <div><strong>Auditeur:</strong> auditor / password</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-500">
                <p>&copy; 2025 KPMG Advisory Services. Tous droits réservés.</p>
                <p class="mt-1">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Connexion sécurisée SSL
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.style.opacity = '0';
            form.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                form.style.transition = 'all 0.6s ease';
                form.style.opacity = '1';
                form.style.transform = 'translateY(0)';
            }, 300);
        });
    </script>
</body>
</html>
