<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $designFactor->title }} - {{ $evaluation->nom_entreprise }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .kpmg-blue { color: #00338D; }
        .kpmg-bg { background-color: #00338D; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .df-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .df-circle.completed { background-color: #10b981; color: white; }
        .df-circle.current { background-color: #3b82f6; color: white; }
        .df-circle.pending { background-color: #e5e7eb; color: #6b7280; }
        .df-circle:hover { transform: scale(1.1); }

        /* Animations pour les graphiques */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .chart-container {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .chart-container:nth-child(2) {
            animation-delay: 0.2s;
        }
        .chart-container:nth-child(3) {
            animation-delay: 0.4s;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="kpmg-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cobit.home') }}" class="text-white hover:text-blue-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div class="bg-white p-2 rounded">
                        <svg width="40" height="20" viewBox="0 0 200 100" xmlns="http://www.w3.org/2000/svg">
                            <text x="10" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#00338D">KPMG</text>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">{{ $evaluation->nom_entreprise }}</h1>
                        <p class="text-blue-200 text-sm">{{ $evaluation->taille_entreprise }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-200">Design Factor {{ $dfNumber }}/10</div>
                    <div class="text-lg font-bold">{{ $designFactor->title }}</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation par cercles -->
    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-center space-x-3">
                @for($i = 1; $i <= 10; $i++)
                    <div class="df-circle 
                        @if($evaluation->isDFCompleted($i)) completed
                        @elseif($i == $dfNumber) current
                        @else pending
                        @endif"
                        onclick="navigateToDF({{ $i }})"
                        title="Design Factor {{ $i }}">
                        {{ $i }}
                    </div>
                @endfor
            </div>
            <div class="text-center mt-2">
                <div class="text-sm text-gray-600">
                    {{ $evaluation->getCompletedDFsCount() }}/10 Design Factors complétés
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2 max-w-md mx-auto">
                    <div class="bg-blue-600 h-2 rounded-full transition-all"
                         style="width: {{ $evaluation->getProgressPercentage() }}%"></div>
                </div>

                @php
                    $currentDFData = $evaluation->getDFData($dfNumber);
                    $isAIGenerated = $currentDFData && isset($currentDFData['ai_generated']) && $currentDFData['ai_generated'];
                @endphp

                @if($isAIGenerated)
                <div class="mt-3 max-w-md mx-auto">
                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-3">
                        <div class="flex items-center justify-center space-x-2 text-sm">
                            <div class="w-6 h-6 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-white text-xs"></i>
                            </div>
                            <span class="text-purple-700 font-medium">Paramètres pré-remplis par l'IA COBIT</span>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                        <div class="text-xs text-purple-600 text-center mt-1">
                            Basé sur l'analyse de vos documents • Modifiable à tout moment
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- En-tête du DF -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 kpmg-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold text-2xl">{{ $dfNumber }}</span>
                    </div>
                    <h2 class="text-3xl font-bold kpmg-blue mb-2">{{ $designFactor->title }}</h2>
                    <p class="text-gray-600 text-lg">{{ $designFactor->description }}</p>
                </div>
            </div>

            <!-- Formulaire d'évaluation -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold mb-6">Évaluez chaque objectif COBIT (1-5)</h3>
                
                <form id="dfForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($objectives as $index => $objective)
                        <div class="border rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $objective }}
                            </label>
                            <div class="flex space-x-2">
                                @for($value = 1; $value <= 5; $value++)
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="objective_{{ $index }}" 
                                           value="{{ $value }}"
                                           @if(isset($dfData['inputs'][$index]) && $dfData['inputs'][$index] == $value) checked @endif
                                           class="sr-only">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center cursor-pointer hover:border-blue-500 transition-colors radio-button"
                                         data-value="{{ $value }}">
                                        <span class="text-sm font-bold">{{ $value }}</span>
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t">
                        <div class="flex space-x-4">
                            @if($dfNumber > 1)
                            <button type="button" onclick="navigateToDF({{ $dfNumber - 1 }})" 
                                    class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>DF Précédent
                            </button>
                            @endif
                        </div>

                        <div class="flex space-x-4">
                            <button type="button" onclick="saveDFData()" 
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Sauvegarder
                            </button>
                            
                            @if($dfNumber < 10)
                            <button type="button" onclick="saveAndNext()" 
                                    class="kpmg-bg text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-arrow-right mr-2"></i>Suivant
                            </button>
                            @else
                            <button type="button" onclick="saveAndShowCanvas()" 
                                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-chart-area mr-2"></i>Afficher Canvas
                            </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section Graphiques Interactifs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
            <!-- Graphique Radar -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover chart-container">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold kmpg-blue flex items-center">
                        <i class="fas fa-chart-area mr-2"></i>
                        Vue d'ensemble - Radar
                    </h3>
                    <div class="text-sm text-gray-600">
                        <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></span>
                        Temps réel
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="radar-chart"></canvas>
                </div>
                <div class="mt-4 text-xs text-gray-500 text-center">
                    Comparaison par domaine COBIT (EDM, APO, BAI, DSS, MEA)
                </div>
            </div>

            <!-- Graphique en Barres -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover chart-container">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold kmpg-blue flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Scores par Domaine
                    </h3>
                    <div class="text-sm text-gray-600">
                        <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                        Interactif
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="bar-chart"></canvas>
                </div>
                <div class="mt-4 text-xs text-gray-500 text-center">
                    Scores actuels par domaine de gouvernance IT
                </div>
            </div>
        </div>

        <!-- Section Objectifs COBIT -->
        <div class="mt-8 chart-container">
            <div id="objectives-container">
                <!-- Le contenu sera généré dynamiquement par JavaScript -->
            </div>
        </div>
    </div>

    <!-- Données pour JavaScript -->
    <div style="display: none;">
        <div data-evaluation-id="{{ $evaluation->id }}"></div>
        <div data-df-number="{{ $dfNumber }}"></div>
    </div>

    <script>
        // Variables globales
        const evaluationId = {{ $evaluation->id }};
        const currentDF = {{ $dfNumber }};

        // Initialiser les boutons radio
        document.addEventListener('DOMContentLoaded', function() {
            initializeRadioButtons();
        });

        function initializeRadioButtons() {
            document.querySelectorAll('.radio-button').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input[type="radio"]');
                    input.checked = true;
                    updateRadioButtonStyles(input.name);
                });
            });

            // Mettre à jour les styles initiaux
            document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
                updateRadioButtonStyles(input.name);
            });
        }

        function updateRadioButtonStyles(name) {
            document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
                const button = input.parentElement.querySelector('.radio-button');
                if (input.checked) {
                    button.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                    button.classList.remove('border-gray-300');
                } else {
                    button.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                    button.classList.add('border-gray-300');
                }
            });
        }

        function collectFormData() {
            const inputs = [];
            @foreach($objectives as $index => $objective)
            const objective{{ $index }} = document.querySelector('input[name="objective_{{ $index }}"]:checked');
            inputs[{{ $index }}] = objective{{ $index }} ? parseInt(objective{{ $index }}.value) : 0;
            @endforeach
            return inputs;
        }

        function saveDFData() {
            const inputs = collectFormData();
            
            fetch('/cobit/evaluation/save-df', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    evaluation_id: evaluationId,
                    df_number: currentDF,
                    inputs: inputs
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Données sauvegardées avec succès');
                    updateProgressDisplay(data.completed_dfs);
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }

        function saveAndNext() {
            const inputs = collectFormData();
            
            fetch('/cobit/evaluation/save-df', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    evaluation_id: evaluationId,
                    df_number: currentDF,
                    inputs: inputs
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    navigateToDF(currentDF + 1);
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }

        function saveAndShowCanvas() {
            const inputs = collectFormData();
            
            fetch('/cobit/evaluation/save-df', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    evaluation_id: evaluationId,
                    df_number: currentDF,
                    inputs: inputs
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = `/cobit/evaluation/${evaluationId}/canvas`;
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }

        function navigateToDF(dfNumber) {
            if (dfNumber >= 1 && dfNumber <= 10) {
                window.location.href = `/cobit/evaluation/${evaluationId}/df/${dfNumber}`;
            }
        }

        function updateProgressDisplay(completedDFs) {
            // Mettre à jour l'affichage de progression si nécessaire
            const progressText = document.querySelector('.text-sm.text-gray-600');
            if (progressText) {
                progressText.textContent = `${completedDFs}/10 Design Factors complétés`;
            }
        }
    </script>

    <!-- Inclure le script des graphiques interactifs -->
    <script src="{{ asset('js/cobit-interactive-charts.js') }}"></script>

    <script>
        // Initialiser les graphiques interactifs après le chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Déclencher une mise à jour initiale des graphiques
            updateChartsWithCurrentData();

            // Ajouter des écouteurs d'événements pour les changements d'inputs
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', function() {
                    // Mettre à jour les graphiques en temps réel
                    updateChartsWithCurrentData();
                });
            });
        });

        // Mettre à jour les graphiques avec les données actuelles
        function updateChartsWithCurrentData() {
            const inputs = collectFormData();

            // Envoyer les données à l'API pour recalcul
            fetch('/cobit/api/update-inputs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    df_number: currentDF,
                    inputs: inputs
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✅ Données recalculées pour les graphiques:', data);

                    // Déclencher l'événement de mise à jour des graphiques
                    const event = new CustomEvent('cobitDataUpdate', {
                        detail: data
                    });
                    window.dispatchEvent(event);
                }
            })
            .catch(error => {
                console.error('❌ Erreur lors du recalcul pour les graphiques:', error);
            });
        }
    </script>
</body>
</html>
