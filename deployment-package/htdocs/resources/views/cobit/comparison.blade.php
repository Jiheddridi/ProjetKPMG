<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparaison Évaluations COBIT 2019</title>
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
        .comparison-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-balance-scale mr-3"></i>
                        Comparaison Évaluations COBIT 2019
                    </h1>
                    <p class="text-blue-100">Analyse comparative avec IA Ollama</p>
                </div>
                <div class="text-right">
                    <button onclick="goHome()" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition-colors">
                        <i class="fas fa-home mr-2"></i>Retour Accueil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Sélection des évaluations -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold kmpg-blue mb-6 flex items-center">
                <i class="fas fa-check-square mr-2"></i>
                Sélectionner les Évaluations à Comparer (2-5 évaluations)
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($evaluations as $evaluation)
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" class="evaluation-checkbox mr-3" value="{{ $evaluation->id }}" 
                               data-name="{{ $evaluation->nom_entreprise }}"
                               data-score="{{ $evaluation->score_global }}"
                               data-maturity="{{ round($evaluation->score_global) }}">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">{{ $evaluation->nom_entreprise }}</div>
                            <div class="text-sm text-gray-600">{{ $evaluation->taille_entreprise }}</div>
                            <div class="text-sm text-blue-600">
                                Score: {{ number_format($evaluation->score_global, 1) }}/5 | 
                                Maturité: {{ round($evaluation->score_global) }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $evaluation->updated_at->format('d/m/Y') }}</div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span id="selected-count">0</span> évaluation(s) sélectionnée(s)
                </div>
                <button id="compare-btn" onclick="startComparison()" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-chart-line mr-2"></i>Comparer avec IA Ollama
                </button>
            </div>
        </div>

        <!-- Zone de comparaison (cachée par défaut) -->
        <div id="comparison-results" class="hidden">
            <!-- Analyse IA Ollama -->
            <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-purple-700 mb-6 flex items-center">
                    <i class="fas fa-robot mr-2"></i>
                    Analyse Comparative IA Ollama
                </h3>
                <div id="ai-analysis-content">
                    <!-- Contenu généré par Ollama -->
                </div>
            </div>

            <!-- Tableau de comparaison -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold kmpg-blue mb-6 flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    Tableau Comparatif
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="comparison-table">
                        <!-- Tableau généré dynamiquement -->
                    </table>
                </div>
            </div>

            <!-- Graphiques de comparaison -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold kmpg-blue mb-4">Comparaison Scores Globaux</h3>
                    <canvas id="scores-chart"></canvas>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold kmpg-blue mb-4">Niveaux de Maturité</h3>
                    <canvas id="maturity-chart"></canvas>
                </div>
            </div>

            <!-- Recommandation finale IA -->
            <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-green-700 mb-6 flex items-center">
                    <i class="fas fa-trophy mr-2"></i>
                    Recommandation Finale IA
                </h3>
                <div id="final-recommendation">
                    <!-- Recommandation finale -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedEvaluations = [];
        let comparisonCharts = {};

        // Gestion de la sélection des évaluations
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.evaluation-checkbox');
            const compareBtn = document.getElementById('compare-btn');
            const selectedCount = document.getElementById('selected-count');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelection();
                });
            });

            function updateSelection() {
                const checked = document.querySelectorAll('.evaluation-checkbox:checked');
                selectedCount.textContent = checked.length;
                
                // Activer le bouton si 2-5 évaluations sélectionnées
                if (checked.length >= 2 && checked.length <= 5) {
                    compareBtn.disabled = false;
                } else {
                    compareBtn.disabled = true;
                }

                // Mettre à jour la liste des évaluations sélectionnées
                selectedEvaluations = Array.from(checked).map(cb => ({
                    id: parseInt(cb.value),
                    name: cb.dataset.name,
                    score: parseFloat(cb.dataset.score),
                    maturity: parseInt(cb.dataset.maturity)
                }));
            }
        });

        // Démarrer la comparaison
        function startComparison() {
            if (selectedEvaluations.length < 2) {
                alert('Veuillez sélectionner au moins 2 évaluations');
                return;
            }

            // Valider les IDs
            const validIds = selectedEvaluations
                .map(e => e.id)
                .filter(id => id && !isNaN(id) && id > 0);

            if (validIds.length < 2) {
                alert('Erreur: IDs d\'évaluation invalides');
                return;
            }

            console.log('IDs sélectionnés:', validIds); // Debug

            // Afficher la zone de résultats
            document.getElementById('comparison-results').classList.remove('hidden');
            document.getElementById('comparison-results').scrollIntoView({ behavior: 'smooth' });

            // Afficher le chargement
            showLoadingState();

            // Appeler l'API de comparaison
            fetch('/cobit/comparison/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    evaluation_ids: validIds
                })
            })
            .then(response => {
                console.log('Réponse reçue:', response.status); // Debug
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data); // Debug
                if (data.success) {
                    displayComparisonResults(data.comparison);
                } else {
                    displayError(data.message || 'Erreur inconnue');
                }
            })
            .catch(error => {
                console.error('Erreur complète:', error);
                displayError(`Erreur lors de la comparaison: ${error.message}`);
            });
        }

        // Afficher l'état de chargement
        function showLoadingState() {
            document.getElementById('ai-analysis-content').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-robot text-6xl text-purple-600 mb-4 animate-pulse"></i>
                    <p class="text-xl text-gray-700 mb-2">🤖 IA Ollama analyse les évaluations...</p>
                    <p class="text-sm text-gray-500">Comparaison des scores, maturité, objectifs et recommandations</p>
                    <div class="mt-4">
                        <div class="w-64 mx-auto bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full animate-pulse" style="width: 70%"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Afficher les résultats de comparaison
        function displayComparisonResults(comparison) {
            // Afficher l'analyse IA
            displayAIAnalysis(comparison.ai_analysis);
            
            // Créer le tableau comparatif
            createComparisonTable(comparison.evaluations);
            
            // Créer les graphiques
            createComparisonCharts(comparison.evaluations);
            
            // Afficher la recommandation finale
            displayFinalRecommendation(comparison.recommendation);
        }

        // Afficher l'analyse IA
        function displayAIAnalysis(analysis) {
            const content = document.getElementById('ai-analysis-content');

            content.innerHTML = `
                <div class="space-y-6">
                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-bold text-purple-700 mb-2">📊 Résumé Exécutif</h4>
                        <p class="text-gray-700">${analysis.comparative_analysis?.summary || 'Analyse comparative des évaluations COBIT'}</p>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-bold text-purple-700 mb-2">🎯 Benchmarking</h4>
                        <p class="text-gray-700">${analysis.comparative_analysis?.benchmarking || 'Positionnement relatif des entreprises'}</p>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-bold text-purple-700 mb-2">💼 Impact Stratégique</h4>
                        <p class="text-gray-700">${analysis.comparative_analysis?.strategic_impact || 'Impact business des niveaux de maturité'}</p>
                    </div>

                    ${analysis.ranking ? `
                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-bold text-purple-700 mb-3">🏆 Classement IA</h4>
                        <div class="space-y-2">
                            ${analysis.ranking.map(rank => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">${rank.position}</span>
                                        <div>
                                            <div class="font-semibold">${rank.company}</div>
                                            <div class="text-sm text-gray-600">${rank.justification}</div>
                                        </div>
                                    </div>
                                    <div class="text-lg font-bold text-blue-600">${rank.score}/5</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
        }

        // Créer le tableau comparatif
        function createComparisonTable(evaluations) {
            const table = document.getElementById('comparison-table');

            const headers = ['Critère', ...evaluations.map(e => e.nom_entreprise)];
            const rows = [
                ['Score Global', ...evaluations.map(e => `${e.score_global}/5`)],
                ['Niveau Maturité', ...evaluations.map(e => `${e.maturity_level} - ${getMaturityLabel(e.maturity_level)}`)],
                ['Taille Entreprise', ...evaluations.map(e => e.taille_entreprise || 'N/A')],
                ['Objectifs Complétés', ...evaluations.map(e => (e.best_objectives?.length || 0) + ' objectifs')],
                ['Dernière MAJ', ...evaluations.map(e => e.updated_at || 'N/A')]
            ];

            table.innerHTML = `
                <thead class="bg-gray-50">
                    <tr>
                        ${headers.map(h => `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${h}</th>`).join('')}
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${rows.map(row => `
                        <tr>
                            ${row.map((cell, index) => `
                                <td class="px-6 py-4 whitespace-nowrap text-sm ${index === 0 ? 'font-medium text-gray-900' : 'text-gray-500'}">${cell}</td>
                            `).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            `;
        }

        // Créer les graphiques de comparaison
        function createComparisonCharts(evaluations) {
            // Graphique des scores
            const scoresCtx = document.getElementById('scores-chart');
            comparisonCharts.scores = new Chart(scoresCtx, {
                type: 'bar',
                data: {
                    labels: evaluations.map(e => e.nom_entreprise),
                    datasets: [{
                        label: 'Score Global',
                        data: evaluations.map(e => e.score_global),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5
                        }
                    }
                }
            });

            // Graphique des niveaux de maturité
            const maturityCtx = document.getElementById('maturity-chart');
            comparisonCharts.maturity = new Chart(maturityCtx, {
                type: 'doughnut',
                data: {
                    labels: evaluations.map(e => e.nom_entreprise),
                    datasets: [{
                        data: evaluations.map(e => e.maturity_level),
                        backgroundColor: [
                            '#EF4444', '#F97316', '#EAB308', '#22C55E', '#3B82F6'
                        ].slice(0, evaluations.length)
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Afficher la recommandation finale
        function displayFinalRecommendation(recommendation) {
            const content = document.getElementById('final-recommendation');

            content.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-white rounded-lg p-6 border-l-4 border-green-500">
                        <h4 class="text-xl font-bold text-green-700 mb-2">🏆 Meilleure Entreprise</h4>
                        <p class="text-2xl font-bold text-gray-900 mb-2">${recommendation.best_company}</p>
                        <p class="text-gray-700">${recommendation.why_best}</p>
                    </div>

                    <div class="bg-white rounded-lg p-6">
                        <h4 class="font-bold text-green-700 mb-3">📋 Prochaines Étapes Recommandées</h4>
                        <ul class="space-y-2">
                            ${(recommendation.next_steps || []).map(step => `
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                    <span class="text-gray-700">${step}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg p-6">
                        <h4 class="font-bold text-green-700 mb-2">⚠️ Mitigation des Risques</h4>
                        <p class="text-gray-700">${recommendation.risk_mitigation}</p>
                    </div>
                </div>
            `;
        }

        // Utilitaires
        function getMaturityLabel(level) {
            const labels = {
                1: 'Initial',
                2: 'Géré',
                3: 'Défini',
                4: 'Quantitatif',
                5: 'Optimisé'
            };
            return labels[level] || 'Non défini';
        }

        function displayError(message) {
            document.getElementById('ai-analysis-content').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-6xl text-red-600 mb-4"></i>
                    <p class="text-xl text-red-700 mb-2">Erreur d'Analyse</p>
                    <p class="text-sm text-gray-600">${message}</p>
                </div>
            `;
        }

        // Navigation
        function goHome() {
            window.location.href = '/cobit/home';
        }
    </script>
</body>
</html>
