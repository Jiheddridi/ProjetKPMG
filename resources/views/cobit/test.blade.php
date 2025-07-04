<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBIT 2019 - Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-cogs mr-3 text-blue-600"></i>
                COBIT 2019 - Évaluation des Design Factors
            </h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($designFactors as $df)
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-6 border border-blue-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $df->code }}</h3>
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                            {{ $df->getNumberFromCode() }}
                        </span>
                    </div>
                    
                    <h4 class="text-md font-medium text-gray-800 mb-2">{{ $df->title }}</h4>
                    <p class="text-sm text-gray-600 mb-4">{{ $df->description }}</p>
                    
                    <div class="space-y-3">
                        @foreach($df->parameters as $index => $param)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $param['label'] }}
                                <span class="text-blue-600 font-bold ml-2" id="value-{{ $df->code }}-{{ $index }}">
                                    {{ $param['default'] ?? 0 }}
                                </span>
                            </label>
                            <input 
                                type="range" 
                                min="{{ $param['min'] ?? 0 }}" 
                                max="{{ $param['max'] ?? 5 }}" 
                                value="{{ $param['default'] ?? 0 }}"
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                id="input-{{ $df->code }}-{{ $index }}"
                                data-df="{{ $df->getNumberFromCode() }}"
                                data-index="{{ $index }}"
                                oninput="updateValue('{{ $df->code }}', {{ $index }}, this.value)"
                            >
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <div class="grid grid-cols-2 gap-2 text-center">
                            <div class="bg-white rounded p-2">
                                <div class="text-lg font-bold text-blue-600" id="score-{{ $df->code }}">0.0</div>
                                <div class="text-xs text-gray-600">Score</div>
                            </div>
                            <div class="bg-white rounded p-2">
                                <div class="text-lg font-bold text-green-600" id="status-{{ $df->code }}">-</div>
                                <div class="text-xs text-gray-600">Statut</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Section IA et Résultats -->
            <div class="mt-8 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-brain mr-3 text-purple-600"></i>
                    Analyse IA et Résultats
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Métriques Globales</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Score Global:</span>
                                <span class="font-bold text-blue-600" id="global-score">0.0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Complétude:</span>
                                <span class="font-bold text-green-600" id="global-completion">0%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">DF Validés:</span>
                                <span class="font-bold text-purple-600" id="validated-count">0/10</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Recommandations IA</h3>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-700" id="ai-recommendation">
                                Commencez par ajuster les paramètres des Design Factors...
                            </div>
                            <div class="mt-3">
                                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">
                                    Priorité: <span id="ai-priority">Moyenne</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Graphique Radar</h3>
                        <canvas id="radar-chart" width="200" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <button onclick="saveAll()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Sauvegarder Tout
                </button>
                <button onclick="resetAll()" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                    <i class="fas fa-undo mr-2"></i>Réinitialiser
                </button>
                <button onclick="exportResults()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Exporter
                </button>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let evaluationData = {};
        let radarChart = null;
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeData();
            createRadarChart();
            updateGlobalMetrics();
        });
        
        // Initialiser les données
        function initializeData() {
            @foreach($designFactors as $df)
            evaluationData['{{ $df->code }}'] = {
                inputs: [
                    @foreach($df->parameters as $param)
                    {{ $param['default'] ?? 0 }},
                    @endforeach
                ],
                score: 0
            };
            @endforeach
        }
        
        // Mettre à jour une valeur
        function updateValue(dfCode, index, value) {
            document.getElementById(`value-${dfCode}-${index}`).textContent = value;
            evaluationData[dfCode].inputs[index] = parseFloat(value);
            
            // Calculer le score
            const score = evaluationData[dfCode].inputs.reduce((a, b) => a + b, 0) / evaluationData[dfCode].inputs.length;
            evaluationData[dfCode].score = score;
            
            // Mettre à jour l'affichage
            document.getElementById(`score-${dfCode}`).textContent = score.toFixed(1);
            document.getElementById(`status-${dfCode}`).textContent = score > 3 ? 'Bon' : score > 2 ? 'Moyen' : 'Faible';
            
            updateGlobalMetrics();
            updateRadarChart();
        }
        
        // Mettre à jour les métriques globales
        function updateGlobalMetrics() {
            const scores = Object.values(evaluationData).map(df => df.score);
            const globalScore = scores.reduce((a, b) => a + b, 0) / scores.length;
            const validatedCount = scores.filter(score => score > 2.5).length;
            const completion = Math.round((scores.filter(score => score > 0).length / scores.length) * 100);
            
            document.getElementById('global-score').textContent = globalScore.toFixed(1);
            document.getElementById('global-completion').textContent = completion + '%';
            document.getElementById('validated-count').textContent = `${validatedCount}/10`;
            
            // Recommandation IA
            let recommendation = '';
            if (completion < 50) {
                recommendation = '🔍 Complétez l\'évaluation de plus de Design Factors';
            } else if (globalScore < 2) {
                recommendation = '⚠️ Scores critiques détectés. Améliorations nécessaires.';
            } else if (globalScore > 4) {
                recommendation = '✅ Excellente performance ! Maintenez ces standards.';
            } else {
                recommendation = '📈 Performance correcte. Identifiez les améliorations.';
            }
            
            document.getElementById('ai-recommendation').textContent = recommendation;
            document.getElementById('ai-priority').textContent = globalScore < 2 ? 'Haute' : globalScore > 4 ? 'Basse' : 'Moyenne';
        }
        
        // Créer le graphique radar
        function createRadarChart() {
            const ctx = document.getElementById('radar-chart');
            radarChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: [
                        @foreach($designFactors as $df)
                        '{{ $df->code }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Scores DF',
                        data: Object.values(evaluationData).map(df => df.score),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 5
                        }
                    }
                }
            });
        }
        
        // Mettre à jour le graphique radar
        function updateRadarChart() {
            if (radarChart) {
                radarChart.data.datasets[0].data = Object.values(evaluationData).map(df => df.score);
                radarChart.update();
            }
        }
        
        // Fonctions d'action
        function saveAll() {
            alert('Données sauvegardées ! (Fonctionnalité à implémenter)');
        }
        
        function resetAll() {
            if (confirm('Réinitialiser toutes les données ?')) {
                location.reload();
            }
        }
        
        function exportResults() {
            alert('Export en cours... (Fonctionnalité à implémenter)');
        }
    </script>
</body>
</html>
