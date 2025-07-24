<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canvas Final - Résultats COBIT 2019</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .kpmg-blue { color: #00338D; }
        .kpmg-bg { background-color: #00338D; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .animate-pulse-slow { animation: pulse 3s infinite; }
        .results-canvas {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .filter-btn {
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .filter-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .filter-btn.active {
            background-color: #1d4ed8 !important;
            box-shadow: 0 0 0 2px #93c5fd;
        }
        #total-objectives {
            transition: transform 0.2s ease;
        }
        .objective-modal {
            backdrop-filter: blur(5px);
        }
        .chart-container {
            position: relative;
            transition: all 0.3s ease;
        }
        .chart-container:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="kpmg-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button onclick="goHome()" class="text-white hover:text-blue-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </button>
                    <div class="bg-white p-2 rounded">
                        <svg width="40" height="20" viewBox="0 0 200 100" xmlns="http://www.w3.org/2000/svg">
                            <text x="10" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#00338D">KPMG</text>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Canvas de Résultats Finaux</h1>
                        <p class="text-blue-200 text-sm">Analyse Complète COBIT 2019</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm animate-pulse-slow">✅ Évaluation Complète</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Canvas Principal -->
    <div class="container mx-auto px-6 py-8">
        <div class="results-canvas mb-8">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold kpmg-blue mb-4">
                    <i class="fas fa-trophy mr-3 text-yellow-500"></i>
                    Résultats Finaux COBIT 2019
                </h2>
                @if(isset($evaluation))
                <div class="bg-blue-50 rounded-lg p-4 mb-4 inline-block">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">{{ $evaluation->nom_entreprise }}</h3>
                    <div class="flex items-center justify-center space-x-4 text-sm text-blue-600">
                        <span><i class="fas fa-building mr-1"></i>{{ $evaluation->taille_entreprise }}</span>
                        <span><i class="fas fa-user mr-1"></i>{{ $evaluation->user_name ?? 'Utilisateur' }}</span>
                        <span><i class="fas fa-calendar mr-1"></i>{{ $evaluation->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($evaluation->contraintes)
                    <div class="mt-2 text-sm text-blue-600">
                        <i class="fas fa-info-circle mr-1"></i>{{ $evaluation->contraintes }}
                    </div>
                    @endif
                    <div class="mt-2 text-green-600 text-sm">
                        <i class="fas fa-check-circle mr-1"></i>Évaluation terminée - Score: {{ $scoreGlobal }}/5
                    </div>
                </div>
                @endif
                <p class="text-xl text-gray-600">Analyse complète de votre évaluation des 10 Design Factors</p>
            </div>

            <!-- Métriques Globales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-green-600 mb-2" id="maturity-level">0</div>
                    <div class="text-sm text-gray-600">Niveau de Maturité</div>
                    <div class="mt-2 text-xs text-gray-500" id="maturity-description">-</div>
                </div>

                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-purple-600 mb-4" id="total-objectives">0</div>
                    <div class="text-sm text-gray-600 mb-4">Filtres d'Objectifs</div>
                    <div class="flex flex-wrap justify-center gap-2">
                        <button onclick="filterObjectives(3)" class="filter-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                            Top 3
                        </button>
                        <button onclick="filterObjectives(5)" class="filter-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                            Top 5
                        </button>
                        <button onclick="filterObjectives(10)" class="filter-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                            Top 10
                        </button>
                        <button onclick="filterObjectives('all')" class="filter-btn bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                            Tous
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">sur 40 objectifs COBIT</div>
                </div>

                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-orange-600 mb-2" id="completion-rate">100%</div>
                    <div class="text-sm text-gray-600">Taux de Complétude</div>
                    <div class="mt-2 text-xs text-green-600">✓ Évaluation terminée</div>
                </div>
            </div>

            <!-- Graphiques Principaux -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Graphique Radar Global -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-xl font-bold kmpg-blue mb-4 flex items-center">
                        <i class="fas fa-chart-area mr-2"></i>
                        Vue d'ensemble - Radar Chart
                    </h3>
                    <div class="relative h-80">
                        <canvas id="final-radar-chart"></canvas>
                    </div>
                </div>
                
                <!-- Graphique de Performance par DF -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-xl font-bold kmpg-blue mb-4 flex items-center justify-between w-full">
                        <div>
                            <i class="fas fa-chart-bar mr-2"></i>
                            Performance par Objectif
                        </div>
                        <div class="text-sm font-normal text-gray-600">
                            Graphique interactif - Cliquez sur une barre pour plus de détails
                        </div>
                    </h3>

                    <!-- Filter Buttons -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex gap-2">
                            <button onclick="filterObjectives(3)" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-blue-50">
                                Top 3
                            </button>
                            <button onclick="filterObjectives(5)" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-blue-50">
                                Top 5
                            </button>
                            <button onclick="filterObjectives(10)" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-blue-50">
                                Top 10
                            </button>
                            <button onclick="filterObjectives('all')" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-blue-50 active">
                                Tous
                            </button>
                        </div>
                        <div class="text-sm text-gray-600">
                            Objectifs affichés: <span id="total-objectives" class="font-bold text-blue-600">0</span>
                        </div>
                    </div>

                    <div class="relative h-80 chart-container">
                        <canvas id="objective-performance-chart"></canvas>
                    </div>
                </div>
            </div>



            <!-- Meilleurs Objectifs COBIT -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 card-hover">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold kmpg-blue flex items-center">
                        <i class="fas fa-star mr-2 text-yellow-500"></i>
                        Meilleurs Objectifs COBIT ({{ count($bestObjectives) }} objectifs)
                    </h3>
                    <button onclick="generateRoadmap()" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:from-purple-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg">
                        <i class="fas fa-route mr-2"></i>Générer Roadmap IA
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($bestObjectives as $index => $objective)
                    <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-lg p-4 border-l-4 border-green-500 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-lg text-green-700">{{ $objective['objective'] ?? 'N/A' }}</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-semibold">
                                {{ number_format($objective['score'] ?? 0, 1) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">
                            <strong>Priorité:</strong>
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ ($objective['priority'] ?? 'L') == 'H' ? 'bg-red-100 text-red-800' :
                                   (($objective['priority'] ?? 'L') == 'M' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $objective['priority'] ?? 'L' }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">
                            Gap: {{ number_format($objective['gap'] ?? 0, 2) }} |
                            Baseline: {{ number_format($objective['baseline'] ?? 0, 1) }}
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all"
                                     style="width: {{ min(100, max(0, ($objective['score'] ?? 0) / 5 * 100)) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Roadmap IA (Section cachée par défaut) -->
            <div id="roadmap-section" class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl shadow-lg p-6 mb-8 card-hover" style="display: none;">
                <h3 class="text-xl font-bold text-purple-700 mb-6 flex items-center">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    Roadmap de Mise en Œuvre IA
                </h3>
                <div id="roadmap-content">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>

            <!-- Recommandations IA Globales -->
            <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-lg p-6 card-hover">
                <h3 class="text-xl font-bold text-purple-800 mb-6 flex items-center">
                    <i class="fas fa-brain mr-2"></i>
                    Recommandations IA Globales
                </h3>
                <div id="global-recommendations" class="space-y-4">
                    <!-- Recommandations générées dynamiquement -->
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center space-x-4">
            <button onclick="exportPDF()" class="bg-red-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
            </button>
            <button onclick="exportExcel()" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i>Exporter Excel
            </button>
            <button onclick="shareResults()" class="kmpg-bg text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                <i class="fas fa-share mr-2"></i>Partager
            </button>
            <button onclick="newEvaluation()" class="bg-gray-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-gray-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Nouvelle Évaluation
            </button>
        </div>
    </div>

    <script>
        // Variables globales
        let finalData = {
            designFactors: [
                @foreach($designFactors as $df)
                @php $dfNumber = str_replace('DF', '', $df->code); @endphp
                {
                    code: '{{ $df->code }}',
                    title: '{{ $df->title }}',
                    number: {{ $dfNumber }},
                    score: Math.random() * 3 + 2, // Simulation
                    completed: true,
                    objectives: Math.floor(Math.random() * 10) + 5
                },
                @endforeach
            ]
        };

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            calculateGlobalMetrics();
            createCharts();
            generateRecommendations();

            // Initialiser le graphique des objectifs
            initializeObjectiveChart();
        });

        // Calculer les métriques globales
        function calculateGlobalMetrics() {
            // Calculer le niveau de maturité basé sur les DF complétés
            const globalScore = {{ $scoreGlobal ?? 3 }};
            const maturityLevel = Math.round(globalScore);
            const totalObjectives = {{ count($bestObjectives ?? []) }};

            // Afficher seulement le niveau de maturité et les objectifs
            document.getElementById('maturity-level').textContent = maturityLevel;
            document.getElementById('total-objectives').textContent = totalObjectives;

            // Description du niveau de maturité
            const maturityDescriptions = {
                1: 'Initial',
                2: 'Géré',
                3: 'Défini',
                4: 'Quantitativement géré',
                5: 'Optimisé'
            };
            document.getElementById('maturity-description').textContent = maturityDescriptions[maturityLevel] || 'Non défini';
        }

        // Créer les graphiques
        function createCharts() {
            // Graphique Radar Final
            const radarCtx = document.getElementById('final-radar-chart');
            new Chart(radarCtx, {
                type: 'line',
                data: {
                    labels: finalData.designFactors.map(df => df.code),
                    datasets: [{
                        label: 'Scores Finaux',
                        data: finalData.designFactors.map(df => df.score),
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(59, 130, 246)',
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }, {
                        label: 'Baseline (2.5)',
                        data: new Array(10).fill(2.5),
                        backgroundColor: 'rgba(156, 163, 175, 0.05)',
                        borderColor: 'rgb(156, 163, 175)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 5,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                        title: {
                            display: true,
                            text: 'Performance Globale des Design Factors'
                        }
                    }
                }
            });

            // Graphique de Performance par Objectif (Meilleurs)
            // Initialisation des données des objectifs
            const defaultObjectives = [
                { objective: 'EDM01', score: 4.2, priority: 'H' },
                { objective: 'EDM02', score: 3.8, priority: 'M' },
                { objective: 'EDM03', score: 3.9, priority: 'H' },
                { objective: 'APO01', score: 3.5, priority: 'M' },
                { objective: 'APO02', score: 3.7, priority: 'H' },
                { objective: 'BAI01', score: 3.4, priority: 'M' },
                { objective: 'BAI02', score: 3.6, priority: 'H' },
                { objective: 'DSS01', score: 4.1, priority: 'H' },
                { objective: 'DSS02', score: 3.2, priority: 'M' },
                { objective: 'MEA01', score: 3.8, priority: 'H' }
            ];
            
            let allObjectives = @json($bestObjectives ?? defaultObjectives);
            let currentObjectives = allObjectives;
            let objectiveChart;

            function createObjectiveChart(objectives) {
                const objBarCtx = document.getElementById('objective-performance-chart');
                if (!objBarCtx) {
                    console.error('Canvas not found');
                    return;
                }

                if (objectiveChart) {
                    objectiveChart.destroy();
                }

                // Utiliser une couleur bleue uniforme pour toutes les barres
                const colors = objectives.map(() => ({
                    background: 'rgba(59, 130, 246, 0.8)', // Bleu avec transparence
                    border: 'rgb(37, 99, 235)',           // Bleu plus foncé pour la bordure
                    hover: 'rgba(37, 99, 235, 0.9)'       // Bleu plus foncé au survol
                }));

                objectiveChart = new Chart(objBarCtx, {
                    type: 'bar',
                    data: {
                        labels: objectives.map(obj => obj.objective),
                        datasets: [{
                            label: 'Score',
                            data: objectives.map(obj => obj.score),
                            backgroundColor: 'rgb(37, 99, 235)',
                            borderColor: 'rgb(29, 78, 216)',
                            borderWidth: 1,
                            borderRadius: 4,
                            barThickness: 40,
                            maxBarThickness: 50,
                            minBarLength: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 5,
                                ticks: {
                                    stepSize: 1,
                                    color: '#333'
                                },
                                grid: {
                                    color: '#e5e7eb',
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#333',
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const objective = objectives[context.dataIndex];
                                        return [
                                            `Score: ${objective.score.toFixed(1)}`,
                                            `Priorité: ${objective.priority || 'N/A'}`
                                        ];
                                    }
                                }
                            }
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                showObjectiveDetails(objectives[index]);
                            }
                        }
                    }
                });
            }

            // Fonction de filtrage des objectifs
            function filterObjectives(count) {
                // Mettre à jour les boutons actifs
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('bg-blue-700', 'active');
                    btn.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
                    if (btn.textContent.includes('Tous')) {
                        btn.classList.remove('bg-blue-500');
                        btn.classList.add('bg-gray-500');
                    } else {
                        btn.classList.remove('bg-gray-500');
                        btn.classList.add('bg-blue-500');
                    }
                });

                // Activer le bouton cliqué
                const clickedButton = event.target;
                clickedButton.classList.remove('bg-blue-500', 'bg-gray-500');
                clickedButton.classList.add('bg-blue-700', 'active');
                clickedButton.style.boxShadow = '0 0 0 2px #93c5fd';

                if (count === 'all') {
                    currentObjectives = allObjectives;
                } else {
                    // Trier par score décroissant et prendre les N premiers
                    currentObjectives = [...allObjectives]
                        .sort((a, b) => b.score - a.score)
                        .slice(0, count);
                }

                // Mettre à jour le graphique avec animation
                createObjectiveChart(currentObjectives);

                // Mettre à jour le compteur avec animation
                const counter = document.getElementById('total-objectives');
                counter.style.transform = 'scale(1.1)';
                counter.textContent = currentObjectives.length;
                setTimeout(() => {
                    counter.style.transform = 'scale(1)';
                }, 200);

                // Afficher un message de confirmation avec plus de détails
                const message = count === 'all' ?
                    `Affichage de tous les objectifs (${currentObjectives.length} objectifs)` :
                    `Filtrage appliqué: Top ${count} objectifs (${currentObjectives.length} affichés)`;

                // Créer une notification temporaire
                showNotification(message);
            }

            // Fonction pour afficher une notification
            function showNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-20 right-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg shadow-xl z-50 transform translate-x-full transition-all duration-300 flex items-center space-x-2';
                notification.innerHTML = `
                    <i class="fas fa-filter text-sm"></i>
                    <span class="font-medium">${message}</span>
                `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);

                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 2500);
            }

            // Fonction pour afficher les détails d'un objectif
            function showObjectiveDetails(objective) {
                const getPriorityColor = (priority) => {
                    switch(priority) {
                        case 'H': return ['text-red-600', 'bg-red-50', 'Haute'];
                        case 'M': return ['text-yellow-600', 'bg-yellow-50', 'Moyenne'];
                        case 'L': return ['text-green-600', 'bg-green-50', 'Basse'];
                        default: return ['text-gray-600', 'bg-gray-50', 'Non définie'];
                    }
                };

                const getDomainInfo = (objective) => {
                    const domain = objective.objective.substring(0, 3);
                    const domainInfo = {
                        'EDM': ['Évaluer, Diriger et Monitorer', 'bg-purple-100', 'text-purple-700'],
                        'APO': ['Aligner, Planifier et Organiser', 'bg-blue-100', 'text-blue-700'],
                        'BAI': ['Construire, Acquérir et Implémenter', 'bg-green-100', 'text-green-700'],
                        'DSS': ['Délivrer, Servir et Soutenir', 'bg-yellow-100', 'text-yellow-700'],
                        'MEA': ['Monitorer, Évaluer et Apprécier', 'bg-red-100', 'text-red-700']
                    };
                    return domainInfo[domain] || ['Domaine inconnu', 'bg-gray-100', 'text-gray-700'];
                };

                const [priorityColor, priorityBg, priorityLabel] = getPriorityColor(objective.priority);
                const [domainLabel, domainBg, domainColor] = getDomainInfo(objective);

                const modalHtml = `
                    <div class="fixed inset-0 bg-black bg-opacity-60 objective-modal flex items-center justify-center z-50">
                        <div class="bg-white rounded-xl p-8 max-w-2xl w-full mx-4 shadow-2xl transform transition-all duration-300 scale-95 hover:scale-100">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-14 h-14 ${domainBg} rounded-full flex items-center justify-center">
                                        <span class="text-2xl ${domainColor} font-bold">${objective.objective.substring(0, 3)}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-1">
                                            ${objective.objective}
                                        </h3>
                                        <p class="text-sm text-gray-500">${domainLabel}</p>
                                    </div>
                                </div>
                                <button onclick="this.closest('.objective-modal').remove()" 
                                        class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-xl">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="font-semibold text-gray-700">Score de Performance</h4>
                                        <span class="text-3xl font-bold ${objective.score >= 4 ? 'text-green-600' : objective.score >= 3 ? 'text-blue-600' : 'text-yellow-600'}">
                                            ${objective.score.toFixed(1)}/5
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-1000 ease-out"
                                             style="width: ${(objective.score / 5 * 100)}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>0</span>
                                        <span>1</span>
                                        <span>2</span>
                                        <span>3</span>
                                        <span>4</span>
                                        <span>5</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-6">
                                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                                        <h4 class="font-semibold text-gray-700 mb-3">Niveau de Priorité</h4>
                                        <span class="px-4 py-2 rounded-full ${priorityBg} ${priorityColor} text-sm font-semibold inline-block">
                                            ${priorityLabel}
                                        </span>
                                    </div>
                                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                                        <h4 class="font-semibold text-gray-700 mb-3">Écart à combler</h4>
                                        <div class="flex items-baseline space-x-2">
                                            <span class="text-2xl font-bold ${(5 - objective.score) > 2 ? 'text-red-600' : 'text-green-600'}">
                                                ${(5 - objective.score).toFixed(1)}
                                            </span>
                                            <span class="text-gray-500 text-sm">points</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button onclick="this.closest('.objective-modal').remove()" 
                                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg 
                                            hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                        Fermer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);

                // Animation d'entrée
                setTimeout(() => {
                    const modal = document.querySelector('.objective-modal > div');
                    if (modal) {
                        modal.classList.remove('scale-95');
                        modal.classList.add('scale-100');
                    }
                }, 50);
            }

            // Fonction d'initialisation du graphique des objectifs
            function initializeObjectiveChart() {
                // Initialiser le graphique
                createObjectiveChart(currentObjectives);

                // Initialiser le compteur d'objectifs
                document.getElementById('total-objectives').textContent = currentObjectives.length;

                // Marquer le bouton "Tous" comme actif par défaut
                const allButton = document.querySelector('button[onclick="filterObjectives(\'all\')"]');
                if (allButton) {
                    allButton.classList.remove('bg-gray-500');
                    allButton.classList.add('bg-blue-700', 'active');
                    allButton.style.boxShadow = '0 0 0 2px #93c5fd';
                }
            }
        }

        // Navigation
        function goHome() {
            window.location.href = '/cobit/home';
        }

        // Actions
        function exportPDF() {
            // Créer un formulaire pour l'export PDF
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/cobit/export/pdf';

            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function exportExcel() {
            // Créer un formulaire pour l'export Excel
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/cobit/export/excel';

            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function shareResults() {
            alert('Fonctionnalité de partage en cours de développement...');
        }

        function newEvaluation() {
            if (confirm('Commencer une nouvelle évaluation ? (Les données actuelles seront perdues)')) {
                window.location.href = '/cobit/home';
            }
        }



        // Générer les recommandations
        function generateRecommendations() {
            const recommendationsEl = document.getElementById('global-recommendations');
            const recommendations = [];

            const globalScore = finalData.designFactors.reduce((sum, df) => sum + df.score, 0) / finalData.designFactors.length;
            const lowScoreDFs = finalData.designFactors.filter(df => df.score < 2.5);
            const highScoreDFs = finalData.designFactors.filter(df => df.score >= 4);

            // Analyse globale
            if (globalScore >= 4) {
                recommendations.push({
                    icon: '🏆',
                    type: 'success',
                    title: 'Performance Exceptionnelle',
                    text: 'Votre organisation démontre une excellente maturité en gouvernance IT. Maintenez ces standards élevés et partagez les bonnes pratiques.'
                });
            } else if (globalScore >= 3) {
                recommendations.push({
                    icon: '📈',
                    type: 'improvement',
                    title: 'Bonne Performance Globale',
                    text: 'Votre gouvernance IT est bien établie. Concentrez-vous sur l\'optimisation continue et l\'innovation.'
                });
            } else if (globalScore >= 2) {
                recommendations.push({
                    icon: '⚠️',
                    type: 'warning',
                    title: 'Performance Modérée',
                    text: 'Des améliorations sont nécessaires dans plusieurs domaines. Priorisez les Design Factors critiques.'
                });
            } else {
                recommendations.push({
                    icon: '🚨',
                    type: 'critical',
                    title: 'Action Urgente Requise',
                    text: 'Votre gouvernance IT nécessite une refonte majeure. Élaborez un plan d\'amélioration immédiat.'
                });
            }

            // Recommandations spécifiques
            if (lowScoreDFs.length > 0) {
                recommendations.push({
                    icon: '🎯',
                    type: 'action',
                    title: 'Design Factors Prioritaires',
                    text: `Concentrez vos efforts sur : ${lowScoreDFs.map(df => df.code).join(', ')}. Ces domaines nécessitent une attention immédiate.`
                });
            }

            if (highScoreDFs.length > 0) {
                recommendations.push({
                    icon: '✅',
                    type: 'success',
                    title: 'Points Forts Identifiés',
                    text: `Excellente performance sur : ${highScoreDFs.map(df => df.code).join(', ')}. Utilisez ces succès comme modèles pour les autres domaines.`
                });
            }

            // Recommandations stratégiques
            recommendations.push({
                icon: '📋',
                type: 'strategy',
                title: 'Plan d\'Action Recommandé',
                text: '1. Établir un comité de gouvernance IT, 2. Définir des KPIs de suivi, 3. Planifier des revues trimestrielles, 4. Former les équipes sur COBIT 2019.'
            });

            // Afficher les recommandations
            recommendationsEl.innerHTML = '';
            recommendations.forEach(rec => {
                const div = document.createElement('div');
                div.className = `p-4 rounded-lg border-l-4 ${getRecommendationClass(rec.type)}`;
                div.innerHTML = `
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">${rec.icon}</span>
                        <div class="flex-1">
                            <h4 class="font-bold text-lg mb-2">${rec.title}</h4>
                            <p class="text-sm leading-relaxed">${rec.text}</p>
                        </div>
                    </div>
                `;
                recommendationsEl.appendChild(div);
            });
        }

        function getRecommendationClass(type) {
            switch(type) {
                case 'critical': return 'border-red-500 bg-red-50';
                case 'warning': return 'border-yellow-500 bg-yellow-50';
                case 'improvement': return 'border-blue-500 bg-blue-50';
                case 'success': return 'border-green-500 bg-green-50';
                case 'action': return 'border-purple-500 bg-purple-50';
                case 'strategy': return 'border-indigo-500 bg-indigo-50';
                default: return 'border-gray-500 bg-gray-50';
            }
        }

        // Générer le roadmap IA
        function generateRoadmap() {
            const roadmapSection = document.getElementById('roadmap-section');
            const roadmapContent = document.getElementById('roadmap-content');

            // Afficher la section
            roadmapSection.style.display = 'block';
            roadmapSection.scrollIntoView({ behavior: 'smooth' });

            // Simuler un chargement
            roadmapContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-purple-600 mb-4"></i>
                    <p class="text-lg text-gray-600">Génération du roadmap en cours...</p>
                </div>
            `;

            // Générer le roadmap après 2 secondes
            setTimeout(() => {
                const bestObjectives = @json($bestObjectives ?? []);
                const scoreGlobal = {{ $scoreGlobal ?? 3 }};

                let roadmapHTML = `
                    <div class="mb-6">
                        <h4 class="text-lg font-bold text-purple-700 mb-3">🎯 Plan de Mise en Œuvre Recommandé</h4>
                        <div class="bg-white rounded-lg p-4 border border-purple-200">
                            <p class="text-gray-700">Basé sur votre score global de <strong>${scoreGlobal.toFixed(1)}/5</strong> et vos ${bestObjectives.length} objectifs prioritaires.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Phase 1: Court terme (0-6 mois) -->
                        <div class="bg-white rounded-lg p-6 border-l-4 border-green-500">
                            <h5 class="font-bold text-green-700 mb-3 flex items-center">
                                <i class="fas fa-rocket mr-2"></i>Phase 1: Court terme
                            </h5>
                            <p class="text-sm text-gray-600 mb-3">0-6 mois</p>
                            <ul class="space-y-2 text-sm">
                `;

                // Ajouter les 3 premiers objectifs pour la phase 1
                bestObjectives.slice(0, 3).forEach((obj, index) => {
                    roadmapHTML += `
                        <li class="flex items-start">
                            <span class="bg-green-100 text-green-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2 mt-0.5">${index + 1}</span>
                            <div>
                                <strong>${obj.objective || 'N/A'}</strong>
                                <div class="text-xs text-gray-500">Priorité: ${obj.priority || 'M'} | Score: ${(obj.score || 0).toFixed(1)}</div>
                            </div>
                        </li>
                    `;
                });

                roadmapHTML += `
                            </ul>
                        </div>

                        <!-- Phase 2: Moyen terme (6-12 mois) -->
                        <div class="bg-white rounded-lg p-6 border-l-4 border-yellow-500">
                            <h5 class="font-bold text-yellow-700 mb-3 flex items-center">
                                <i class="fas fa-cogs mr-2"></i>Phase 2: Moyen terme
                            </h5>
                            <p class="text-sm text-gray-600 mb-3">6-12 mois</p>
                            <ul class="space-y-2 text-sm">
                `;

                // Ajouter les objectifs suivants pour la phase 2
                bestObjectives.slice(3, 6).forEach((obj, index) => {
                    roadmapHTML += `
                        <li class="flex items-start">
                            <span class="bg-yellow-100 text-yellow-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2 mt-0.5">${index + 4}</span>
                            <div>
                                <strong>${obj.objective || 'N/A'}</strong>
                                <div class="text-xs text-gray-500">Priorité: ${obj.priority || 'M'} | Score: ${(obj.score || 0).toFixed(1)}</div>
                            </div>
                        </li>
                    `;
                });

                roadmapHTML += `
                            </ul>
                        </div>

                        <!-- Phase 3: Long terme (12+ mois) -->
                        <div class="bg-white rounded-lg p-6 border-l-4 border-blue-500">
                            <h5 class="font-bold text-blue-700 mb-3 flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>Phase 3: Long terme
                            </h5>
                            <p class="text-sm text-gray-600 mb-3">12+ mois</p>
                            <ul class="space-y-2 text-sm">
                `;

                // Ajouter les objectifs restants pour la phase 3
                bestObjectives.slice(6).forEach((obj, index) => {
                    roadmapHTML += `
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2 mt-0.5">${index + 7}</span>
                            <div>
                                <strong>${obj.objective || 'N/A'}</strong>
                                <div class="text-xs text-gray-500">Priorité: ${obj.priority || 'M'} | Score: ${(obj.score || 0).toFixed(1)}</div>
                            </div>
                        </li>
                    `;
                });

                roadmapHTML += `
                            </ul>
                        </div>
                    </div>

                    <div class="mt-6 bg-gradient-to-r from-purple-100 to-blue-100 rounded-lg p-4">
                        <h5 class="font-bold text-purple-700 mb-2 flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>Recommandations IA
                        </h5>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Commencez par les objectifs à haute priorité pour un impact rapide</li>
                            <li>• Allouez 60% des ressources à la Phase 1, 30% à la Phase 2, 10% à la Phase 3</li>
                            <li>• Réévaluez le roadmap tous les 3 mois pour ajuster les priorités</li>
                            <li>• Impliquez les parties prenantes clés dès la Phase 1</li>
                        </ul>
                    </div>
                `;

                roadmapContent.innerHTML = roadmapHTML;
            }, 2000);
        }

        // Navigation
        function goHome() {
            window.location.href = '/cobit/home';
        }

        // Initialize variables for chart management
        let allObjectives = @json($bestObjectives ?? []);
        let currentObjectives = allObjectives;
        let objectiveChart = null;

        // Create or update the objective performance chart
        function createObjectiveChart(objectives) {
            const ctx = document.getElementById('objective-performance-chart');
            if (objectiveChart) {
                objectiveChart.destroy();
            }

            // Generate gradient colors for bars
            const colors = objectives.map((obj, index) => {
                const hue = (index * 360 / objectives.length) % 360;
                return {
                    background: `hsla(${hue}, 70%, 60%, 0.8)`,
                    border: `hsla(${hue}, 70%, 50%, 1)`,
                    hover: `hsla(${hue}, 70%, 50%, 0.9)`
                };
            });

            objectiveChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: objectives.map(obj => obj.objective),
                    datasets: [{
                        label: 'Score',
                        data: objectives.map(obj => obj.score),
                        backgroundColor: colors.map(c => c.background),
                        borderColor: colors.map(c => c.border),
                        borderWidth: 2,
                        hoverBackgroundColor: colors.map(c => c.hover),
                        barThickness: 'flex',
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const objective = objectives[context.dataIndex];
                                    return [
                                        `Score: ${objective.score.toFixed(1)}`,
                                        `Priorité: ${objective.priority || 'N/A'}`
                                    ];
                                }
                            }
                        }
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            showObjectiveDetails(objectives[index]);
                        }
                    }
                }
            });
        }

        // Filter objectives based on count
        function filterObjectives(count) {
            // Update active state of filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            const clickedButton = event.target;
            clickedButton.classList.add('active');

            // Apply filter
            if (count === 'all') {
                currentObjectives = allObjectives;
            } else {
                // Sort by score in descending order and take top N
                currentObjectives = [...allObjectives]
                    .sort((a, b) => b.score - a.score)
                    .slice(0, count);
            }

            // Update chart with animation
            createObjectiveChart(currentObjectives);

            // Update counter with animation
            const counter = document.getElementById('total-objectives');
            counter.style.transform = 'scale(1.1)';
            counter.textContent = currentObjectives.length;
            setTimeout(() => {
                counter.style.transform = 'scale(1)';
            }, 200);

            // Show notification
            showNotification(
                count === 'all' 
                    ? `Affichage de tous les objectifs (${currentObjectives.length} objectifs)`
                    : `Filtrage appliqué: Top ${count} objectifs (${currentObjectives.length} affichés)`
            );
        }

        // Show objective details in a modal
        function showObjectiveDetails(objective) {
            const getPriorityColor = (priority) => {
                switch(priority) {
                    case 'H': return ['text-red-600', 'bg-red-50', 'Haute'];
                    case 'M': return ['text-yellow-600', 'bg-yellow-50', 'Moyenne'];
                    case 'L': return ['text-green-600', 'bg-green-50', 'Basse'];
                    default: return ['text-gray-600', 'bg-gray-50', 'Non définie'];
                }
            };

            const getDomainInfo = (objective) => {
                const domain = objective.objective.substring(0, 3);
                const domainInfo = {
                    'EDM': ['Évaluer, Diriger et Monitorer', 'bg-purple-100', 'text-purple-700'],
                    'APO': ['Aligner, Planifier et Organiser', 'bg-blue-100', 'text-blue-700'],
                    'BAI': ['Construire, Acquérir et Implémenter', 'bg-green-100', 'text-green-700'],
                    'DSS': ['Délivrer, Servir et Soutenir', 'bg-yellow-100', 'text-yellow-700'],
                    'MEA': ['Monitorer, Évaluer et Apprécier', 'bg-red-100', 'text-red-700']
                };
                return domainInfo[domain] || ['Domaine inconnu', 'bg-gray-100', 'text-gray-700'];
            };

            const [priorityColor, priorityBg, priorityLabel] = getPriorityColor(objective.priority);
            const [domainLabel, domainBg, domainColor] = getDomainInfo(objective);

            const modalHtml = `
                <div class="fixed inset-0 bg-black bg-opacity-60 objective-modal flex items-center justify-center z-50">
                    <div class="bg-white rounded-xl p-8 max-w-2xl w-full mx-4 shadow-2xl transform transition-all duration-300 scale-95 hover:scale-100">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-14 h-14 ${domainBg} rounded-full flex items-center justify-center">
                                    <span class="text-2xl ${domainColor} font-bold">${objective.objective.substring(0, 3)}</span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-1">
                                        ${objective.objective}
                                    </h3>
                                    <p class="text-sm text-gray-500">${domainLabel}</p>
                                </div>
                            </div>
                            <button onclick="this.closest('.objective-modal').remove()" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-semibold text-gray-700">Score de Performance</h4>
                                    <span class="text-3xl font-bold ${objective.score >= 4 ? 'text-green-600' : objective.score >= 3 ? 'text-blue-600' : 'text-yellow-600'}">
                                        ${objective.score.toFixed(1)}/5
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-1000 ease-out"
                                         style="width: ${(objective.score / 5 * 100)}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>0</span>
                                    <span>1</span>
                                    <span>2</span>
                                    <span>3</span>
                                    <span>4</span>
                                    <span>5</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                                    <h4 class="font-semibold text-gray-700 mb-3">Niveau de Priorité</h4>
                                    <span class="px-4 py-2 rounded-full ${priorityBg} ${priorityColor} text-sm font-semibold inline-block">
                                        ${priorityLabel}
                                    </span>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                                    <h4 class="font-semibold text-gray-700 mb-3">Écart à combler</h4>
                                    <div class="flex items-baseline space-x-2">
                                        <span class="text-2xl font-bold ${(5 - objective.score) > 2 ? 'text-red-600' : 'text-green-600'}">
                                            ${(5 - objective.score).toFixed(1)}
                                        </span>
                                        <span class="text-gray-500 text-sm">points</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button onclick="this.closest('.objective-modal').remove()" 
                                        class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg 
                                        hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Animation d'entrée
            setTimeout(() => {
                const modal = document.querySelector('.objective-modal > div');
                if (modal) {
                    modal.classList.remove('scale-95');
                    modal.classList.add('scale-100');
                }
            }, 50);
        }

        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', () => {
            createObjectiveChart(allObjectives);
            document.getElementById('total-objectives').textContent = allObjectives.length;
        });
    </script>
</body>
</html>
