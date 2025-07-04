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
        .kmpg-blue { color: #00338D; }
        .kmpg-bg { background-color: #00338D; }
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
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="kmpg-bg text-white shadow-lg">
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
                <h2 class="text-4xl font-bold kmpg-blue mb-4">
                    <i class="fas fa-trophy mr-3 text-yellow-500"></i>
                    Résultats Finaux COBIT 2019
                </h2>
                <p class="text-xl text-gray-600">Analyse complète de votre évaluation des 10 Design Factors</p>
            </div>

            <!-- Métriques Globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-blue-600 mb-2" id="global-score">0.0</div>
                    <div class="text-sm text-gray-600">Score Global</div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" id="global-progress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-green-600 mb-2" id="maturity-level">0</div>
                    <div class="text-sm text-gray-600">Niveau de Maturité</div>
                    <div class="mt-2 text-xs text-gray-500" id="maturity-description">-</div>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg card-hover">
                    <div class="text-4xl font-bold text-purple-600 mb-2" id="total-objectives">0</div>
                    <div class="text-sm text-gray-600">Objectifs Impactés</div>
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
                    <h3 class="text-xl font-bold kmpg-blue mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Performance par Design Factor
                    </h3>
                    <div class="relative h-80">
                        <canvas id="df-performance-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tableau de Bord des DF -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 card-hover">
                <h3 class="text-xl font-bold kmpg-blue mb-6 flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    Tableau de Bord des Design Factors
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DF</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité IA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Objectifs</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="df-summary-table">
                            <!-- Contenu généré dynamiquement -->
                        </tbody>
                    </table>
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
                {
                    code: '{{ $df->code }}',
                    title: '{{ $df->title }}',
                    number: {{ $df->getNumberFromCode() }},
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
            generateDFTable();
            generateRecommendations();
        });

        // Calculer les métriques globales
        function calculateGlobalMetrics() {
            const scores = finalData.designFactors.map(df => df.score);
            const globalScore = scores.reduce((a, b) => a + b, 0) / scores.length;
            const maturityLevel = Math.round(globalScore);
            const totalObjectives = finalData.designFactors.reduce((sum, df) => sum + df.objectives, 0);
            
            document.getElementById('global-score').textContent = globalScore.toFixed(1);
            document.getElementById('global-progress').style.width = (globalScore / 5 * 100) + '%';
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
                type: 'radar',
                data: {
                    labels: finalData.designFactors.map(df => df.code),
                    datasets: [{
                        label: 'Scores Finaux',
                        data: finalData.designFactors.map(df => df.score),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(59, 130, 246)'
                    }, {
                        label: 'Baseline (2.5)',
                        data: new Array(10).fill(2.5),
                        backgroundColor: 'rgba(156, 163, 175, 0.1)',
                        borderColor: 'rgb(156, 163, 175)',
                        borderWidth: 1,
                        pointBackgroundColor: 'rgb(156, 163, 175)',
                        pointBorderColor: '#fff'
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

            // Graphique de Performance par DF
            const barCtx = document.getElementById('df-performance-chart');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: finalData.designFactors.map(df => df.code),
                    datasets: [{
                        label: 'Score',
                        data: finalData.designFactors.map(df => df.score),
                        backgroundColor: finalData.designFactors.map(df => 
                            df.score >= 4 ? 'rgba(16, 185, 129, 0.8)' :
                            df.score >= 3 ? 'rgba(59, 130, 246, 0.8)' :
                            df.score >= 2 ? 'rgba(245, 158, 11, 0.8)' :
                            'rgba(239, 68, 68, 0.8)'
                        ),
                        borderColor: finalData.designFactors.map(df => 
                            df.score >= 4 ? 'rgb(16, 185, 129)' :
                            df.score >= 3 ? 'rgb(59, 130, 246)' :
                            df.score >= 2 ? 'rgb(245, 158, 11)' :
                            'rgb(239, 68, 68)'
                        ),
                        borderWidth: 1
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
                        title: {
                            display: true,
                            text: 'Scores par Design Factor'
                        }
                    }
                }
            });
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

        // Générer le tableau des DF
        function generateDFTable() {
            const tableBody = document.getElementById('df-summary-table');
            tableBody.innerHTML = '';

            finalData.designFactors.forEach(df => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';

                const statusClass = df.score >= 4 ? 'bg-green-100 text-green-800' :
                                   df.score >= 3 ? 'bg-blue-100 text-blue-800' :
                                   df.score >= 2 ? 'bg-yellow-100 text-yellow-800' :
                                   'bg-red-100 text-red-800';

                const statusText = df.score >= 4 ? 'Excellent' :
                                  df.score >= 3 ? 'Bon' :
                                  df.score >= 2 ? 'Moyen' :
                                  'Faible';

                const priorityClass = df.score >= 4 ? 'text-green-600' :
                                     df.score >= 3 ? 'text-blue-600' :
                                     df.score >= 2 ? 'text-yellow-600' :
                                     'text-red-600';

                const priority = df.score >= 4 ? 'Maintenir' :
                                df.score >= 3 ? 'Surveiller' :
                                df.score >= 2 ? 'Améliorer' :
                                'Critique';

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${df.code}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${df.title}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">${df.score.toFixed(1)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ${priorityClass}">${priority}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${df.objectives}</td>
                `;

                tableBody.appendChild(row);
            });
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
    </script>
</body>
</html>
