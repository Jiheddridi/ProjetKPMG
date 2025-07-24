<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canvas Historique - {{ $canvas->company_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .kpmg-blue { color: #00338D; }
        .kpmg-bg { background-color: #00338D; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .results-canvas { 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="kpmg-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button onclick="goToHistory()" class="text-white hover:text-blue-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </button>
                    <div class="bg-white p-2 rounded">
                        <svg width="40" height="20" viewBox="0 0 200 100" xmlns="http://www.w3.org/2000/svg">
                            <text x="10" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#00338D">KPMG</text>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Canvas Historique</h1>
                        <p class="text-blue-200 text-sm">{{ $canvas->company_name }} - {{ $canvas->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <i class="fas fa-history mr-1"></i>
                        Évaluation du {{ $canvas->created_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Canvas Principal -->
    <div class="container mx-auto px-6 py-8">
        <div class="results-canvas mb-8">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold kpmg-blue mb-4">
                    <i class="fas fa-archive mr-3 text-blue-500"></i>
                    Canvas Sauvegardé
                </h2>
                <div class="bg-blue-50 rounded-lg p-4 mb-4 inline-block">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">{{ $canvas->company_name }}</h3>
                    <div class="flex items-center justify-center space-x-4 text-sm text-blue-600">
                        <span><i class="fas fa-building mr-1"></i>{{ $canvas->company_size }}</span>
                        <span><i class="fas fa-user mr-1"></i>{{ $canvas->user_name }}</span>
                        <span><i class="fas fa-calendar mr-1"></i>{{ $canvas->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mt-2 text-green-600 text-sm">
                        <i class="fas fa-check-circle mr-1"></i>Score global: {{ $canvas->score_global }}/5
                    </div>
                </div>
                <p class="text-xl text-gray-600">Résultats de l'évaluation des 10 Design Factors</p>
            </div>

            <!-- Métriques Globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($canvas->score_global, 1) }}</div>
                    <div class="text-sm text-gray-600">Score Global</div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ ($canvas->score_global / 5) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-green-600 mb-2">{{ $canvas->completed_dfs }}</div>
                    <div class="text-sm text-gray-600">Design Factors</div>
                    <div class="mt-2 text-xs text-gray-500">Complétés</div>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-purple-600 mb-2">{{ $canvas->status }}</div>
                    <div class="text-sm text-gray-600">Statut</div>
                    <div class="mt-2 text-xs text-green-600">✓ Évaluation terminée</div>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-orange-600 mb-2">{{ $canvas->evaluation_duration ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-600">Durée</div>
                    <div class="mt-2 text-xs text-gray-500">Temps d'évaluation</div>
                </div>
            </div>

            <!-- Message informatif -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Canvas Historique:</strong> Cette évaluation a été sauvegardée le {{ $canvas->created_at->format('d/m/Y à H:i') }}. 
                            Les données affichées correspondent à l'état de l'évaluation à ce moment-là.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center">
                <button onclick="goToHistory()" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors mr-4">
                    <i class="fas fa-arrow-left mr-2"></i>Retour à l'historique
                </button>
                <button onclick="startNewEvaluation()" class="kpmg-bg text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nouvelle évaluation
                </button>
            </div>
        </div>
    </div>

    <script>
        function goToHistory() {
            window.location.href = '/cobit/historique';
        }

        function startNewEvaluation() {
            window.location.href = '/cobit/home';
        }
    </script>
</body>
</html>
