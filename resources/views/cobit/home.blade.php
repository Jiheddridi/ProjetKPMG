<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBIT 2019 - KPMG Digital Governance Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .kpmg-blue {
            color: #00338D;
        }
        .kpmg-bg {
            background-color: #00338D;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header avec logo KPMG -->
    <header class="kpmg-bg text-white shadow-2xl">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Logo KPMG -->
                    <div class="bg-white p-3 rounded-lg">
                        <svg width="80" height="40" viewBox="0 0 200 100" xmlns="http://www.w3.org/2000/svg">
                            <text x="10" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#00338D">KPMG</text>
                            <text x="10" y="55" font-family="Arial, sans-serif" font-size="8" fill="#666">Advisory</text>
                            <circle cx="170" cy="25" r="15" fill="#00338D"/>
                            <path d="M160 25 L180 15 L180 35 Z" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">COBIT 2019</h1>
                        <p class="text-blue-200">Digital Governance Platform</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if(isset($user))
                        <div class="text-sm text-blue-200">
                            <i class="fas fa-user mr-1"></i>
                            {{ $user['name'] ?? 'Utilisateur' }} ({{ $user['role'] ?? 'N/A' }})
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-blue-200 hover:text-white transition-colors">
                                <i class="fas fa-sign-out-alt mr-1"></i>
                                Déconnexion
                            </button>
                        </form>
                    @endif
                    <span class="text-sm">Powered by KPMG Advisory</span>
                    <div class="w-2 h-2 bg-green-400 rounded-full pulse-animation"></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <div class="animate-float">
                <i class="fas fa-cogs text-6xl mb-6 opacity-80"></i>
            </div>
            <h1 class="text-5xl font-bold mb-6">
                Évaluation des Design Factors
                <span class="block text-3xl text-blue-200 mt-2">COBIT 2019 Framework</span>
            </h1>
            <p class="text-xl mb-8 max-w-3xl mx-auto leading-relaxed">
                Optimisez votre gouvernance IT avec notre plateforme d'évaluation avancée. 
                Analysez les 10 Design Factors critiques et obtenez des recommandations personnalisées 
                basées sur les meilleures pratiques COBIT 2019.
            </p>
            <div class="flex justify-center space-x-4">
                <button onclick="startEvaluation()" class="bg-white kpmg-blue px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition-all transform hover:scale-105">
                    <i class="fas fa-play mr-2"></i>Commencer l'Évaluation
                </button>
                <button onclick="viewDemo()" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-blue-600 transition-all">
                    <i class="fas fa-eye mr-2"></i>Voir la Démo
                </button>
            </div>
        </div>
    </section>

    <!-- Design Factors Grid -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold kpmg-blue mb-4">Les 10 Design Factors COBIT 2019</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Chaque Design Factor influence la conception de votre système de gouvernance. 
                    Cliquez sur un facteur pour commencer son évaluation détaillée.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                @foreach($designFactors as $index => $df)
                <div class="card-hover bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border-2 border-blue-200 cursor-pointer"
                     onclick="openDF({{ $df->getNumberFromCode() }})">
                    <div class="text-center">
                        <!-- Icône du DF -->
                        <div class="w-16 h-16 kpmg-bg rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white font-bold text-xl">{{ $df->getNumberFromCode() }}</span>
                        </div>
                        
                        <!-- Code et titre -->
                        <h3 class="font-bold text-lg kpmg-blue mb-2">{{ $df->code }}</h3>
                        <h4 class="font-semibold text-gray-800 mb-3 text-sm leading-tight">{{ $df->title }}</h4>
                        
                        <!-- Indicateur de progression -->
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                 style="width: 0%" id="progress-df{{ $df->getNumberFromCode() }}"></div>
                        </div>
                        
                        <!-- Statut -->
                        <div class="flex items-center justify-center space-x-2">
                            <div class="w-3 h-3 bg-gray-400 rounded-full" id="status-df{{ $df->getNumberFromCode() }}"></div>
                            <span class="text-xs text-gray-600" id="status-text-df{{ $df->getNumberFromCode() }}">Non commencé</span>
                        </div>
                        
                        <!-- Score -->
                        <div class="mt-3 text-center">
                            <span class="text-2xl font-bold kmpg-blue" id="score-df{{ $df->getNumberFromCode() }}">-</span>
                            <span class="text-xs text-gray-500 block">Score</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Bouton Canvas Final -->
            <div class="text-center mt-12">
                <button id="final-canvas-btn" 
                        onclick="openFinalCanvas()" 
                        class="bg-gradient-to-r from-green-500 to-blue-600 text-white px-12 py-4 rounded-xl font-bold text-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-105"
                        disabled>
                    <i class="fas fa-chart-area mr-3"></i>
                    Canvas de Résultats Finaux
                    <span class="block text-sm mt-1">Complétez tous les DF pour débloquer</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold kpmg-blue mb-4">Fonctionnalités Avancées</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <i class="fas fa-brain text-4xl text-purple-600 mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">IA Intégrée</h3>
                    <p class="text-gray-600">Recommandations intelligentes basées sur vos évaluations</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <i class="fas fa-chart-line text-4xl text-green-600 mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">Graphiques Temps Réel</h3>
                    <p class="text-gray-600">Visualisations interactives qui s'adaptent à vos données</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <i class="fas fa-target text-4xl text-red-600 mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">Objectifs COBIT</h3>
                    <p class="text-gray-600">Mapping précis avec les 40 objectifs COBIT 2019</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="kmpg-bg text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <div class="flex items-center justify-center space-x-4 mb-4">
                <div class="bg-white p-2 rounded">
                    <svg width="40" height="20" viewBox="0 0 200 100" xmlns="http://www.w3.org/2000/svg">
                        <text x="10" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#00338D">KPMG</text>
                    </svg>
                </div>
                <span class="text-lg">COBIT 2019 Digital Platform</span>
            </div>
            <p class="text-blue-200">&copy; 2025 KPMG Advisory. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Variables globales
        let evaluationProgress = {};
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeProgress();
            loadProgressFromStorage();

            // Vérifier les mises à jour toutes les 2 secondes
            setInterval(loadProgressFromStorage, 2000);
        });

        // Initialiser le progrès
        function initializeProgress() {
            // Charger les données depuis le serveur
            @foreach($designFactors as $df)
            @php $dfKey = 'DF' . $df->getNumberFromCode(); @endphp
            evaluationProgress['{{ $dfKey }}'] = {
                completed: {{ isset($dfStatuses[$dfKey]) ? ($dfStatuses[$dfKey]['completed'] ? 'true' : 'false') : 'false' }},
                score: {{ isset($dfStatuses[$dfKey]) ? $dfStatuses[$dfKey]['score'] : 0 }},
                progress: {{ isset($dfStatuses[$dfKey]) ? $dfStatuses[$dfKey]['progress'] : 0 }}
            };

            // Mettre à jour l'affichage immédiatement
            updateDFProgress(
                {{ $df->getNumberFromCode() }},
                {{ isset($dfStatuses[$dfKey]) ? $dfStatuses[$dfKey]['progress'] : 0 }},
                {{ isset($dfStatuses[$dfKey]) ? $dfStatuses[$dfKey]['score'] : 0 }},
                {{ isset($dfStatuses[$dfKey]) ? ($dfStatuses[$dfKey]['completed'] ? 'true' : 'false') : 'false' }}
            );
            @endforeach
            updateFinalCanvasButton();
        }

        // Charger le progrès depuis localStorage (pour les mises à jour en temps réel)
        function loadProgressFromStorage() {
            let hasUpdates = false;
            @foreach($designFactors as $df)
            const df{{ $df->getNumberFromCode() }}Status = localStorage.getItem('df{{ $df->getNumberFromCode() }}_status');
            if (df{{ $df->getNumberFromCode() }}Status) {
                const status = JSON.parse(df{{ $df->getNumberFromCode() }}Status);
                const currentStatus = evaluationProgress['DF{{ $df->getNumberFromCode() }}'];

                // Vérifier s'il y a des changements
                if (currentStatus.score !== status.score ||
                    currentStatus.progress !== status.progress ||
                    currentStatus.completed !== status.completed) {

                    evaluationProgress['DF{{ $df->getNumberFromCode() }}'] = {
                        completed: status.completed || false,
                        score: status.score || 0,
                        progress: status.progress || 0
                    };
                    updateDFProgress({{ $df->getNumberFromCode() }}, status.progress || 0, status.score || 0, status.completed || false);
                    hasUpdates = true;
                }
            }
            @endforeach

            if (hasUpdates) {
                updateFinalCanvasButton();
            }
        }
        
        // Ouvrir un Design Factor
        function openDF(dfNumber) {
            window.location.href = `/cobit/df/${dfNumber}`;
        }
        
        // Commencer l'évaluation
        function startEvaluation() {
            openDF(1);
        }
        
        // Voir la démo
        function viewDemo() {
            alert('Démo à venir - Fonctionnalité en développement');
        }
        
        // Ouvrir le canvas final
        function openFinalCanvas() {
            window.location.href = '/cobit/canvas-final';
        }
        
        // Mettre à jour le bouton canvas final
        function updateFinalCanvasButton() {
            const completedCount = Object.values(evaluationProgress).filter(df => df.completed).length;
            const btn = document.getElementById('final-canvas-btn');
            
            if (completedCount === 10) {
                btn.disabled = false;
                btn.innerHTML = `
                    <i class="fas fa-chart-area mr-3"></i>
                    Canvas de Résultats Finaux
                    <span class="block text-sm mt-1">Tous les DF complétés ✓</span>
                `;
            } else {
                btn.innerHTML = `
                    <i class="fas fa-chart-area mr-3"></i>
                    Canvas de Résultats Finaux
                    <span class="block text-sm mt-1">${completedCount}/10 DF complétés</span>
                `;
            }
        }
        
        // Simuler la mise à jour du progrès (à connecter avec les vraies données)
        function updateDFProgress(dfNumber, progress, score, completed) {
            evaluationProgress[`DF${dfNumber}`] = { progress, score, completed };
            
            // Mettre à jour l'interface
            const progressBar = document.getElementById(`progress-df${dfNumber}`);
            const statusDot = document.getElementById(`status-df${dfNumber}`);
            const statusText = document.getElementById(`status-text-df${dfNumber}`);
            const scoreDisplay = document.getElementById(`score-df${dfNumber}`);
            
            if (progressBar) progressBar.style.width = progress + '%';
            if (scoreDisplay) scoreDisplay.textContent = score.toFixed(1);
            
            if (statusDot && statusText) {
                if (completed) {
                    statusDot.className = 'w-3 h-3 bg-green-500 rounded-full';
                    statusText.textContent = 'Complété';
                } else if (progress > 0) {
                    statusDot.className = 'w-3 h-3 bg-yellow-500 rounded-full';
                    statusText.textContent = 'En cours';
                } else {
                    statusDot.className = 'w-3 h-3 bg-gray-400 rounded-full';
                    statusText.textContent = 'Non commencé';
                }
            }
            
            updateFinalCanvasButton();
        }
    </script>

    <!-- Intégration du Chatbot COBIT 2019 -->
    @include('components.chatbot')
</body>
</html>
