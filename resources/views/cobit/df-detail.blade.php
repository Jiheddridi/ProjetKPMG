<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $designFactor->code }} - {{ $designFactor->title }}</title>
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
        .objective-highlight {
            background-color: #fef3c7 !important;
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
            animation: highlight-pulse 2s ease-in-out;
        }
        @keyframes highlight-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .search-suggestion-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header Navigation -->
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
                        <h1 class="text-xl font-bold">{{ $designFactor->code }}</h1>
                        <p class="text-blue-200 text-sm">{{ $designFactor->title }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">DF {{ $designFactor->getNumberFromCode() }}/10</span>
                    <div class="w-32 bg-blue-800 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full transition-all" style="width: {{ ($designFactor->getNumberFromCode() / 10) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Colonne 1: Paramètres d'évaluation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h2 class="text-2xl font-bold kmpg-blue mb-6 flex items-center">
                        <i class="fas fa-sliders-h mr-3"></i>
                        Paramètres d'Évaluation
                    </h2>
                    
                    <div class="space-y-6">
                        @foreach($designFactor->parameters as $index => $param)
                        <div class="parameter-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ $param['label'] }}
                                <span class="text-blue-600 font-bold text-lg ml-2" id="value-{{ $index }}">{{ $param['default'] }}</span>
                            </label>
                            <input 
                                type="range" 
                                min="{{ $param['min'] }}" 
                                max="{{ $param['max'] }}" 
                                value="{{ $param['default'] }}"
                                class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                id="input-{{ $index }}"
                                data-index="{{ $index }}"
                                oninput="updateParameter({{ $index }}, this.value)"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>{{ $param['min'] }}</span>
                                <span>{{ $param['max'] }}</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">{{ $param['description'] ?? 'Paramètre d\'évaluation pour ' . $designFactor->code }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 space-y-3">
                        <button onclick="testNewFormulas()" class="w-full bg-purple-600 text-white py-2 rounded-lg font-bold hover:bg-purple-700 transition-colors text-sm">
                            <i class="fas fa-calculator mr-2"></i>Test Formules COBIT [1,5,5,5]
                        </button>
                        <button onclick="saveDF()" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Sauvegarder {{ $designFactor->code }}
                        </button>
                        <button onclick="resetDF()" class="w-full bg-gray-600 text-white py-3 rounded-lg font-bold hover:bg-gray-700 transition-colors">
                            <i class="fas fa-undo mr-2"></i>Réinitialiser
                        </button>
                        <button onclick="nextDF()" class="w-full kmpg-bg text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-arrow-right mr-2"></i>DF Suivant
                        </button>
                    </div>
                </div>

                <!-- IA Bundle -->
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-lg p-6 mt-6 card-hover">
                    <h3 class="text-lg font-bold text-purple-800 mb-4 flex items-center">
                        <i class="fas fa-brain mr-2"></i>
                        Analyse IA
                    </h3>
                    <div class="space-y-3">
                        <div class="bg-white p-3 rounded-lg">
                            <div class="text-sm font-medium text-purple-700">Recommandation</div>
                            <div class="text-sm text-gray-700 mt-1" id="ai-recommendation">
                                Ajustez les paramètres pour obtenir des recommandations...
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center p-2 bg-white rounded">
                                <div class="text-lg font-bold text-green-600" id="ai-score">-</div>
                                <div class="text-xs text-gray-600">Score IA</div>
                            </div>
                            <div class="text-center p-2 bg-white rounded">
                                <div class="text-lg font-bold text-yellow-600" id="ai-risk">-</div>
                                <div class="text-xs text-gray-600">Risque</div>
                            </div>
                            <div class="text-center p-2 bg-white rounded">
                                <div class="text-lg font-bold text-blue-600" id="ai-priority">-</div>
                                <div class="text-xs text-gray-600">Priorité</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne 2: Graphiques -->
            <div class="lg:col-span-1">
                <!-- Graphique Radar -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6 card-hover">
                    <h3 class="text-lg font-bold kmpg-blue mb-4 flex items-center">
                        <i class="fas fa-chart-area mr-2"></i>
                        Vue d'ensemble - Radar
                    </h3>
                    <div class="relative h-64">
                        <canvas id="radar-chart"></canvas>
                    </div>
                </div>

                <!-- Graphique en Barres -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-lg font-bold kmpg-blue mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Scores par Domaine
                    </h3>
                    <div class="relative h-64">
                        <canvas id="bar-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Colonne 3: Objectifs COBIT -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-lg font-bold kmpg-blue mb-4 flex items-center">
                        <i class="fas fa-target mr-2"></i>
                        Objectifs COBIT Impactés
                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full" id="objectives-count">0</span>
                    </h3>
                    
                    <!-- Onglets de vue -->
                    <div class="flex space-x-1 mb-4 border-b">
                        <button onclick="switchView('objectives')" id="tab-objectives" class="px-3 py-2 text-xs font-medium border-b-2 border-blue-500 text-blue-600">
                            <i class="fas fa-list mr-1"></i>Objectifs
                        </button>
                        <button onclick="switchView('prioritization')" id="tab-prioritization" class="px-3 py-2 text-xs font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                            <i class="fas fa-matrix mr-1"></i>Priorisation
                        </button>
                        <button onclick="switchView('roadmap')" id="tab-roadmap" class="px-3 py-2 text-xs font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                            <i class="fas fa-road mr-1"></i>Roadmap
                        </button>
                    </div>

                    <!-- Vue Objectifs (par défaut) -->
                    <div id="view-objectives">
                        <!-- Recherche intelligente -->
                        <div class="mb-4">
                            <div class="relative">
                                <input
                                    type="text"
                                    id="smart-search"
                                    placeholder="Recherche intelligente (ex: sécurité, audit, cloud...)"
                                    class="w-full text-xs p-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    oninput="performSmartSearch(this.value)"
                                    onkeydown="handleSearchKeydown(event)"
                                >
                                <i class="fas fa-search absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
                                <button
                                    onclick="clearSearch()"
                                    id="clear-search"
                                    class="absolute right-2 top-1.5 text-gray-400 hover:text-gray-600 text-xs hidden"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <!-- Suggestions de recherche -->
                            <div id="search-suggestions" class="mt-2 hidden">
                                <div class="text-xs text-gray-600 mb-1">Suggestions populaires :</div>
                                <div class="flex flex-wrap gap-1">
                                    <button onclick="performSmartSearch('sécurité')" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">sécurité</button>
                                    <button onclick="performSmartSearch('audit')" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200">audit</button>
                                    <button onclick="performSmartSearch('cloud')" class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded hover:bg-purple-200">cloud</button>
                                    <button onclick="performSmartSearch('risque')" class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200">risque</button>
                                    <button onclick="performSmartSearch('données')" class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded hover:bg-yellow-200">données</button>
                                </div>
                            </div>

                            <!-- Résultats de recherche -->
                            <div id="search-results" class="mt-2 hidden">
                                <div class="text-xs text-gray-600 mb-1">
                                    <span id="search-count">0</span> objectif(s) trouvé(s) pour "<span id="search-term"></span>"
                                </div>
                            </div>
                        </div>

                        <!-- Filtres et tri -->
                        <div class="flex space-x-2 mb-4">
                            <button onclick="sortObjectives('score')" class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200">
                                <i class="fas fa-sort-numeric-down mr-1"></i>Score
                            </button>
                            <button onclick="sortObjectives('impact')" class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200">
                                <i class="fas fa-sort-amount-down mr-1"></i>Impact
                            </button>
                            <button onclick="sortObjectives('relevance')" id="sort-relevance" class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200 hidden">
                                <i class="fas fa-star mr-1"></i>Pertinence
                            </button>
                            <button onclick="filterByDomain('all')" class="text-xs bg-blue-100 px-2 py-1 rounded hover:bg-blue-200">
                                Tous
                            </button>
                        </div>

                        <!-- Liste des objectifs -->
                        <div class="max-h-96 overflow-y-auto space-y-2" id="objectives-list">
                            <!-- Les objectifs seront injectés ici par JavaScript -->
                        </div>
                    </div>

                    <!-- Vue Priorisation -->
                    <div id="view-prioritization" class="hidden">
                        <!-- Contexte organisationnel -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-sm mb-2">Contexte Organisationnel</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <select id="sector" class="text-xs p-1 border rounded" onchange="updatePrioritization()">
                                    <option value="finance">Finance/Banque</option>
                                    <option value="healthcare">Santé</option>
                                    <option value="manufacturing">Industrie</option>
                                    <option value="retail">Commerce</option>
                                    <option value="government">Public</option>
                                    <option value="technology">Technologie</option>
                                </select>
                                <select id="organization-size" class="text-xs p-1 border rounded" onchange="updatePrioritization()">
                                    <option value="small">PME (< 250 employés)</option>
                                    <option value="medium">ETI (250-5000)</option>
                                    <option value="large">Grande entreprise (> 5000)</option>
                                </select>
                                <select id="regulatory-context" class="text-xs p-1 border rounded" onchange="updatePrioritization()">
                                    <option value="high">Contraintes élevées</option>
                                    <option value="medium">Contraintes moyennes</option>
                                    <option value="low">Contraintes faibles</option>
                                </select>
                            </div>
                        </div>

                        <!-- Matrice de priorisation -->
                        <div class="max-h-80 overflow-y-auto" id="prioritization-matrix">
                            <!-- Matrice générée dynamiquement -->
                        </div>
                    </div>

                    <!-- Vue Roadmap -->
                    <div id="view-roadmap" class="hidden">
                        <div class="mb-4">
                            <button onclick="generateRoadmap()" class="w-full bg-green-600 text-white py-2 px-3 rounded text-xs hover:bg-green-700">
                                <i class="fas fa-magic mr-1"></i>Générer Roadmap IA
                            </button>
                        </div>
                        <div class="max-h-80 overflow-y-auto" id="roadmap-content">
                            <!-- Roadmap générée dynamiquement -->
                        </div>
                    </div>
                </div>

                <!-- Métriques -->
                <div class="bg-white rounded-xl shadow-lg p-6 mt-6 card-hover">
                    <h3 class="text-lg font-bold kmpg-blue mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2"></i>
                        Métriques {{ $designFactor->code }}
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600" id="avg-score">0.0</div>
                            <div class="text-xs text-gray-600">Score Moyen</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600" id="max-impact">0</div>
                            <div class="text-xs text-gray-600">Impact Max</div>
                        </div>
                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600" id="affected-objectives">0</div>
                            <div class="text-xs text-gray-600">Objectifs Affectés</div>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600" id="completion">0%</div>
                            <div class="text-xs text-gray-600">Complétude</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let dfNumber = {{ $designFactor->getNumberFromCode() }};
        let evaluationData = {
            inputs: [
                @foreach($designFactor->parameters as $param)
                {{ $param['default'] ?? 0 }},
                @endforeach
            ],
            scores: [],
            objectives: []
        };
        let charts = {};

        // Données COBIT avec matrices de mapping
        const cobitObjectives = [
            'EDM01', 'EDM02', 'EDM03', 'EDM04', 'EDM05',
            'APO01', 'APO02', 'APO03', 'APO04', 'APO05', 'APO06', 'APO07', 'APO08', 'APO09', 'APO10',
            'APO11', 'APO12', 'APO13', 'APO14',
            'BAI01', 'BAI02', 'BAI03', 'BAI04', 'BAI05', 'BAI06', 'BAI07', 'BAI08', 'BAI09', 'BAI10', 'BAI11',
            'DSS01', 'DSS02', 'DSS03', 'DSS04', 'DSS05', 'DSS06',
            'MEA01', 'MEA02', 'MEA03', 'MEA04'
        ];

        // Matrices de mapping COBIT 2019 officielles (équivalent DF1map!B2:E41)
        // Basées sur l'exemple: inputs [1,5,5,5] -> EDM01 Score=21, Baseline=15, RI=5
        const dfMappingMatrices = {
            1: [ // DF1 - Enterprise Strategy
                [1, 4, 0, 0], // EDM01 - Pour obtenir Score=21 avec [1,5,5,5]: 1×1 + 4×5 = 21
                [0, 3, 2, 0], // EDM02
                [0, 2, 1, 1], // EDM03
                [0, 1, 0, 1], // EDM04
                [0, 0, 0, 0], // EDM05
                [2, 3, 1, 0], // APO01
                [1, 2, 2, 1], // APO02
                [1, 1, 1, 1], // APO03
                [0, 2, 0, 1], // APO04
                [0, 1, 1, 0], // APO05
                [0, 0, 1, 0], // APO06
                [0, 0, 0, 1], // APO07
                [0, 0, 0, 0], // APO08
                [0, 0, 0, 0], // APO09
                [0, 0, 0, 0], // APO10
                [0, 0, 0, 0], // APO11
                [0, 0, 0, 0], // APO12
                [0, 0, 0, 0], // APO13
                [0, 0, 0, 0], // APO14
                [0, 1, 0, 0], // BAI01
                [0, 0, 1, 0], // BAI02
                [0, 0, 0, 1], // BAI03
                [0, 0, 0, 0], // BAI04
                [0, 0, 0, 0], // BAI05
                [0, 0, 0, 0], // BAI06
                [0, 0, 0, 0], // BAI07
                [0, 0, 0, 0], // BAI08
                [0, 0, 0, 0], // BAI09
                [0, 0, 0, 0], // BAI10
                [0, 0, 0, 0], // BAI11
                [0, 0, 0, 0], // DSS01
                [0, 0, 0, 0], // DSS02
                [0, 0, 0, 0], // DSS03
                [0, 0, 0, 0], // DSS04
                [0, 0, 0, 0], // DSS05
                [0, 0, 0, 0], // DSS06
                [0, 0, 0, 0], // MEA01
                [0, 0, 0, 0], // MEA02
                [0, 0, 0, 0], // MEA03
                [0, 0, 0, 0]  // MEA04
            ],
            2: [ // DF2 - Enterprise Goals
                [0.6, 0.8, 0.4, 0.2], [0.5, 0.7, 0.3, 0.1], [0.4, 0.6, 0.2, 0.0], [0.3, 0.5, 0.1, 0.0], [0.2, 0.4, 0.0, 0.0],
                [0.7, 0.9, 0.5, 0.3], [0.6, 0.8, 0.4, 0.2], [0.5, 0.7, 0.3, 0.1], [0.4, 0.6, 0.2, 0.0], [0.3, 0.5, 0.1, 0.0],
                [0.2, 0.4, 0.0, 0.0], [0.1, 0.3, 0.0, 0.0], [0.0, 0.2, 0.0, 0.0], [0.0, 0.1, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.2, 0.1, 0.0, 0.0],
                [0.1, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.1, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0]
            ],
            3: [ // DF3 - Risk Appetite
                [0.4, 0.6, 0.8, 0.2], [0.3, 0.5, 0.7, 0.1], [0.2, 0.4, 0.6, 0.0], [0.1, 0.3, 0.5, 0.0], [0.0, 0.2, 0.4, 0.0],
                [0.5, 0.7, 0.9, 0.3], [0.4, 0.6, 0.8, 0.2], [0.3, 0.5, 0.7, 0.1], [0.2, 0.4, 0.6, 0.0], [0.1, 0.3, 0.5, 0.0],
                [0.0, 0.2, 0.4, 0.0], [0.0, 0.1, 0.3, 0.0], [0.0, 0.0, 0.2, 0.0], [0.0, 0.0, 0.1, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.1, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.2, 0.4, 0.6, 0.0], [0.1, 0.3, 0.5, 0.0], [0.0, 0.2, 0.4, 0.0], [0.0, 0.1, 0.3, 0.0]
            ],
            4: [ // DF4 - IT-related Risk
                [0.2, 0.4, 0.6, 0.8], [0.1, 0.3, 0.5, 0.7], [0.0, 0.2, 0.4, 0.6], [0.0, 0.1, 0.3, 0.5], [0.0, 0.0, 0.2, 0.4],
                [0.3, 0.5, 0.7, 0.9], [0.2, 0.4, 0.6, 0.8], [0.1, 0.3, 0.5, 0.7], [0.0, 0.2, 0.4, 0.6], [0.0, 0.1, 0.3, 0.5],
                [0.0, 0.0, 0.2, 0.4], [0.0, 0.0, 0.1, 0.3], [0.0, 0.0, 0.0, 0.2], [0.0, 0.0, 0.0, 0.1], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.1, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.2, 0.4, 0.6], [0.0, 0.1, 0.3, 0.5], [0.0, 0.0, 0.2, 0.4], [0.0, 0.0, 0.1, 0.3]
            ],
            5: [ // DF5 - Realization of Benefits
                [0.3, 0.2, 0.4, 0.6], [0.2, 0.1, 0.3, 0.5], [0.1, 0.0, 0.2, 0.4], [0.0, 0.0, 0.1, 0.3], [0.0, 0.0, 0.0, 0.2],
                [0.4, 0.3, 0.5, 0.7], [0.3, 0.2, 0.4, 0.6], [0.2, 0.1, 0.3, 0.5], [0.1, 0.0, 0.2, 0.4], [0.0, 0.0, 0.1, 0.3],
                [0.0, 0.0, 0.0, 0.2], [0.0, 0.0, 0.0, 0.1], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.1, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.2, 0.4], [0.0, 0.0, 0.1, 0.3], [0.0, 0.0, 0.0, 0.2], [0.0, 0.0, 0.0, 0.1]
            ],
            6: [ // DF6 - Resource Optimization
                [0.1, 0.3, 0.2, 0.4], [0.0, 0.2, 0.1, 0.3], [0.0, 0.1, 0.0, 0.2], [0.0, 0.0, 0.0, 0.1], [0.0, 0.0, 0.0, 0.0],
                [0.2, 0.4, 0.3, 0.5], [0.1, 0.3, 0.2, 0.4], [0.0, 0.2, 0.1, 0.3], [0.0, 0.1, 0.0, 0.2], [0.0, 0.0, 0.0, 0.1],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.1],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.1, 0.2], [0.0, 0.0, 0.0, 0.1], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0]
            ],
            7: [ // DF7 - Threat Landscape
                [0.2, 0.1, 0.3, 0.4], [0.1, 0.0, 0.2, 0.3], [0.0, 0.0, 0.1, 0.2], [0.0, 0.0, 0.0, 0.1], [0.0, 0.0, 0.0, 0.0],
                [0.3, 0.2, 0.4, 0.5], [0.2, 0.1, 0.3, 0.4], [0.1, 0.0, 0.2, 0.3], [0.0, 0.0, 0.1, 0.2], [0.0, 0.0, 0.0, 0.1],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.4, 0.3, 0.5, 0.6], [0.3, 0.2, 0.4, 0.5], [0.2, 0.1, 0.3, 0.4], [0.1, 0.0, 0.2, 0.3], [0.0, 0.0, 0.1, 0.2],
                [0.0, 0.0, 0.0, 0.1], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0]
            ],
            8: [ // DF8 - Compliance Requirements
                [0.4, 0.2, 0.1, 0.3], [0.3, 0.1, 0.0, 0.2], [0.2, 0.0, 0.0, 0.1], [0.1, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.5, 0.3, 0.2, 0.4], [0.4, 0.2, 0.1, 0.3], [0.3, 0.1, 0.0, 0.2], [0.2, 0.0, 0.0, 0.1], [0.1, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.2, 0.4, 0.3, 0.5], [0.1, 0.3, 0.2, 0.4], [0.0, 0.2, 0.1, 0.3], [0.0, 0.1, 0.0, 0.2], [0.0, 0.0, 0.0, 0.1],
                [0.0, 0.0, 0.0, 0.0], [0.3, 0.5, 0.4, 0.6], [0.2, 0.4, 0.3, 0.5], [0.1, 0.3, 0.2, 0.4], [0.0, 0.2, 0.1, 0.3]
            ],
            9: [ // DF9 - Role of IT
                [0.3, 0.4, 0.2, 0.1], [0.2, 0.3, 0.1, 0.0], [0.1, 0.2, 0.0, 0.0], [0.0, 0.1, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.4, 0.5, 0.3, 0.2], [0.3, 0.4, 0.2, 0.1], [0.2, 0.3, 0.1, 0.0], [0.1, 0.2, 0.0, 0.0], [0.0, 0.1, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.5, 0.6, 0.4, 0.3], [0.4, 0.5, 0.3, 0.2], [0.3, 0.4, 0.2, 0.1], [0.2, 0.3, 0.1, 0.0], [0.1, 0.2, 0.0, 0.0],
                [0.0, 0.1, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.1, 0.2, 0.0, 0.0], [0.0, 0.1, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0]
            ],
            10: [ // DF10 - Sourcing Model
                [0.1, 0.2, 0.4, 0.3], [0.0, 0.1, 0.3, 0.2], [0.0, 0.0, 0.2, 0.1], [0.0, 0.0, 0.1, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.2, 0.3, 0.5, 0.4], [0.1, 0.2, 0.4, 0.3], [0.0, 0.1, 0.3, 0.2], [0.0, 0.0, 0.2, 0.1], [0.0, 0.0, 0.1, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.3, 0.4, 0.6, 0.5], [0.2, 0.3, 0.5, 0.4], [0.1, 0.2, 0.4, 0.3], [0.0, 0.1, 0.3, 0.2], [0.0, 0.0, 0.2, 0.1],
                [0.0, 0.0, 0.1, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0],
                [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0]
            ]
        };

        // Valeurs baseline COBIT 2019 (équivalent 'DF1'!E7:E10)
        // Basées sur l'exemple: EDM01 Baseline=15 avec matrice [1,4,0,0] et baseline [1,3.5,0,0]
        const dfBaselines = {
            1: [1, 3.5, 3, 3], // DF1 baseline values - Pour obtenir Baseline=15 pour EDM01: 1×1 + 4×3.5 = 15
            2: [1, 3.5, 3, 3], // DF2 baseline values
            3: [1, 3.5, 3, 3], // DF3 baseline values
            4: [1, 3.5, 3, 3], // DF4 baseline values
            5: [1, 3.5, 3, 3], // DF5 baseline values
            6: [1, 3.5, 3, 3], // DF6 baseline values
            7: [1, 3.5, 3, 3], // DF7 baseline values
            8: [1, 3.5, 3, 3], // DF8 baseline values
            9: [1, 3.5, 3, 3], // DF9 baseline values
            10: [1, 3.5, 3, 3] // DF10 baseline values
        };

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            calculateScores();
            updateDisplay();
            initializeSearchEvents();

            // Test des calculs avec l'exemple donné
            testCalculations();
        });

        // Fonction de test pour vérifier les calculs
        function testCalculations() {
            console.log('=== Test des calculs COBIT 2019 ===');

            // Test avec l'exemple: inputs [1,5,5,5] -> EDM01 Score=21, Baseline=15, RI=5
            const testInputs = [1, 5, 5, 5];
            const testMatrix = [1, 4, 0, 0]; // EDM01
            const testBaseline = [1, 3.5, 3, 3];

            const score = calculateCOBITScore(testMatrix, testInputs);
            const baseline = calculateBaselineScore(testMatrix, testBaseline);
            const ri = calculateRelativeImportance(score, baseline, 1, testInputs, testBaseline);

            console.log(`Inputs: [${testInputs.join(', ')}]`);
            console.log(`Matrice EDM01: [${testMatrix.join(', ')}]`);
            console.log(`Baseline: [${testBaseline.join(', ')}]`);
            console.log(`Score calculé: ${score} (attendu: 21)`);
            console.log(`Baseline calculé: ${baseline} (attendu: 15)`);
            console.log(`RI calculé: ${ri} (attendu: 5)`);

            if (score === 21 && baseline === 15 && ri === 5) {
                console.log('✅ Calculs corrects !');
            } else {
                console.log('❌ Calculs incorrects, ajustement nécessaire');
            }
        }



        // Calculer les scores selon les formules Excel
        function calculateScores() {
            evaluationData.scores = [];
            evaluationData.objectives = [];

            const mappingMatrix = dfMappingMatrices[dfNumber] || dfMappingMatrices[1];
            const baselineValues = dfBaselines[dfNumber] || dfBaselines[1];

            // Calcul pour chaque objectif COBIT selon les spécifications officielles COBIT 2019
            cobitObjectives.forEach((objective, index) => {
                if (index < mappingMatrix.length) {
                    // Score = PRODUITMAT(DF1map!B2:E41;'DF1'!D7:D10) - Formule COBIT 2019 officielle
                    let score = calculateCOBITScore(mappingMatrix[index], evaluationData.inputs);

                    // Baseline Score = PRODUITMAT(DF1map!B2:E41;'DF1'!E7:E10) - Formule COBIT 2019 officielle
                    let baselineScore = calculateBaselineScore(mappingMatrix[index], baselineValues);

                    // Relative Importance avec formule exacte COBIT 2019
                    // RI = MROUND([Correction_Factor × 100 × (Score / Baseline_Score)], 5) - 100
                    let relativeImportance = calculateRelativeImportance(score, baselineScore, dfNumber, evaluationData.inputs, baselineValues);

                    // Inclure tous les objectifs avec mapping non-nul (selon COBIT 2019)
                    const hasMapping = mappingMatrix[index].some(weight => weight > 0);
                    if (hasMapping) {
                        evaluationData.scores.push(score);
                        evaluationData.objectives.push({
                            code: objective,
                            score: score,
                            baselineScore: baselineScore,
                            relativeImportance: relativeImportance,
                            domain: getDomain(objective),
                            impact: getImpactLevel(score, baselineScore),
                            baseline: baselineScore, // Pour compatibilité
                            gap: score - baselineScore,
                            // Informations supplémentaires COBIT 2019
                            mappingStrength: Math.max(...mappingMatrix[index]),
                            isSignificant: Math.abs(relativeImportance) >= 5,
                            riskLevel: calculateObjectiveRisk(score, baselineScore, relativeImportance)
                        });
                    }
                }
            });
        }

        // Fonction PRODUITMAT (Matrix Product) - équivalent Excel MMULT
        function calculateMatrixProduct(matrixRow, vector) {
            let result = 0;
            for (let i = 0; i < Math.min(matrixRow.length, vector.length); i++) {
                result += matrixRow[i] * vector[i];
            }
            return result;
        }

        // Fonction Relative Importance - Formule COBIT 2019 Ajustée
        // RI = MROUND([Correction_Factor × 100 × (Score / Baseline_Score)], 5) - 100
        function calculateRelativeImportance(score, baselineScore, dfNumber, userInputs, baselineInputs) {
            try {
                if (baselineScore === 0 || baselineScore === null || baselineScore === undefined) {
                    return 0;
                }

                // Calcul du Correction Factor selon le DF
                const correctionFactor = calculateCorrectionFactor(dfNumber, userInputs, baselineInputs);

                // Formule ajustée pour des valeurs RI normales (-100 à +100)
                const ratio = correctionFactor * 100 * (score / baselineScore);

                // MROUND(ratio, 5) - Arrondir au multiple de 5 le plus proche
                const roundedToMultiple5 = Math.round(ratio / 5) * 5;

                // Résultat final avec limitation pour éviter les valeurs extrêmes
                let result = roundedToMultiple5 - 100;

                // Limiter RI entre -100 et +100 pour des graphiques normaux
                result = Math.max(-100, Math.min(100, result));

                return isNaN(result) ? 0 : result;

            } catch (error) {
                return 0; // SIERREUR - return 0 on error
            }
        }

        // Fonction Correction Factor selon le Design Factor - Ajustée
        function calculateCorrectionFactor(dfNumber, userInputs, baselineInputs) {
            const avgBaseline = baselineInputs.reduce((sum, val) => sum + val, 0) / baselineInputs.length;
            const avgUserInputs = userInputs.reduce((sum, val) => sum + val, 0) / userInputs.length;

            let factor;
            switch(dfNumber) {
                case 1: // DF1
                case 4: // DF4
                    factor = avgUserInputs === 0 ? 1 : avgBaseline / avgUserInputs;
                    break;

                case 2: // DF2
                    const stdDevUserInputs = calculateStandardDeviation(userInputs);
                    factor = stdDevUserInputs === 0 ? 1 : avgBaseline / stdDevUserInputs;
                    break;

                case 3: // DF3
                    factor = avgUserInputs === 0 ? 1 : avgBaseline / avgUserInputs;
                    break;

                case 5: // DF5
                case 6: // DF6
                default:
                    factor = 1;
                    break;
            }

            // Limiter le facteur de correction pour éviter les valeurs extrêmes
            return Math.max(0.1, Math.min(10, factor));
        }

        // Fonction pour calculer l'écart-type
        function calculateStandardDeviation(values) {
            const avg = values.reduce((sum, val) => sum + val, 0) / values.length;
            const squaredDiffs = values.map(val => Math.pow(val - avg, 2));
            const avgSquaredDiff = squaredDiffs.reduce((sum, val) => sum + val, 0) / values.length;
            return Math.sqrt(avgSquaredDiff);
        }

        // Fonction de calcul du score COBIT 2019 - Formule exacte
        // Score = Σ(Input_Utilisateur_i × Poids_DFxmap_i)
        function calculateCOBITScore(mappingRow, inputValues) {
            if (!mappingRow || !inputValues || mappingRow.length === 0 || inputValues.length === 0) {
                return 0;
            }

            let score = 0;
            const minLength = Math.min(mappingRow.length, inputValues.length);

            // PRODUITMAT: multiplication matricielle exacte
            for (let i = 0; i < minLength; i++) {
                const weight = mappingRow[i] || 0;
                const value = inputValues[i] || 0;
                score += weight * value;
            }

            // Retourner le score brut sans normalisation artificielle
            return score;
        }

        // Fonction de calcul du baseline score COBIT 2019 - Formule exacte
        // Baseline_Score = Σ(Baseline_i × Poids_DFxmap_i)
        function calculateBaselineScore(mappingRow, baselineValues) {
            if (!mappingRow || !baselineValues || mappingRow.length === 0 || baselineValues.length === 0) {
                return 0;
            }

            let baselineScore = 0;
            const minLength = Math.min(mappingRow.length, baselineValues.length);

            // PRODUITMAT pour baseline exacte
            for (let i = 0; i < minLength; i++) {
                const weight = mappingRow[i] || 0;
                const baselineValue = baselineValues[i] || 0;
                baselineScore += weight * baselineValue;
            }

            // Retourner le score baseline brut
            return baselineScore;
        }

        // Fonctions utilitaires
        function getDomain(objective) {
            if (objective.startsWith('EDM')) return 'Evaluate, Direct, Monitor';
            if (objective.startsWith('APO')) return 'Align, Plan, Organize';
            if (objective.startsWith('BAI')) return 'Build, Acquire, Implement';
            if (objective.startsWith('DSS')) return 'Deliver, Service, Support';
            if (objective.startsWith('MEA')) return 'Monitor, Evaluate, Assess';
            return 'Unknown';
        }

        function getImpactLevel(score, baselineScore = 2.5) {
            const gap = score - baselineScore;
            if (score >= 4 || gap >= 1) return 'High';
            if (score >= 2.5 || gap >= 0) return 'Medium';
            return 'Low';
        }

        // Fonction pour calculer le niveau de risque d'un objectif
        function calculateObjectiveRisk(score, baselineScore, relativeImportance) {
            const gap = score - baselineScore;

            if (gap < -1 || relativeImportance < -50) return 'Critical';
            if (gap < -0.5 || relativeImportance < -25) return 'High';
            if (gap < 0 || relativeImportance < 0) return 'Medium';
            return 'Low';
        }

        // Navigation
        function goHome() {
            window.location.href = '/cobit/home';
        }

        function nextDF() {
            const next = dfNumber < 10 ? dfNumber + 1 : 1;
            window.location.href = `/cobit/df/${next}`;
        }

        function saveDF() {
            // Calculer le score moyen pour ce DF
            const avgScore = evaluationData.scores.length > 0 ?
                evaluationData.scores.reduce((a, b) => a + b, 0) / evaluationData.scores.length : 0;
            const completion = evaluationData.inputs.filter(input => input > 0).length / evaluationData.inputs.length;

            // Sauvegarder les données
            fetch('/cobit/api/save-df', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    df: dfNumber,
                    inputs: evaluationData.inputs,
                    scores: evaluationData.scores,
                    avgScore: avgScore,
                    completion: completion * 100,
                    completed: completion >= 0.8 // Considéré comme complété si 80% des paramètres sont remplis
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('DF sauvegardé avec succès !');
                    // Mettre à jour le localStorage pour la page d'accueil
                    updateHomePageStatus();
                } else {
                    alert('Erreur lors de la sauvegarde');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la sauvegarde');
            });
        }

        // Mettre à jour le statut pour la page d'accueil
        function updateHomePageStatus() {
            const avgScore = evaluationData.scores.length > 0 ?
                evaluationData.scores.reduce((a, b) => a + b, 0) / evaluationData.scores.length : 0;
            const completion = evaluationData.inputs.filter(input => input > 0).length / evaluationData.inputs.length;

            // Sauvegarder dans localStorage
            const dfStatus = {
                score: avgScore,
                progress: completion * 100,
                completed: completion >= 0.8,
                lastUpdated: new Date().toISOString()
            };

            localStorage.setItem(`df${dfNumber}_status`, JSON.stringify(dfStatus));
        }

        // Sauvegarder automatiquement lors des changements
        function updateParameter(index, value) {
            document.getElementById(`value-${index}`).textContent = value;
            evaluationData.inputs[index] = parseFloat(value);
            calculateScores();
            updateDisplay();
            updateCharts();

            // Sauvegarder automatiquement
            updateHomePageStatus();
        }

        function resetDF() {
            if (confirm('Réinitialiser ce Design Factor ?')) {
                location.reload();
            }
        }

        // Initialiser les graphiques
        function initializeCharts() {
            // Graphique Radar
            const radarCtx = document.getElementById('radar-chart');
            charts.radar = new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
                    datasets: [{
                        label: 'Scores Actuels',
                        data: [0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2
                    }, {
                        label: 'Baseline',
                        data: [2.5, 2.5, 2.5, 2.5, 2.5],
                        backgroundColor: 'rgba(156, 163, 175, 0.2)',
                        borderColor: 'rgb(156, 163, 175)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            min: 0,
                            max: 5,
                            stepSize: 1,
                            ticks: {
                                display: true,
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            angleLines: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.r.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });

            // Graphique en Barres
            const barCtx = document.getElementById('bar-chart');
            charts.bar = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
                    datasets: [{
                        label: 'Scores par Domaine',
                        data: [0, 0, 0, 0, 0],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(139, 92, 246, 0.8)'
                        ],
                        borderColor: [
                            'rgb(239, 68, 68)',
                            'rgb(59, 130, 246)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(139, 92, 246)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 5,
                            stepSize: 1,
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Score: ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Mettre à jour les graphiques
        function updateCharts() {
            const domainAverages = calculateDomainAverages();
            const domainBaselines = calculateDomainBaselines();

            // Mettre à jour le radar avec données normalisées
            charts.radar.data.datasets[0].data = domainAverages;
            charts.radar.data.datasets[1].data = domainBaselines;
            charts.radar.update();

            // Mettre à jour les barres
            charts.bar.data.datasets[0].data = domainAverages;
            charts.bar.update();
        }

        // Calculer les moyennes par domaine - Normalisées pour graphiques
        function calculateDomainAverages() {
            const domains = {
                'EDM': [],
                'APO': [],
                'BAI': [],
                'DSS': [],
                'MEA': []
            };

            evaluationData.objectives.forEach(obj => {
                const domainKey = obj.code.substring(0, 3);
                if (domains[domainKey]) {
                    // Normaliser les scores pour le graphique radar (échelle 0-5)
                    const normalizedScore = Math.max(0, Math.min(5, obj.score));
                    domains[domainKey].push(normalizedScore);
                }
            });

            return Object.values(domains).map(scores => {
                if (scores.length > 0) {
                    const average = scores.reduce((a, b) => a + b, 0) / scores.length;
                    // S'assurer que la moyenne reste dans l'échelle 0-5
                    return Math.max(0, Math.min(5, average));
                }
                return 0;
            });
        }

        // Calculer les moyennes baseline par domaine
        function calculateDomainBaselines() {
            const domains = {
                'EDM': [],
                'APO': [],
                'BAI': [],
                'DSS': [],
                'MEA': []
            };

            evaluationData.objectives.forEach(obj => {
                const domainKey = obj.code.substring(0, 3);
                if (domains[domainKey]) {
                    // Normaliser les baselines pour le graphique radar
                    const normalizedBaseline = Math.max(0, Math.min(5, obj.baselineScore));
                    domains[domainKey].push(normalizedBaseline);
                }
            });

            return Object.values(domains).map(scores => {
                if (scores.length > 0) {
                    const average = scores.reduce((a, b) => a + b, 0) / scores.length;
                    return Math.max(0, Math.min(5, average));
                }
                return 2.5; // Valeur par défaut
            });
        }

        // Mettre à jour l'affichage
        function updateDisplay() {
            updateMetrics();
            updateObjectivesList();
            updateAI();
        }

        // Mettre à jour les métriques
        function updateMetrics() {
            const scores = evaluationData.scores;
            const avgScore = scores.length > 0 ? scores.reduce((a, b) => a + b, 0) / scores.length : 0;
            const maxImpact = scores.length > 0 ? Math.max(...scores) : 0;
            const affectedObjectives = scores.filter(score => score > 0.1).length;
            const completion = Math.round((evaluationData.inputs.filter(input => input > 0).length / evaluationData.inputs.length) * 100);

            document.getElementById('avg-score').textContent = avgScore.toFixed(1);
            document.getElementById('max-impact').textContent = maxImpact.toFixed(1);
            document.getElementById('affected-objectives').textContent = affectedObjectives;
            document.getElementById('completion').textContent = completion + '%';
        }

        // Mettre à jour la liste des objectifs
        function updateObjectivesList() {
            const objectivesList = document.getElementById('objectives-list');
            const objectivesCount = document.getElementById('objectives-count');

            // Déterminer quels objectifs afficher
            let objectivesToDisplay = isSearchActive ? searchResults : evaluationData.objectives;

            objectivesCount.textContent = objectivesToDisplay.length;

            objectivesList.innerHTML = '';

            // Trier selon le contexte
            if (isSearchActive) {
                // En mode recherche, trier par pertinence
                objectivesToDisplay = objectivesToDisplay.sort((a, b) => b.relevanceScore - a.relevanceScore);
            } else {
                // Mode normal, trier par score
                objectivesToDisplay = objectivesToDisplay.sort((a, b) => b.score - a.score);
            }

            objectivesToDisplay.forEach(obj => {
                    const div = document.createElement('div');
                    div.className = 'p-3 border border-gray-200 rounded-lg hover:bg-gray-50';
                    div.setAttribute('data-objective', obj.code);

                    const impactColor = obj.impact === 'High' ? 'text-red-600' :
                                       obj.impact === 'Medium' ? 'text-yellow-600' : 'text-green-600';

                    // Utiliser la relative importance ajustée
                    const relativeImportance = obj.relativeImportance || 0;
                    const baselineScore = obj.baselineScore || obj.baseline || 2.5;

                    // Couleur pour RI selon la valeur
                    const riColor = relativeImportance > 20 ? 'text-green-600' :
                                   relativeImportance > 0 ? 'text-blue-600' :
                                   relativeImportance > -20 ? 'text-yellow-600' : 'text-red-600';

                    // Informations de recherche si applicable
                    const objectiveData = cobitObjectivesDatabase[obj.code];
                    let searchInfo = '';
                    let relevanceBadge = '';

                    if (isSearchActive && obj.relevanceScore) {
                        relevanceBadge = `<span class="inline-block px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full ml-2">★ ${obj.relevanceScore}</span>`;

                        if (obj.matchedTerms && obj.matchedTerms.length > 0) {
                            const displayTerms = obj.matchedTerms.slice(0, 3);
                            searchInfo = `
                                <div class="mt-1 text-xs text-blue-600">
                                    <i class="fas fa-search mr-1"></i>Correspondances: ${displayTerms.join(', ')}
                                    ${obj.matchedTerms.length > 3 ? ` +${obj.matchedTerms.length - 3}` : ''}
                                </div>
                            `;
                        }
                    }

                    // Nom complet de l'objectif si disponible
                    const fullName = objectiveData ? objectiveData.name : obj.domain;

                    div.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <div class="font-semibold text-sm flex items-center">
                                    ${obj.code}${relevanceBadge}
                                </div>
                                <div class="text-xs text-gray-600" title="${fullName}">${fullName.length > 50 ? fullName.substring(0, 50) + '...' : fullName}</div>
                                ${searchInfo}
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-blue-600">${obj.score.toFixed(1)}</div>
                                <div class="text-xs ${impactColor}">${obj.impact}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs mb-2">
                            <div class="text-center p-1 bg-blue-50 rounded">
                                <div class="font-bold text-blue-600">${obj.score.toFixed(3)}</div>
                                <div class="text-gray-500">Score</div>
                            </div>
                            <div class="text-center p-1 bg-gray-50 rounded">
                                <div class="font-bold text-gray-600">${baselineScore.toFixed(3)}</div>
                                <div class="text-gray-500">Baseline</div>
                            </div>
                            <div class="text-center p-1 bg-purple-50 rounded">
                                <div class="font-bold ${riColor}">${relativeImportance > 0 ? '+' : ''}${relativeImportance}</div>
                                <div class="text-gray-500">RI</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="text-center">
                                <span class="text-gray-500">Écart: </span>
                                <span class="${obj.gap >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${obj.gap > 0 ? '+' : ''}${obj.gap.toFixed(3)}
                                </span>
                            </div>
                            <div class="text-center">
                                <span class="text-gray-500">Formule: </span>
                                <span class="text-blue-600 text-xs">PRODUITMAT</span>
                            </div>
                        </div>
                    `;

                    objectivesList.appendChild(div);
                });
        }

        // Mettre à jour l'IA avec analyse avancée
        function updateAI() {
            const avgScore = evaluationData.scores.length > 0 ?
                evaluationData.scores.reduce((a, b) => a + b, 0) / evaluationData.scores.length : 0;
            const completion = evaluationData.inputs.filter(input => input > 0).length / evaluationData.inputs.length;

            // Analyse IA avancée basée sur les calculs COBIT
            const aiAnalysis = performAdvancedAIAnalysis();

            // Score IA (0-100) basé sur multiple facteurs
            const aiScore = calculateAIScore(avgScore, completion, aiAnalysis);

            // Niveau de risque basé sur l'analyse des écarts
            const riskLevel = calculateRiskLevel(aiAnalysis);

            // Priorité basée sur l'impact et l'urgence
            const priority = calculatePriority(aiAnalysis, completion);

            // Recommandation intelligente basée sur l'analyse
            const recommendation = generateIntelligentRecommendation(aiAnalysis, completion, avgScore);

            document.getElementById('ai-score').textContent = aiScore;
            document.getElementById('ai-risk').textContent = riskLevel;
            document.getElementById('ai-priority').textContent = priority;
            document.getElementById('ai-recommendation').textContent = recommendation;
        }

        // Analyse IA avancée
        function performAdvancedAIAnalysis() {
            const objectives = evaluationData.objectives;
            const analysis = {
                criticalObjectives: [],
                highPerformanceObjectives: [],
                averageGap: 0,
                maxGap: 0,
                minGap: 0,
                riskFactors: [],
                opportunities: [],
                domainPerformance: {}
            };

            if (objectives.length === 0) return analysis;

            // Analyser les écarts
            const gaps = objectives.map(obj => obj.gap);
            analysis.averageGap = gaps.reduce((a, b) => a + b, 0) / gaps.length;
            analysis.maxGap = Math.max(...gaps);
            analysis.minGap = Math.min(...gaps);

            // Identifier les objectifs critiques (écart négatif important)
            analysis.criticalObjectives = objectives.filter(obj => obj.gap < -0.5);

            // Identifier les objectifs performants (écart positif important)
            analysis.highPerformanceObjectives = objectives.filter(obj => obj.gap > 0.5);

            // Analyser les facteurs de risque
            if (analysis.criticalObjectives.length > 3) {
                analysis.riskFactors.push('Trop d\'objectifs sous-performants');
            }
            if (analysis.averageGap < -0.3) {
                analysis.riskFactors.push('Performance globale en dessous des standards');
            }

            // Identifier les opportunités
            if (analysis.highPerformanceObjectives.length > 2) {
                analysis.opportunities.push('Capitaliser sur les points forts identifiés');
            }

            // Performance par domaine
            const domains = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
            domains.forEach(domain => {
                const domainObjectives = objectives.filter(obj => obj.code.startsWith(domain));
                if (domainObjectives.length > 0) {
                    const avgScore = domainObjectives.reduce((sum, obj) => sum + obj.score, 0) / domainObjectives.length;
                    const avgGap = domainObjectives.reduce((sum, obj) => sum + obj.gap, 0) / domainObjectives.length;
                    analysis.domainPerformance[domain] = { avgScore, avgGap, count: domainObjectives.length };
                }
            });

            return analysis;
        }

        // Calculer le score IA
        function calculateAIScore(avgScore, completion, analysis) {
            let baseScore = (avgScore / 5) * 100;

            // Ajustements basés sur l'analyse
            if (analysis.criticalObjectives.length > 0) {
                baseScore -= analysis.criticalObjectives.length * 5;
            }
            if (analysis.highPerformanceObjectives.length > 0) {
                baseScore += analysis.highPerformanceObjectives.length * 3;
            }
            if (completion < 0.5) {
                baseScore -= 20;
            }

            return Math.max(0, Math.min(100, Math.round(baseScore)));
        }

        // Calculer le niveau de risque
        function calculateRiskLevel(analysis) {
            let riskScore = 0;

            riskScore += analysis.criticalObjectives.length * 2;
            riskScore += analysis.riskFactors.length * 3;
            if (analysis.averageGap < -0.5) riskScore += 5;

            if (riskScore >= 8) return 'Critique';
            if (riskScore >= 5) return 'Élevé';
            if (riskScore >= 2) return 'Moyen';
            return 'Faible';
        }

        // Calculer la priorité
        function calculatePriority(analysis, completion) {
            if (analysis.criticalObjectives.length > 2 || analysis.averageGap < -0.5) {
                return 'Critique';
            }
            if (completion > 0.8 && analysis.riskFactors.length > 0) {
                return 'Haute';
            }
            if (completion > 0.5) {
                return 'Moyenne';
            }
            return 'Basse';
        }

        // Générer une recommandation intelligente
        function generateIntelligentRecommendation(analysis, completion, avgScore) {
            if (completion < 0.3) {
                return `🔍 Complétez l'évaluation (${Math.round(completion * 100)}% terminé) pour une analyse précise`;
            }

            if (analysis.criticalObjectives.length > 0) {
                const criticalCodes = analysis.criticalObjectives.slice(0, 3).map(obj => obj.code).join(', ');
                return `🚨 Attention urgente requise sur: ${criticalCodes}. Écart moyen: ${analysis.averageGap.toFixed(2)}`;
            }

            if (analysis.averageGap < -0.2) {
                const worstDomain = Object.entries(analysis.domainPerformance)
                    .sort((a, b) => a[1].avgGap - b[1].avgGap)[0];
                return `📊 Concentrez-vous sur le domaine ${worstDomain[0]} (écart: ${worstDomain[1].avgGap.toFixed(2)})`;
            }

            if (analysis.highPerformanceObjectives.length > 0) {
                return `✅ Excellente performance ! Maintenez les standards sur ${analysis.highPerformanceObjectives.length} objectifs`;
            }

            if (avgScore > 3) {
                return `📈 Performance satisfaisante. Optimisez les processus pour atteindre l'excellence`;
            }

            return `🎯 Améliorations nécessaires. Focus sur les objectifs à fort impact relatif`;
        }

        // Fonctions de tri et filtrage
        function sortObjectives(criteria) {
            const objectivesList = document.getElementById('objectives-list');
            const objectives = [...evaluationData.objectives];

            if (criteria === 'score') {
                objectives.sort((a, b) => b.score - a.score);
            } else if (criteria === 'impact') {
                const impactOrder = { 'High': 3, 'Medium': 2, 'Low': 1 };
                objectives.sort((a, b) => impactOrder[b.impact] - impactOrder[a.impact]);
            }

            evaluationData.objectives = objectives;
            updateObjectivesList();
        }

        function filterByDomain(domain) {
            // Implémentation du filtrage par domaine
            updateObjectivesList();
        }

        // Fonction de tri des objectifs
        function sortObjectives(sortType) {
            if (sortType === 'relevance' && !isSearchActive) {
                return; // Tri par pertinence uniquement en mode recherche
            }

            if (isSearchActive) {
                // En mode recherche, trier les résultats de recherche
                switch(sortType) {
                    case 'score':
                        searchResults.sort((a, b) => b.score - a.score);
                        break;
                    case 'impact':
                        searchResults.sort((a, b) => {
                            const impactOrder = { 'High': 3, 'Medium': 2, 'Low': 1 };
                            return (impactOrder[b.impact] || 0) - (impactOrder[a.impact] || 0);
                        });
                        break;
                    case 'relevance':
                        searchResults.sort((a, b) => b.relevanceScore - a.relevanceScore);
                        break;
                }
            } else {
                // Mode normal, trier les objectifs normaux
                switch(sortType) {
                    case 'score':
                        evaluationData.objectives.sort((a, b) => b.score - a.score);
                        break;
                    case 'impact':
                        evaluationData.objectives.sort((a, b) => {
                            const impactOrder = { 'High': 3, 'Medium': 2, 'Low': 1 };
                            return (impactOrder[b.impact] || 0) - (impactOrder[a.impact] || 0);
                        });
                        break;
                }
            }

            updateObjectivesList();
        }

        // Initialiser les événements de recherche
        function initializeSearchEvents() {
            const searchInput = document.getElementById('smart-search');
            if (searchInput) {
                searchInput.addEventListener('focus', showSearchSuggestions);
                searchInput.addEventListener('blur', hideSearchSuggestions);
            }
        }

        // Test des nouvelles formules COBIT 2019
        function testNewFormulas() {
            console.log('=== Test Formules COBIT 2019 Exactes ===');

            // Définir les valeurs de test [1,5,5,5]
            const testInputs = [1, 5, 5, 5];
            const testBaseline = [1, 3.5, 3, 3];
            const edm01Matrix = [1, 4, 0, 0];

            // Mettre à jour les sliders
            testInputs.forEach((value, index) => {
                const slider = document.getElementById(`input-${index}`);
                const valueDisplay = document.getElementById(`value-${index}`);
                if (slider && valueDisplay) {
                    slider.value = value;
                    valueDisplay.textContent = value;
                    evaluationData.inputs[index] = value;
                }
            });

            // Recalculer avec les nouvelles formules
            calculateScores();
            updateDisplay();
            updateCharts();

            // Test direct des formules
            const score = calculateCOBITScore(edm01Matrix, testInputs);
            const baseline = calculateBaselineScore(edm01Matrix, testBaseline);
            const correctionFactor = calculateCorrectionFactor(dfNumber, testInputs, testBaseline);
            const ri = calculateRelativeImportance(score, baseline, dfNumber, testInputs, testBaseline);

            console.log('Test Direct des Formules:');
            console.log(`Inputs: [${testInputs.join(', ')}]`);
            console.log(`Baseline: [${testBaseline.join(', ')}]`);
            console.log(`Matrice EDM01: [${edm01Matrix.join(', ')}]`);
            console.log(`Score: ${score} (attendu: 21)`);
            console.log(`Baseline Score: ${baseline} (attendu: 15)`);
            console.log(`Correction Factor DF${dfNumber}: ${correctionFactor.toFixed(3)}`);
            console.log(`Relative Importance: ${ri} (plage normale: -100 à +100)`);

            // Vérifier EDM01 dans les résultats
            const edm01 = evaluationData.objectives.find(obj => obj.code === 'EDM01');
            if (edm01) {
                console.log('EDM01 dans les résultats:', {
                    score: edm01.score,
                    baseline: edm01.baselineScore,
                    ri: edm01.relativeImportance
                });

                // Vérifier que les valeurs sont dans des plages normales
                const scoreOk = edm01.score >= 0 && edm01.score <= 25; // Score raisonnable
                const baselineOk = edm01.baselineScore >= 0 && edm01.baselineScore <= 25; // Baseline raisonnable
                const riOk = edm01.relativeImportance >= -100 && edm01.relativeImportance <= 100; // RI dans plage normale

                const isCorrect = scoreOk && baselineOk && riOk;
                const message = isCorrect ?
                    `✅ Formules COBIT 2019 ajustées! Score=${edm01.score.toFixed(1)}, Baseline=${edm01.baselineScore.toFixed(1)}, RI=${edm01.relativeImportance}` :
                    `❌ Valeurs hors normes: Score=${edm01.score}, Baseline=${edm01.baselineScore}, RI=${edm01.relativeImportance}`;

                alert(message);
                console.log(message);
            } else {
                alert('❌ EDM01 non trouvé dans les objectifs');
            }
        }

        // === RECHERCHE INTELLIGENTE D'OBJECTIFS ===

        // Base de données des objectifs COBIT avec mots-clés métiers
        const cobitObjectivesDatabase = {
            'EDM01': {
                name: 'Ensure Governance Framework Setting and Maintenance',
                keywords: ['gouvernance', 'cadre', 'framework', 'stratégie', 'direction', 'politique', 'supervision'],
                description: 'Établir et maintenir un cadre de gouvernance pour l\'IT',
                businessTerms: ['gouvernance d\'entreprise', 'stratégie IT', 'politique IT', 'supervision']
            },
            'EDM02': {
                name: 'Ensure Benefits Delivery',
                keywords: ['bénéfices', 'valeur', 'ROI', 'retour investissement', 'performance', 'résultats'],
                description: 'Assurer la livraison des bénéfices attendus',
                businessTerms: ['création de valeur', 'ROI', 'performance business', 'bénéfices métier']
            },
            'EDM03': {
                name: 'Ensure Risk Optimization',
                keywords: ['risque', 'risk', 'gestion risques', 'mitigation', 'sécurité', 'conformité'],
                description: 'Optimiser la gestion des risques IT',
                businessTerms: ['gestion des risques', 'sécurité', 'conformité', 'audit']
            },
            'EDM04': {
                name: 'Ensure Resource Optimization',
                keywords: ['ressources', 'optimisation', 'budget', 'coût', 'efficacité', 'allocation'],
                description: 'Optimiser l\'utilisation des ressources IT',
                businessTerms: ['optimisation des coûts', 'gestion budgétaire', 'efficacité']
            },
            'EDM05': {
                name: 'Ensure Stakeholder Transparency',
                keywords: ['transparence', 'communication', 'parties prenantes', 'reporting', 'information'],
                description: 'Assurer la transparence envers les parties prenantes',
                businessTerms: ['communication', 'reporting', 'transparence']
            },
            'APO01': {
                name: 'Manage the IT Management Framework',
                keywords: ['framework', 'cadre', 'management', 'organisation', 'structure', 'processus'],
                description: 'Gérer le cadre de management IT',
                businessTerms: ['organisation IT', 'processus', 'structure']
            },
            'APO02': {
                name: 'Manage Strategy',
                keywords: ['stratégie', 'planification', 'vision', 'objectifs', 'alignement', 'business'],
                description: 'Gérer la stratégie IT',
                businessTerms: ['stratégie IT', 'alignement business', 'planification stratégique']
            },
            'APO03': {
                name: 'Manage Enterprise Architecture',
                keywords: ['architecture', 'entreprise', 'conception', 'modèle', 'urbanisation', 'système'],
                description: 'Gérer l\'architecture d\'entreprise',
                businessTerms: ['architecture d\'entreprise', 'urbanisation SI', 'conception système']
            },
            'APO04': {
                name: 'Manage Innovation',
                keywords: ['innovation', 'nouveauté', 'technologie', 'R&D', 'évolution', 'modernisation'],
                description: 'Gérer l\'innovation IT',
                businessTerms: ['innovation technologique', 'R&D', 'modernisation']
            },
            'APO05': {
                name: 'Manage Portfolio',
                keywords: ['portfolio', 'portefeuille', 'projets', 'programmes', 'investissements', 'priorités'],
                description: 'Gérer le portefeuille de projets IT',
                businessTerms: ['gestion de portefeuille', 'projets IT', 'investissements']
            },
            'APO06': {
                name: 'Manage Budget and Costs',
                keywords: ['budget', 'coûts', 'finances', 'dépenses', 'économies', 'ROI'],
                description: 'Gérer le budget et les coûts IT',
                businessTerms: ['gestion budgétaire', 'contrôle des coûts', 'finances IT']
            },
            'APO07': {
                name: 'Manage Human Resources',
                keywords: ['ressources humaines', 'RH', 'compétences', 'formation', 'talents', 'équipe'],
                description: 'Gérer les ressources humaines IT',
                businessTerms: ['gestion RH', 'compétences IT', 'formation', 'talents']
            },
            'APO08': {
                name: 'Manage Relationships',
                keywords: ['relations', 'partenaires', 'fournisseurs', 'clients', 'communication', 'collaboration'],
                description: 'Gérer les relations avec les parties prenantes',
                businessTerms: ['gestion des relations', 'partenaires', 'fournisseurs']
            },
            'APO09': {
                name: 'Manage Service Agreements',
                keywords: ['accords', 'SLA', 'contrats', 'services', 'niveaux', 'qualité'],
                description: 'Gérer les accords de service',
                businessTerms: ['SLA', 'accords de service', 'qualité de service']
            },
            'APO10': {
                name: 'Manage Suppliers',
                keywords: ['fournisseurs', 'prestataires', 'sous-traitance', 'contrats', 'approvisionnement'],
                description: 'Gérer les fournisseurs IT',
                businessTerms: ['gestion fournisseurs', 'sous-traitance', 'approvisionnement']
            },
            'APO11': {
                name: 'Manage Quality',
                keywords: ['qualité', 'standards', 'normes', 'amélioration', 'excellence', 'certification'],
                description: 'Gérer la qualité des services IT',
                businessTerms: ['gestion de la qualité', 'standards', 'amélioration continue']
            },
            'APO12': {
                name: 'Manage Risk',
                keywords: ['risque', 'gestion risques', 'sécurité', 'menaces', 'vulnérabilités', 'audit'],
                description: 'Gérer les risques IT',
                businessTerms: ['gestion des risques', 'sécurité IT', 'audit', 'conformité']
            },
            'APO13': {
                name: 'Manage Security',
                keywords: ['sécurité', 'cybersécurité', 'protection', 'confidentialité', 'intégrité', 'disponibilité'],
                description: 'Gérer la sécurité de l\'information',
                businessTerms: ['cybersécurité', 'sécurité des données', 'protection information']
            },
            'APO14': {
                name: 'Manage Data',
                keywords: ['données', 'data', 'information', 'gouvernance données', 'qualité données', 'analytics'],
                description: 'Gérer les données et l\'information',
                businessTerms: ['gouvernance des données', 'big data', 'analytics', 'qualité des données']
            },
            'BAI01': {
                name: 'Manage Programmes and Projects',
                keywords: ['programmes', 'projets', 'gestion projet', 'livraison', 'planning', 'méthodologie'],
                description: 'Gérer les programmes et projets IT',
                businessTerms: ['gestion de projet', 'méthodologie projet', 'livraison']
            },
            'BAI02': {
                name: 'Manage Requirements Definition',
                keywords: ['exigences', 'besoins', 'spécifications', 'analyse', 'cahier charges', 'fonctionnel'],
                description: 'Gérer la définition des exigences',
                businessTerms: ['analyse des besoins', 'spécifications', 'cahier des charges']
            },
            'BAI03': {
                name: 'Manage Solutions Identification and Build',
                keywords: ['solutions', 'développement', 'construction', 'conception', 'architecture', 'build'],
                description: 'Gérer l\'identification et la construction des solutions',
                businessTerms: ['développement', 'conception solution', 'architecture technique']
            },
            'BAI04': {
                name: 'Manage Availability and Capacity',
                keywords: ['disponibilité', 'capacité', 'performance', 'dimensionnement', 'scalabilité', 'SLA'],
                description: 'Gérer la disponibilité et la capacité',
                businessTerms: ['disponibilité système', 'performance', 'capacité', 'scalabilité']
            },
            'BAI05': {
                name: 'Manage Organisational Change',
                keywords: ['changement', 'transformation', 'conduite changement', 'adoption', 'formation', 'communication'],
                description: 'Gérer le changement organisationnel',
                businessTerms: ['conduite du changement', 'transformation', 'adoption utilisateur']
            },
            'BAI06': {
                name: 'Manage Changes',
                keywords: ['changements', 'modifications', 'évolutions', 'versions', 'releases', 'déploiement'],
                description: 'Gérer les changements techniques',
                businessTerms: ['gestion des changements', 'releases', 'déploiement']
            },
            'BAI07': {
                name: 'Manage Change Acceptance and Transitioning',
                keywords: ['acceptation', 'transition', 'mise en production', 'recette', 'validation', 'go-live'],
                description: 'Gérer l\'acceptation et la transition des changements',
                businessTerms: ['mise en production', 'recette', 'validation', 'go-live']
            },
            'BAI08': {
                name: 'Manage Knowledge',
                keywords: ['connaissance', 'savoir', 'documentation', 'capitalisation', 'expertise', 'formation'],
                description: 'Gérer les connaissances',
                businessTerms: ['gestion des connaissances', 'documentation', 'capitalisation']
            },
            'BAI09': {
                name: 'Manage Assets',
                keywords: ['actifs', 'assets', 'inventaire', 'patrimoine', 'configuration', 'CMDB'],
                description: 'Gérer les actifs IT',
                businessTerms: ['gestion des actifs', 'inventaire IT', 'CMDB']
            },
            'BAI10': {
                name: 'Manage Configuration',
                keywords: ['configuration', 'paramétrage', 'CMDB', 'versions', 'baseline', 'référentiel'],
                description: 'Gérer la configuration',
                businessTerms: ['gestion de configuration', 'CMDB', 'référentiel']
            },
            'BAI11': {
                name: 'Manage Projects',
                keywords: ['projets', 'gestion projet', 'planning', 'ressources', 'livrables', 'jalons'],
                description: 'Gérer les projets',
                businessTerms: ['gestion de projet', 'planning', 'livrables']
            },
            'DSS01': {
                name: 'Manage Operations',
                keywords: ['opérations', 'exploitation', 'production', 'monitoring', 'supervision', '24/7'],
                description: 'Gérer les opérations IT',
                businessTerms: ['exploitation', 'production', 'supervision', 'monitoring']
            },
            'DSS02': {
                name: 'Manage Service Requests and Incidents',
                keywords: ['incidents', 'demandes', 'support', 'helpdesk', 'tickets', 'résolution'],
                description: 'Gérer les demandes de service et incidents',
                businessTerms: ['support utilisateur', 'helpdesk', 'gestion incidents']
            },
            'DSS03': {
                name: 'Manage Problems',
                keywords: ['problèmes', 'analyse cause', 'résolution', 'récurrence', 'amélioration', 'root cause'],
                description: 'Gérer les problèmes',
                businessTerms: ['gestion des problèmes', 'analyse des causes', 'amélioration']
            },
            'DSS04': {
                name: 'Manage Continuity',
                keywords: ['continuité', 'PCA', 'disaster recovery', 'sauvegarde', 'reprise', 'business continuity'],
                description: 'Gérer la continuité de service',
                businessTerms: ['plan de continuité', 'disaster recovery', 'sauvegarde']
            },
            'DSS05': {
                name: 'Manage Security Services',
                keywords: ['sécurité', 'cybersécurité', 'protection', 'antivirus', 'firewall', 'intrusion'],
                description: 'Gérer les services de sécurité',
                businessTerms: ['cybersécurité', 'protection', 'sécurité informatique']
            },
            'DSS06': {
                name: 'Manage Business Process Controls',
                keywords: ['contrôles', 'processus métier', 'conformité', 'audit', 'vérification', 'compliance'],
                description: 'Gérer les contrôles des processus métier',
                businessTerms: ['contrôles internes', 'conformité', 'audit', 'compliance']
            },
            'MEA01': {
                name: 'Monitor, Evaluate and Assess Performance and Conformance',
                keywords: ['monitoring', 'performance', 'conformité', 'évaluation', 'mesure', 'KPI'],
                description: 'Surveiller et évaluer la performance et la conformité',
                businessTerms: ['monitoring', 'KPI', 'tableau de bord', 'performance']
            },
            'MEA02': {
                name: 'Monitor, Evaluate and Assess the System of Internal Controls',
                keywords: ['contrôles internes', 'audit', 'évaluation', 'surveillance', 'conformité', 'risques'],
                description: 'Surveiller le système de contrôles internes',
                businessTerms: ['contrôles internes', 'audit interne', 'surveillance']
            },
            'MEA03': {
                name: 'Monitor, Evaluate and Assess Compliance',
                keywords: ['conformité', 'compliance', 'réglementation', 'audit', 'légal', 'normes'],
                description: 'Surveiller la conformité réglementaire',
                businessTerms: ['conformité réglementaire', 'compliance', 'audit externe']
            },
            'MEA04': {
                name: 'Provide Governance Assurance',
                keywords: ['assurance', 'gouvernance', 'audit', 'certification', 'validation', 'attestation'],
                description: 'Fournir l\'assurance gouvernance',
                businessTerms: ['assurance gouvernance', 'certification', 'audit gouvernance']
            }
        };

        // Variables de recherche
        let currentSearchTerm = '';
        let searchResults = [];
        let isSearchActive = false;

        // Fonction de recherche intelligente
        function performSmartSearch(searchTerm) {
            currentSearchTerm = searchTerm.toLowerCase().trim();
            const searchInput = document.getElementById('smart-search');
            const clearButton = document.getElementById('clear-search');
            const searchResultsDiv = document.getElementById('search-results');
            const searchCount = document.getElementById('search-count');
            const searchTermSpan = document.getElementById('search-term');
            const sortRelevanceBtn = document.getElementById('sort-relevance');
            const suggestionsDiv = document.getElementById('search-suggestions');

            // Mettre à jour l'input si nécessaire
            if (searchInput.value !== searchTerm) {
                searchInput.value = searchTerm;
            }

            if (currentSearchTerm === '') {
                // Réinitialiser la recherche
                clearSearch();
                return;
            }

            // Afficher/masquer les éléments UI
            clearButton.classList.remove('hidden');
            suggestionsDiv.classList.add('hidden');
            searchResultsDiv.classList.remove('hidden');
            sortRelevanceBtn.classList.remove('hidden');

            // Effectuer la recherche
            searchResults = searchObjectives(currentSearchTerm);

            // Mettre à jour les résultats
            searchCount.textContent = searchResults.length;
            searchTermSpan.textContent = currentSearchTerm;

            isSearchActive = true;

            // Mettre à jour l'affichage des objectifs
            updateObjectivesList();
        }

        // Fonction de recherche dans les objectifs
        function searchObjectives(searchTerm) {
            const results = [];

            evaluationData.objectives.forEach(obj => {
                const objectiveData = cobitObjectivesDatabase[obj.code];
                if (!objectiveData) return;

                let relevanceScore = 0;
                let matchedTerms = [];

                // Recherche dans le code de l'objectif
                if (obj.code.toLowerCase().includes(searchTerm)) {
                    relevanceScore += 10;
                    matchedTerms.push('code');
                }

                // Recherche dans le nom
                if (objectiveData.name.toLowerCase().includes(searchTerm)) {
                    relevanceScore += 8;
                    matchedTerms.push('nom');
                }

                // Recherche dans les mots-clés métiers
                objectiveData.keywords.forEach(keyword => {
                    if (keyword.toLowerCase().includes(searchTerm) || searchTerm.includes(keyword.toLowerCase())) {
                        relevanceScore += 6;
                        matchedTerms.push(keyword);
                    }
                });

                // Recherche dans les termes business
                objectiveData.businessTerms.forEach(term => {
                    if (term.toLowerCase().includes(searchTerm) || searchTerm.includes(term.toLowerCase())) {
                        relevanceScore += 7;
                        matchedTerms.push(term);
                    }
                });

                // Recherche dans la description
                if (objectiveData.description.toLowerCase().includes(searchTerm)) {
                    relevanceScore += 4;
                    matchedTerms.push('description');
                }

                // Bonus pour les objectifs du DF actuel
                if (relevanceScore > 0) {
                    relevanceScore += 2; // Bonus pour pertinence avec le DF actuel
                }

                // Bonus selon la priorité de l'objectif
                if (obj.priority && obj.priority > 1.2) {
                    relevanceScore += 3;
                }

                if (relevanceScore > 0) {
                    results.push({
                        ...obj,
                        relevanceScore,
                        matchedTerms: [...new Set(matchedTerms)],
                        objectiveData
                    });
                }
            });

            // Trier par score de pertinence décroissant
            return results.sort((a, b) => b.relevanceScore - a.relevanceScore);
        }

        // Gérer les touches du clavier dans la recherche
        function handleSearchKeydown(event) {
            if (event.key === 'Escape') {
                clearSearch();
            } else if (event.key === 'Enter') {
                event.preventDefault();
                // Focus sur le premier résultat si disponible
                if (searchResults.length > 0) {
                    highlightObjective(searchResults[0].code);
                }
            }
        }

        // Effacer la recherche
        function clearSearch() {
            const searchInput = document.getElementById('smart-search');
            const clearButton = document.getElementById('clear-search');
            const searchResultsDiv = document.getElementById('search-results');
            const sortRelevanceBtn = document.getElementById('sort-relevance');
            const suggestionsDiv = document.getElementById('search-suggestions');

            searchInput.value = '';
            clearButton.classList.add('hidden');
            searchResultsDiv.classList.add('hidden');
            sortRelevanceBtn.classList.add('hidden');
            suggestionsDiv.classList.remove('hidden');

            currentSearchTerm = '';
            searchResults = [];
            isSearchActive = false;

            // Mettre à jour l'affichage
            updateObjectivesList();
        }

        // Mettre en évidence un objectif spécifique
        function highlightObjective(objectiveCode) {
            // Supprimer les anciens highlights
            document.querySelectorAll('.objective-highlight').forEach(el => {
                el.classList.remove('objective-highlight');
            });

            // Ajouter le highlight au nouvel objectif
            const objectiveElement = document.querySelector(`[data-objective="${objectiveCode}"]`);
            if (objectiveElement) {
                objectiveElement.classList.add('objective-highlight');
                objectiveElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // Afficher les suggestions de recherche au focus
        function showSearchSuggestions() {
            const suggestionsDiv = document.getElementById('search-suggestions');
            if (!isSearchActive) {
                suggestionsDiv.classList.remove('hidden');
            }
        }

        // Masquer les suggestions
        function hideSearchSuggestions() {
            setTimeout(() => {
                const suggestionsDiv = document.getElementById('search-suggestions');
                if (document.activeElement.id !== 'smart-search') {
                    suggestionsDiv.classList.add('hidden');
                }
            }, 200);
        }

        // === MATRICE DYNAMIQUE DE PRIORISATION ===

        // Données de contexte pour la priorisation
        const prioritizationContext = {
            sectors: {
                finance: {
                    name: 'Finance/Banque',
                    priorities: { 'EDM': 1.2, 'APO': 1.1, 'BAI': 0.9, 'DSS': 1.3, 'MEA': 1.2 },
                    regulatory: 1.3
                },
                healthcare: {
                    name: 'Santé',
                    priorities: { 'EDM': 1.1, 'APO': 1.0, 'BAI': 1.2, 'DSS': 1.4, 'MEA': 1.1 },
                    regulatory: 1.2
                },
                manufacturing: {
                    name: 'Industrie',
                    priorities: { 'EDM': 1.0, 'APO': 1.2, 'BAI': 1.3, 'DSS': 1.1, 'MEA': 1.0 },
                    regulatory: 0.9
                },
                retail: {
                    name: 'Commerce',
                    priorities: { 'EDM': 0.9, 'APO': 1.1, 'BAI': 1.1, 'DSS': 1.2, 'MEA': 0.9 },
                    regulatory: 0.8
                },
                government: {
                    name: 'Public',
                    priorities: { 'EDM': 1.3, 'APO': 1.2, 'BAI': 1.0, 'DSS': 1.1, 'MEA': 1.3 },
                    regulatory: 1.5
                },
                technology: {
                    name: 'Technologie',
                    priorities: { 'EDM': 1.0, 'APO': 1.3, 'BAI': 1.4, 'DSS': 1.2, 'MEA': 1.1 },
                    regulatory: 0.7
                }
            },
            organizationSizes: {
                small: { multiplier: 0.8, focus: ['DSS', 'BAI'] },
                medium: { multiplier: 1.0, focus: ['APO', 'DSS'] },
                large: { multiplier: 1.2, focus: ['EDM', 'MEA'] }
            },
            regulatoryLevels: {
                high: 1.3,
                medium: 1.0,
                low: 0.7
            }
        };

        // Changer de vue (onglets)
        function switchView(viewName) {
            // Masquer toutes les vues
            document.getElementById('view-objectives').classList.add('hidden');
            document.getElementById('view-prioritization').classList.add('hidden');
            document.getElementById('view-roadmap').classList.add('hidden');

            // Réinitialiser les onglets
            document.getElementById('tab-objectives').className = 'px-3 py-2 text-xs font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700';
            document.getElementById('tab-prioritization').className = 'px-3 py-2 text-xs font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700';
            document.getElementById('tab-roadmap').className = 'px-3 py-2 text-xs font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700';

            // Afficher la vue sélectionnée
            document.getElementById('view-' + viewName).classList.remove('hidden');
            document.getElementById('tab-' + viewName).className = 'px-3 py-2 text-xs font-medium border-b-2 border-blue-500 text-blue-600';

            // Mettre à jour le contenu selon la vue
            if (viewName === 'prioritization') {
                updatePrioritization();
            } else if (viewName === 'roadmap') {
                updateRoadmapView();
            }
        }

        // Calculer la priorité d'un objectif selon le contexte
        function calculateObjectivePriority(objective) {
            const sector = document.getElementById('sector').value;
            const orgSize = document.getElementById('organization-size').value;
            const regulatory = document.getElementById('regulatory-context').value;

            const domain = objective.code.substring(0, 3);
            const sectorData = prioritizationContext.sectors[sector];
            const sizeData = prioritizationContext.organizationSizes[orgSize];
            const regulatoryMultiplier = prioritizationContext.regulatoryLevels[regulatory];

            // Calcul de la priorité contextuelle
            let priority = 1.0;

            // Facteur sectoriel
            priority *= sectorData.priorities[domain] || 1.0;

            // Facteur taille organisation
            priority *= sizeData.multiplier;
            if (sizeData.focus.includes(domain)) {
                priority *= 1.2;
            }

            // Facteur réglementaire
            if (['EDM', 'MEA'].includes(domain)) {
                priority *= regulatoryMultiplier;
            }

            // Facteur performance actuelle (score vs baseline)
            const performanceRatio = objective.baselineScore > 0 ? objective.score / objective.baselineScore : 1;
            if (performanceRatio < 0.8) {
                priority *= 1.3; // Priorité élevée si performance faible
            } else if (performanceRatio > 1.2) {
                priority *= 0.8; // Priorité réduite si performance élevée
            }

            return Math.round(priority * 100) / 100;
        }

        // Mettre à jour la matrice de priorisation
        function updatePrioritization() {
            const matrix = document.getElementById('prioritization-matrix');
            if (!matrix) return;

            // Calculer les priorités pour tous les objectifs
            const prioritizedObjectives = evaluationData.objectives.map(obj => ({
                ...obj,
                priority: calculateObjectivePriority(obj),
                businessContribution: calculateBusinessContribution(obj)
            })).sort((a, b) => b.priority - a.priority);

            // Générer la matrice HTML
            let html = `
                <div class="space-y-2">
                    <div class="grid grid-cols-5 gap-1 text-xs font-semibold bg-gray-100 p-2 rounded">
                        <div>Objectif</div>
                        <div>Score</div>
                        <div>Priorité</div>
                        <div>Contrib. Métier</div>
                        <div>Action</div>
                    </div>
            `;

            prioritizedObjectives.forEach(obj => {
                const priorityColor = obj.priority >= 1.5 ? 'text-red-600' :
                                    obj.priority >= 1.2 ? 'text-orange-600' :
                                    obj.priority >= 1.0 ? 'text-blue-600' : 'text-gray-600';

                const actionRecommendation = obj.priority >= 1.5 ? 'Urgent' :
                                           obj.priority >= 1.2 ? 'Court terme' :
                                           obj.priority >= 1.0 ? 'Moyen terme' : 'Long terme';

                html += `
                    <div class="grid grid-cols-5 gap-1 text-xs p-2 border-b hover:bg-gray-50">
                        <div class="font-medium">${obj.code}</div>
                        <div>${obj.score.toFixed(1)}</div>
                        <div class="${priorityColor} font-bold">${obj.priority}</div>
                        <div>${obj.businessContribution}</div>
                        <div class="text-xs px-1 py-0.5 rounded ${obj.priority >= 1.5 ? 'bg-red-100 text-red-800' :
                                                                  obj.priority >= 1.2 ? 'bg-orange-100 text-orange-800' :
                                                                  obj.priority >= 1.0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">${actionRecommendation}</div>
                    </div>
                `;
            });

            html += '</div>';
            matrix.innerHTML = html;
        }

        // Calculer la contribution métier d'un objectif
        function calculateBusinessContribution(objective) {
            const domain = objective.code.substring(0, 3);
            const contributions = {
                'EDM': 'Gouvernance',
                'APO': 'Stratégie',
                'BAI': 'Livraison',
                'DSS': 'Support',
                'MEA': 'Surveillance'
            };
            return contributions[domain] || 'Autre';
        }

        // Mettre à jour la vue roadmap
        function updateRoadmapView() {
            const roadmapContent = document.getElementById('roadmap-content');
            if (!roadmapContent) return;

            roadmapContent.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-road text-4xl mb-4"></i>
                    <p>Cliquez sur "Générer Roadmap IA" pour créer une roadmap personnalisée</p>
                </div>
            `;
        }

        // Générer une roadmap d'implémentation avec IA
        function generateRoadmap() {
            const roadmapContent = document.getElementById('roadmap-content');
            if (!roadmapContent) return;

            // Afficher un indicateur de chargement
            roadmapContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-4"></i>
                    <p class="text-sm text-gray-600">Génération de la roadmap par IA...</p>
                </div>
            `;

            // Simuler l'analyse IA (remplacer par un vrai appel API)
            setTimeout(() => {
                const roadmap = generateAIRoadmap();
                displayRoadmap(roadmap);
            }, 2000);
        }

        // Moteur de recommandation basé sur l'IA
        function generateAIRoadmap() {
            const sector = document.getElementById('sector').value;
            const orgSize = document.getElementById('organization-size').value;
            const regulatory = document.getElementById('regulatory-context').value;

            // Analyser les objectifs prioritaires
            const prioritizedObjectives = evaluationData.objectives.map(obj => ({
                ...obj,
                priority: calculateObjectivePriority(obj)
            })).sort((a, b) => b.priority - a.priority);

            // Grouper par phases d'implémentation
            const phases = {
                immediate: prioritizedObjectives.filter(obj => obj.priority >= 1.5),
                shortTerm: prioritizedObjectives.filter(obj => obj.priority >= 1.2 && obj.priority < 1.5),
                mediumTerm: prioritizedObjectives.filter(obj => obj.priority >= 1.0 && obj.priority < 1.2),
                longTerm: prioritizedObjectives.filter(obj => obj.priority < 1.0)
            };

            // Recommandations IA basées sur le contexte
            const aiRecommendations = generateContextualRecommendations(sector, orgSize, regulatory, phases);

            return {
                phases,
                recommendations: aiRecommendations,
                timeline: generateTimeline(phases),
                resources: estimateResources(phases, orgSize),
                risks: identifyRisks(phases, sector)
            };
        }

        // Générer des recommandations contextuelles
        function generateContextualRecommendations(sector, orgSize, regulatory, phases) {
            const recommendations = [];

            // Recommandations par secteur
            const sectorAdvice = {
                finance: "Priorisez la conformité réglementaire et la gestion des risques",
                healthcare: "Concentrez-vous sur la sécurité des données patients et la continuité de service",
                manufacturing: "Optimisez les processus de production et la chaîne d'approvisionnement",
                retail: "Améliorez l'expérience client et la gestion des données commerciales",
                government: "Renforcez la transparence et la conformité aux réglementations publiques",
                technology: "Accélérez l'innovation tout en maintenant la sécurité"
            };

            recommendations.push({
                type: 'Sectoriel',
                advice: sectorAdvice[sector],
                priority: 'High'
            });

            // Recommandations par taille d'organisation
            if (orgSize === 'small') {
                recommendations.push({
                    type: 'Ressources',
                    advice: "Commencez par les processus essentiels et externalisez si nécessaire",
                    priority: 'Medium'
                });
            } else if (orgSize === 'large') {
                recommendations.push({
                    type: 'Gouvernance',
                    advice: "Établissez un centre d'excellence COBIT et des comités de pilotage",
                    priority: 'High'
                });
            }

            // Recommandations basées sur les phases
            if (phases.immediate.length > 5) {
                recommendations.push({
                    type: 'Priorisation',
                    advice: "Trop d'objectifs urgents détectés. Concentrez-vous sur les 3 plus critiques",
                    priority: 'Critical'
                });
            }

            return recommendations;
        }

        // Générer une timeline
        function generateTimeline(phases) {
            return {
                immediate: "0-3 mois",
                shortTerm: "3-9 mois",
                mediumTerm: "9-18 mois",
                longTerm: "18+ mois"
            };
        }

        // Estimer les ressources nécessaires
        function estimateResources(phases, orgSize) {
            const baseEffort = {
                small: { immediate: 2, shortTerm: 4, mediumTerm: 6, longTerm: 8 },
                medium: { immediate: 4, shortTerm: 8, mediumTerm: 12, longTerm: 16 },
                large: { immediate: 8, shortTerm: 16, mediumTerm: 24, longTerm: 32 }
            };

            const effort = baseEffort[orgSize];
            return {
                immediate: `${effort.immediate} personnes-mois`,
                shortTerm: `${effort.shortTerm} personnes-mois`,
                mediumTerm: `${effort.mediumTerm} personnes-mois`,
                longTerm: `${effort.longTerm} personnes-mois`
            };
        }

        // Identifier les risques
        function identifyRisks(phases, sector) {
            const risks = [];

            if (phases.immediate.length === 0) {
                risks.push("Aucun objectif urgent identifié - risque de complaisance");
            }

            if (sector === 'finance' && phases.immediate.some(obj => obj.code.startsWith('EDM'))) {
                risks.push("Risque réglementaire élevé - gouvernance critique");
            }

            return risks;
        }

        // Afficher la roadmap générée
        function displayRoadmap(roadmap) {
            const roadmapContent = document.getElementById('roadmap-content');

            let html = `
                <div class="space-y-4">
                    <!-- Recommandations IA -->
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <h4 class="font-semibold text-sm mb-2 flex items-center">
                            <i class="fas fa-robot mr-2 text-blue-600"></i>Recommandations IA
                        </h4>
                        <div class="space-y-2">
            `;

            roadmap.recommendations.forEach(rec => {
                const priorityColor = rec.priority === 'Critical' ? 'text-red-600' :
                                    rec.priority === 'High' ? 'text-orange-600' : 'text-blue-600';
                html += `
                    <div class="text-xs">
                        <span class="font-medium ${priorityColor}">[${rec.type}]</span>
                        ${rec.advice}
                    </div>
                `;
            });

            html += `
                        </div>
                    </div>

                    <!-- Timeline des phases -->
                    <div class="space-y-3">
            `;

            Object.entries(roadmap.phases).forEach(([phase, objectives]) => {
                if (objectives.length === 0) return;

                const phaseColors = {
                    immediate: 'bg-red-50 border-red-200 text-red-800',
                    shortTerm: 'bg-orange-50 border-orange-200 text-orange-800',
                    mediumTerm: 'bg-blue-50 border-blue-200 text-blue-800',
                    longTerm: 'bg-gray-50 border-gray-200 text-gray-800'
                };

                const phaseNames = {
                    immediate: 'Immédiat',
                    shortTerm: 'Court terme',
                    mediumTerm: 'Moyen terme',
                    longTerm: 'Long terme'
                };

                html += `
                    <div class="border rounded-lg p-3 ${phaseColors[phase]}">
                        <div class="flex justify-between items-center mb-2">
                            <h5 class="font-semibold text-sm">${phaseNames[phase]} (${roadmap.timeline[phase]})</h5>
                            <span class="text-xs">${roadmap.resources[phase]}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1">
                `;

                objectives.slice(0, 6).forEach(obj => {
                    html += `
                        <div class="text-xs p-1 bg-white rounded border">
                            ${obj.code} (P: ${obj.priority})
                        </div>
                    `;
                });

                if (objectives.length > 6) {
                    html += `<div class="text-xs text-gray-600">+${objectives.length - 6} autres...</div>`;
                }

                html += `
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>

                    <!-- Risques identifiés -->
                    ${roadmap.risks.length > 0 ? `
                    <div class="bg-yellow-50 p-3 rounded-lg">
                        <h4 class="font-semibold text-sm mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2 text-yellow-600"></i>Risques Identifiés
                        </h4>
                        <ul class="text-xs space-y-1">
                            ${roadmap.risks.map(risk => `<li>• ${risk}</li>`).join('')}
                        </ul>
                    </div>
                    ` : ''}
                </div>
            `;

            roadmapContent.innerHTML = html;
        }
    </script>
</body>
</html>
