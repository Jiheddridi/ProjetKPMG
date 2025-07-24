@extends('layouts.app')

@section('title', 'COBIT 2019 - Évaluation')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cobit-enhanced.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/cobit-evaluation.js') }}"></script>
@endpush

@section('content')
<div class="fade-in">
    <!-- Header avec navigation par onglets -->
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Évaluation COBIT 2019</h1>
                    <p class="text-gray-600 mt-1">Design Factors - Facteurs de conception</p>
                </div>
                <div class="flex space-x-3">
                    <button id="save-all-btn" class="btn btn-success">
                        <i class="fas fa-save mr-2"></i>Sauvegarder Tout
                    </button>
                    <button id="export-btn" class="btn btn-secondary">
                        <i class="fas fa-download mr-2"></i>Exporter
                    </button>
                    <button id="reset-all-btn" class="btn btn-danger">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Onglets des Design Factors -->
        <div class="p-4 border-b">
            <div class="flex flex-wrap gap-2">
                @foreach($designFactors as $df)
                <button
                    id="tab-df{{ $df->getNumberFromCode() }}"
                    data-df="{{ $df->getNumberFromCode() }}"
                    class="df-tab relative px-4 py-2 rounded-lg font-medium transition-all {{ $df->getNumberFromCode() == 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $df->code }}
                    <span class="ml-2 text-xs opacity-75">{{ $df->title }}</span>
                    <!-- Indicateur de validation -->
                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 border-white"
                          id="status-indicator-df{{ $df->getNumberFromCode() }}"
                          style="background-color: #6B7280;"></span>
                </button>
                @endforeach

                <!-- Onglet Résultats -->
                <button
                    id="tab-results"
                    class="df-tab px-4 py-2 rounded-lg font-medium transition-all bg-green-100 text-green-700 hover:bg-green-200 disabled:opacity-50"
                    disabled>
                    <i class="fas fa-chart-bar mr-2"></i>Résultats Finaux
                    <span class="ml-2 text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full" id="completed-count">0/10</span>
                </button>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progression globale</span>
                <span class="text-sm text-gray-600" id="progress-text">0/10 complétés</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Contenu principal - Design Factors -->
    <div id="evaluationContainer">
        @foreach($designFactors as $df)
        <div id="df{{ $df->getNumberFromCode() }}-content" class="df-content card {{ $df->getNumberFromCode() != 1 ? 'hidden' : '' }}">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $df->title }}</h2>
                        <p class="text-gray-600 mt-1">{{ $df->description }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">{{ $df->code }}</span>
                        <button data-df="{{ $df->getNumberFromCode() }}" class="btn btn-secondary btn-sm reset-df-btn">
                            <i class="fas fa-undo mr-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Section des inputs -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-sliders-h mr-2 text-blue-600"></i>
                            Paramètres d'évaluation
                        </h3>

                        <div class="space-y-6">
                            @foreach($df->labels as $index => $label)
                            @php
                                $inputType = $df->metadata['type'] ?? 'slider';
                                $defaultValue = $df->defaults[$index] ?? 1;
                                $min = $df->metadata['min'] ?? 0;
                                $max = $df->metadata['max'] ?? 5;
                                $step = $df->metadata['step'] ?? 1;
                            @endphp

                            <div class="input-group" data-df="{{ $df->getNumberFromCode() }}" data-index="{{ $index }}">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <span class="text-sm font-bold text-blue-600 value-display" id="value-df{{ $df->getNumberFromCode() }}-{{ $index }}">{{ $defaultValue }}</span>
                                </div>

                                @if($inputType === 'slider')
                                <input
                                    type="range"
                                    min="{{ $min }}"
                                    max="{{ $max }}"
                                    step="{{ $step }}"
                                    value="{{ $defaultValue }}"
                                    class="slider input-control"
                                    id="input-df{{ $df->getNumberFromCode() }}-{{ $index }}"
                                    data-df="{{ $df->getNumberFromCode() }}"
                                    data-index="{{ $index }}"

                                >
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>{{ $min }}</span>
                                    <span>{{ round(($min + $max) / 2, 1) }}</span>
                                    <span>{{ $max }}</span>
                                </div>

                                @elseif($inputType === 'dropdown')
                                <select
                                    class="w-full p-2 border border-gray-300 rounded-lg input-control"
                                    id="input-df{{ $df->getNumberFromCode() }}-{{ $index }}"
                                    data-df="{{ $df->getNumberFromCode() }}"
                                    data-index="{{ $index }}"
                                    onchange="updateDFValue({{ $df->getNumberFromCode() }}, {{ $index }}, this.value)"
                                >
                                    @foreach($df->metadata['options'] ?? [] as $option)
                                    <option value="{{ $option['value'] }}" {{ $option['value'] == $defaultValue ? 'selected' : '' }}>
                                        {{ $option['label'] }}
                                    </option>
                                    @endforeach
                                </select>

                                @elseif($inputType === 'checkbox')
                                <div class="flex items-center space-x-3">
                                    <input
                                        type="checkbox"
                                        class="w-4 h-4 text-blue-600 input-control"
                                        id="input-df{{ $df->getNumberFromCode() }}-{{ $index }}"
                                        data-df="{{ $df->getNumberFromCode() }}"
                                        data-index="{{ $index }}"
                                        {{ $defaultValue > 0.5 ? 'checked' : '' }}
                                        onchange="updateDFValue({{ $df->getNumberFromCode() }}, {{ $index }}, this.checked ? 1 : 0)"
                                    >
                                    <label for="input-df{{ $df->getNumberFromCode() }}-{{ $index }}" class="text-sm text-gray-700">
                                        Activé
                                    </label>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <!-- Actions pour ce DF -->
                        <div class="mt-8 flex space-x-4">
                            <button onclick="saveDFData({{ $df->getNumberFromCode() }})" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>Sauvegarder {{ $df->code }}
                            </button>
                            <button onclick="resetDF({{ $df->getNumberFromCode() }})" class="btn btn-secondary">
                                <i class="fas fa-undo mr-2"></i>Réinitialiser
                            </button>
                        </div>
                    </div>

                    <!-- Section des résultats et graphiques -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-chart-line mr-2 text-green-600"></i>
                            Résultats et Visualisation
                        </h3>

                        <!-- Tableau des objectifs spécifiques au DF -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="text-md font-semibold flex items-center">
                                    <i class="fas fa-table mr-2 text-indigo-600"></i>
                                    Objectifs Impactés par {{ $df->code }}
                                    <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full" id="objectives-count-df{{ $df->getNumberFromCode() }}">0</span>
                                </h4>
                                <div class="flex space-x-2 mt-2">
                                    <button onclick="sortDFObjectives({{ $df->getNumberFromCode() }}, 'score')" class="btn btn-xs btn-secondary">
                                        <i class="fas fa-sort-numeric-down mr-1"></i>Score
                                    </button>
                                    <button onclick="sortDFObjectives({{ $df->getNumberFromCode() }}, 'ri')" class="btn btn-xs btn-secondary">
                                        <i class="fas fa-sort-amount-down mr-1"></i>RI
                                    </button>
                                    <button onclick="sortDFObjectives({{ $df->getNumberFromCode() }}, 'gap')" class="btn btn-xs btn-secondary">
                                        <i class="fas fa-sort-numeric-down mr-1"></i>Écart
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="overflow-x-auto max-h-64">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Objectif</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Domaine</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Baseline</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">RI</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Écart</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Impact</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="objectives-table-df{{ $df->getNumberFromCode() }}">
                                            <!-- Les objectifs seront injectés ici par JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Graphique Radar par Domaine -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="text-md font-semibold flex items-center">
                                    <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                                    Performance par Domaine
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="radarChart-df{{ $df->getNumberFromCode() }}"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Graphique en Barres - Top Objectifs -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="text-md font-semibold flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-purple-600"></i>
                                    Top 10 Objectifs Impactés
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="barChart-df{{ $df->getNumberFromCode() }}"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- IA Bundle Simple -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="text-md font-semibold flex items-center">
                                    <i class="fas fa-brain mr-2 text-purple-600"></i>
                                    IA Bundle - Analyse Intelligente
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="space-y-3">
                                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-3 rounded-lg">
                                        <div class="text-sm font-medium text-purple-800">Recommandation IA</div>
                                        <div class="text-sm text-purple-700 mt-1" id="ai-recommendation-df{{ $df->getNumberFromCode() }}">
                                            Analysez vos paramètres pour obtenir des recommandations...
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="text-center p-2 bg-green-50 rounded">
                                            <div class="text-lg font-bold text-green-600" id="ai-score-df{{ $df->getNumberFromCode() }}">-</div>
                                            <div class="text-xs text-gray-600">Score IA</div>
                                        </div>
                                        <div class="text-center p-2 bg-yellow-50 rounded">
                                            <div class="text-lg font-bold text-yellow-600" id="ai-risk-df{{ $df->getNumberFromCode() }}">-</div>
                                            <div class="text-xs text-gray-600">Niveau Risque</div>
                                        </div>
                                        <div class="text-center p-2 bg-blue-50 rounded">
                                            <div class="text-lg font-bold text-blue-600" id="ai-priority-df{{ $df->getNumberFromCode() }}">-</div>
                                            <div class="text-xs text-gray-600">Priorité IA</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques en temps réel -->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-md font-semibold flex items-center">
                                    <i class="fas fa-calculator mr-2 text-orange-600"></i>
                                    Métriques {{ $df->code }}
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-600" id="avg-score-df{{ $df->getNumberFromCode() }}">0.0</div>
                                        <div class="text-xs text-gray-600">Score Moyen</div>
                                    </div>
                                    <div class="text-center p-3 bg-green-50 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600" id="max-impact-df{{ $df->getNumberFromCode() }}">0</div>
                                        <div class="text-xs text-gray-600">Impact Max</div>
                                    </div>
                                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                                        <div class="text-2xl font-bold text-purple-600" id="affected-objectives-df{{ $df->getNumberFromCode() }}">0</div>
                                        <div class="text-xs text-gray-600">Objectifs Affectés</div>
                                    </div>
                                    <div class="text-center p-3 bg-orange-50 rounded-lg">
                                        <div class="text-2xl font-bold text-orange-600" id="completion-df{{ $df->getNumberFromCode() }}">0%</div>
                                        <div class="text-xs text-gray-600">Complétude</div>
                                    </div>
                                </div>

                                <!-- Indicateur de validation -->
                                <div class="mt-4 text-center">
                                    <div class="inline-flex items-center px-4 py-2 rounded-full" id="validation-status-df{{ $df->getNumberFromCode() }}">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span>En attente de validation</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Section Résultats Globaux -->
        <div id="results-content" class="df-content card hidden">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-chart-line mr-3 text-green-600"></i>
                            Résultats Globaux
                        </h2>
                        <p class="text-gray-600 mt-1">Analyse complète de tous les Design Factors</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="exportResults()" class="btn btn-primary">
                            <i class="fas fa-file-pdf mr-2"></i>Export PDF
                        </button>
                        <button onclick="exportJSON()" class="btn btn-secondary">
                            <i class="fas fa-download mr-2"></i>Export JSON
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Résumé des métriques -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="card">
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-list text-blue-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-gray-900" id="total-objectives">40</div>
                            <div class="text-sm text-gray-600">Objectifs Évalués</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-red-600" id="high-priority">0</div>
                            <div class="text-sm text-gray-600">Priorité Haute</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-yellow-600" id="medium-priority">0</div>
                            <div class="text-sm text-gray-600">Priorité Moyenne</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-green-600" id="low-priority">0</div>
                            <div class="text-sm text-gray-600">Priorité Faible</div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques globaux -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Radar global -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-xl font-semibold text-gray-900">Performance Globale par Domaine</h3>
                        </div>
                        <div class="p-6">
                            <div class="chart-container">
                                <canvas id="globalRadarChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Analyse des gaps -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-xl font-semibold text-gray-900">Analyse des Écarts</h3>
                        </div>
                        <div class="p-6">
                            <div class="chart-container">
                                <canvas id="gapAnalysisChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des résultats -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-xl font-semibold text-gray-900">Résultats Détaillés par Objectif</h3>
                        <div class="flex space-x-2">
                            <button onclick="sortResults('objective')" class="btn btn-sm btn-secondary">
                                <i class="fas fa-sort-alpha-down mr-1"></i>Trier par Objectif
                            </button>
                            <button onclick="sortResults('priority')" class="btn btn-sm btn-secondary">
                                <i class="fas fa-sort-amount-down mr-1"></i>Trier par Priorité
                            </button>
                            <button onclick="sortResults('gap')" class="btn btn-sm btn-secondary">
                                <i class="fas fa-sort-numeric-down mr-1"></i>Trier par Écart
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="resultsTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Objectif</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domaine</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Baseline</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Écart</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="resultsTableBody">
                                    <!-- Les résultats seront injectés ici par JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Configuration globale
    let currentDF = 1;
    let evaluationData = {};
    let charts = {};
    let designFactorsData = @json($designFactors);
    let designFactors = [];

    // Convertir les design factors en array si c'est un objet
    if (Array.isArray(designFactorsData)) {
        designFactors = designFactorsData;
    } else if (typeof designFactorsData === 'object') {
        designFactors = Object.values(designFactorsData);
    }

    console.log('Design Factors loaded:', designFactors);

    // Données COBIT 2019
    const cobitData = {
        objectives: [
            'EDM01', 'EDM02', 'EDM03', 'EDM04', 'EDM05',
            'APO01', 'APO02', 'APO03', 'APO04', 'APO05', 'APO06', 'APO07', 'APO08', 'APO09', 'APO10', 'APO11', 'APO12', 'APO13', 'APO14',
            'BAI01', 'BAI02', 'BAI03', 'BAI04', 'BAI05', 'BAI06', 'BAI07', 'BAI08', 'BAI09', 'BAI10', 'BAI11',
            'DSS01', 'DSS02', 'DSS03', 'DSS04', 'DSS05', 'DSS06',
            'MEA01', 'MEA02', 'MEA03', 'MEA04'
        ],
        domains: {
            'EDM': ['EDM01', 'EDM02', 'EDM03', 'EDM04', 'EDM05'],
            'APO': ['APO01', 'APO02', 'APO03', 'APO04', 'APO05', 'APO06', 'APO07', 'APO08', 'APO09', 'APO10', 'APO11', 'APO12', 'APO13', 'APO14'],
            'BAI': ['BAI01', 'BAI02', 'BAI03', 'BAI04', 'BAI05', 'BAI06', 'BAI07', 'BAI08', 'BAI09', 'BAI10', 'BAI11'],
            'DSS': ['DSS01', 'DSS02', 'DSS03', 'DSS04', 'DSS05', 'DSS06'],
            'MEA': ['MEA01', 'MEA02', 'MEA03', 'MEA04']
        }
    };

    // Matrices de calcul COBIT (simplifiées pour la démo)
    const cobitMatrices = {
        DF1: generateMatrix(40, 4, 0.1, 0.3),
        DF2: generateMatrix(40, 4, 0.05, 0.25),
        DF3: generateMatrix(40, 4, 0.08, 0.28),
        DF4: generateMatrix(40, 4, 0.12, 0.35),
        DF5: generateMatrix(40, 2, 0.15, 0.4),
        DF6: generateMatrix(40, 3, 0.1, 0.3),
        DF7: generateMatrix(40, 3, 0.08, 0.25),
        DF8: generateMatrix(40, 2, 0.06, 0.2),
        DF9: generateMatrix(40, 3, 0.1, 0.3),
        DF10: generateMatrix(40, 3, 0.05, 0.2)
    };

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        console.log('COBIT Evaluation App with AI features initialized');
        initializeEvaluationData();
        updateAllCharts();
        updateProgress();
        updateGlobalProgress();

        // Initialiser les tableaux d'objectifs pour tous les DF
        for (let i = 1; i <= 10; i++) {
            updateDFObjectivesTable(i, evaluationData[`DF${i}`].scores || []);
        }
    });

    // Générer une matrice de calcul
    function generateMatrix(objectives, inputs, minWeight, maxWeight) {
        const matrix = [];
        for (let i = 0; i < objectives; i++) {
            const row = [];
            for (let j = 0; j < inputs; j++) {
                row.push(Math.random() * (maxWeight - minWeight) + minWeight);
            }
            matrix.push(row);
        }
        return matrix;
    }

    // Initialiser les données d'évaluation
    function initializeEvaluationData() {
        console.log('Initializing evaluation data...');
        console.log('designFactorsData:', designFactorsData);

        // Convertir les design factors en array si nécessaire
        if (Array.isArray(designFactorsData)) {
            designFactors = designFactorsData;
        } else if (typeof designFactorsData === 'object') {
            designFactors = Object.values(designFactorsData);
        }

        console.log('Processed designFactors:', designFactors);

        if (!Array.isArray(designFactors)) {
            console.error('designFactors is not an array:', designFactors);
            return;
        }

        designFactors.forEach(df => {
            if (!df || !df.code) {
                console.error('Invalid DF:', df);
                return;
            }

            const dfNumber = df.code.replace('DF', '');
            const defaults = df.defaults || [1, 1, 1, 1];

            evaluationData[`DF${dfNumber}`] = {
                inputs: [...defaults],
                scores: new Array(cobitData.objectives.length).fill(0),
                baselines: new Array(cobitData.objectives.length).fill(2.5)
            };

            console.log(`Initialized DF${dfNumber}:`, evaluationData[`DF${dfNumber}`]);
        });

        console.log('Final evaluationData:', evaluationData);
    }

    // Navigation entre les DF
    function switchToDF(dfNumber) {
        // Masquer tous les contenus
        document.querySelectorAll('.df-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Afficher le contenu du DF sélectionné
        document.getElementById(`df${dfNumber}-content`).classList.remove('hidden');

        // Mettre à jour les onglets
        document.querySelectorAll('.df-tab').forEach(tab => {
            tab.classList.remove('bg-blue-600', 'text-white');
            tab.classList.add('bg-gray-100', 'text-gray-700');
        });

        document.getElementById(`tab-df${dfNumber}`).classList.remove('bg-gray-100', 'text-gray-700');
        document.getElementById(`tab-df${dfNumber}`).classList.add('bg-blue-600', 'text-white');

        currentDF = dfNumber;
        updateDFCharts(dfNumber);
    }

    // Basculer vers les résultats
    function switchToResults() {
        // Masquer tous les contenus
        document.querySelectorAll('.df-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Afficher les résultats
        document.getElementById('results-content').classList.remove('hidden');

        // Mettre à jour les onglets
        document.querySelectorAll('.df-tab').forEach(tab => {
            tab.classList.remove('bg-blue-600', 'text-white', 'bg-green-600');
            tab.classList.add('bg-gray-100', 'text-gray-700');
        });

        document.getElementById('tab-results').classList.remove('bg-gray-100', 'text-gray-700', 'bg-green-100', 'text-green-700');
        document.getElementById('tab-results').classList.add('bg-green-600', 'text-white');

        calculateGlobalResults();
    }

    // Mettre à jour une valeur de DF
    function updateDFValue(dfNumber, index, value) {
        const numValue = parseFloat(value);
        evaluationData[`DF${dfNumber}`].inputs[index] = numValue;

        // Mettre à jour l'affichage de la valeur
        document.getElementById(`value-df${dfNumber}-${index}`).textContent = numValue;

        // Recalculer les scores
        calculateDFScores(dfNumber);

        // Mettre à jour les graphiques
        updateDFCharts(dfNumber);

        // Mettre à jour les statistiques
        updateDFStats(dfNumber);

        // Mettre à jour la progression
        updateProgress();
    }

    // Calculer les scores pour un DF
    function calculateDFScores(dfNumber) {
        console.log(`Calculating scores for DF${dfNumber}`);

        // Vérifier que les données existent
        if (!evaluationData[`DF${dfNumber}`]) {
            console.error(`No data found for DF${dfNumber}`);
            return;
        }

        const dfData = evaluationData[`DF${dfNumber}`];
        const inputs = dfData.inputs;

        if (!inputs || !Array.isArray(inputs)) {
            console.error(`Invalid inputs for DF${dfNumber}:`, inputs);
            return;
        }

        const matrix = cobitMatrices[`DF${dfNumber}`];
        if (!matrix) {
            console.error(`No matrix found for DF${dfNumber}`);
            return;
        }

        const scores = [];
        const baselines = [];

        // Calculer les scores
        for (let i = 0; i < cobitData.objectives.length; i++) {
            let score = 0;
            for (let j = 0; j < inputs.length; j++) {
                score += (matrix[i] && matrix[i][j] ? matrix[i][j] : 0) * (inputs[j] || 0);
            }
            scores.push(Math.round(score * 100) / 100);
            baselines.push(2.5); // Baseline par défaut
        }

        evaluationData[`DF${dfNumber}`].scores = scores;
        evaluationData[`DF${dfNumber}`].baselines = baselines;

        console.log(`Scores calculated for DF${dfNumber}:`, scores.slice(0, 5));
    }

    // Mettre à jour les graphiques d'un DF
    function updateDFCharts(dfNumber) {
        calculateDFScores(dfNumber);
        createDFRadarChart(dfNumber);
        createDFBarChart(dfNumber);
    }

    // Créer le graphique radar pour un DF
    function createDFRadarChart(dfNumber) {
        const ctx = document.getElementById(`radarChart-df${dfNumber}`);
        if (!ctx) return;

        const chartKey = `radar-df${dfNumber}`;
        if (charts[chartKey]) {
            charts[chartKey].destroy();
        }

        const domainData = calculateDomainAverages(dfNumber);

        charts[chartKey] = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: domainData.labels,
                datasets: [
                    {
                        label: `Scores DF${dfNumber}`,
                        data: domainData.avgData,
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Baseline',
                        data: domainData.baselineData,
                        backgroundColor: 'rgba(107, 114, 128, 0.2)',
                        borderColor: 'rgba(107, 114, 128, 1)',
                        pointBackgroundColor: 'rgba(107, 114, 128, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Créer le graphique en barres pour un DF
    function createDFBarChart(dfNumber) {
        const ctx = document.getElementById(`barChart-df${dfNumber}`);
        if (!ctx) return;

        const chartKey = `bar-df${dfNumber}`;
        if (charts[chartKey]) {
            charts[chartKey].destroy();
        }

        const scores = evaluationData[`DF${dfNumber}`].scores;
        const topScores = scores.slice(0, 10);
        const topObjectives = cobitData.objectives.slice(0, 10);

        charts[chartKey] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: topObjectives,
                datasets: [{
                    label: `Impact DF${dfNumber}`,
                    data: topScores,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
    }

    // Calculer les moyennes par domaine
    function calculateDomainAverages(dfNumber) {
        const scores = evaluationData[`DF${dfNumber}`].scores;
        const baselines = evaluationData[`DF${dfNumber}`].baselines;

        const domainAverages = {};
        const domainBaselines = {};
        const domainCounts = {};

        // Initialiser
        Object.keys(cobitData.domains).forEach(domain => {
            domainAverages[domain] = 0;
            domainBaselines[domain] = 0;
            domainCounts[domain] = 0;
        });

        // Calculer les moyennes
        cobitData.objectives.forEach((objective, index) => {
            const domain = getDomain(objective);
            if (domain) {
                domainAverages[domain] += scores[index] || 0;
                domainBaselines[domain] += baselines[index] || 0;
                domainCounts[domain]++;
            }
        });

        // Finaliser les moyennes
        const labels = [];
        const avgData = [];
        const baselineData = [];

        Object.keys(cobitData.domains).forEach(domain => {
            if (domainCounts[domain] > 0) {
                labels.push(domain);
                avgData.push(Math.round((domainAverages[domain] / domainCounts[domain]) * 100) / 100);
                baselineData.push(Math.round((domainBaselines[domain] / domainCounts[domain]) * 100) / 100);
            }
        });

        return { labels, avgData, baselineData };
    }

    // Obtenir le domaine d'un objectif
    function getDomain(objective) {
        for (const [domain, objectives] of Object.entries(cobitData.domains)) {
            if (objectives.includes(objective)) {
                return domain;
            }
        }
        return null;
    }

    // Mettre à jour les statistiques d'un DF
    function updateDFStats(dfNumber) {
        const dfData = evaluationData[`DF${dfNumber}`];
        if (!dfData) {
            console.warn(`No data for DF${dfNumber} stats`);
            return;
        }

        const scores = dfData.scores || [];
        const inputs = dfData.inputs || [];

        // Vérifier que les éléments existent
        const avgScoreEl = document.getElementById(`avg-score-df${dfNumber}`);
        const maxImpactEl = document.getElementById(`max-impact-df${dfNumber}`);
        const affectedObjectivesEl = document.getElementById(`affected-objectives-df${dfNumber}`);
        const completionEl = document.getElementById(`completion-df${dfNumber}`);

        if (scores.length > 0) {
            // Score moyen
            const avgScore = scores.reduce((a, b) => a + b, 0) / scores.length;
            if (avgScoreEl) avgScoreEl.textContent = avgScore.toFixed(1);

            // Impact maximum
            const maxImpact = Math.max(...scores);
            if (maxImpactEl) maxImpactEl.textContent = maxImpact.toFixed(1);

            // Objectifs affectés (score > 0.1)
            const affectedObjectives = scores.filter(score => score > 0.1).length;
            if (affectedObjectivesEl) affectedObjectivesEl.textContent = affectedObjectives;

            // Mettre à jour le tableau des objectifs
            updateDFObjectivesTable(dfNumber, scores);
        }

        if (inputs.length > 0) {
            // Complétude (pourcentage d'inputs non-zéro)
            const nonZeroInputs = inputs.filter(input => input > 0).length;
            const completion = Math.round((nonZeroInputs / inputs.length) * 100);
            if (completionEl) completionEl.textContent = completion + '%';

            // Mettre à jour l'IA Bundle
            updateAIBundle(dfNumber, inputs, scores);

            // Mettre à jour le statut de validation
            updateValidationStatus(dfNumber, completion);
        }
    }

    // Mettre à jour le tableau des objectifs pour un DF
    function updateDFObjectivesTable(dfNumber, scores) {
        const tableBody = document.getElementById(`objectives-table-df${dfNumber}`);
        const countEl = document.getElementById(`objectives-count-df${dfNumber}`);

        if (!tableBody) return;

        const baselines = evaluationData[`DF${dfNumber}`].baselines || [];
        const objectives = [];

        // Créer les données des objectifs
        cobitData.objectives.forEach((objective, index) => {
            const score = scores[index] || 0;
            const baseline = baselines[index] || 2.5;
            const gap = score - baseline;
            const ri = calculateRelativeImportance(score, baseline);
            const domain = getDomain(objective);

            if (score > 0.05) { // Seulement les objectifs avec un impact significatif
                objectives.push({
                    objective,
                    domain,
                    score: score.toFixed(2),
                    baseline: baseline.toFixed(2),
                    ri: ri.toFixed(2),
                    gap: gap.toFixed(2),
                    impact: getImpactLevel(score)
                });
            }
        });

        // Trier par score décroissant
        objectives.sort((a, b) => parseFloat(b.score) - parseFloat(a.score));

        // Mettre à jour le compteur
        if (countEl) countEl.textContent = objectives.length;

        // Remplir le tableau
        tableBody.innerHTML = '';
        objectives.forEach(obj => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            const impactClass = obj.impact === 'High' ? 'text-red-600' :
                               obj.impact === 'Medium' ? 'text-yellow-600' : 'text-green-600';

            row.innerHTML = `
                <td class="px-3 py-2 text-sm font-medium">${obj.objective}</td>
                <td class="px-3 py-2 text-sm">${obj.domain}</td>
                <td class="px-3 py-2 text-sm font-bold text-blue-600">${obj.score}</td>
                <td class="px-3 py-2 text-sm">${obj.baseline}</td>
                <td class="px-3 py-2 text-sm font-bold text-purple-600">${obj.ri}</td>
                <td class="px-3 py-2 text-sm ${obj.gap >= 0 ? 'text-green-600' : 'text-red-600'}">
                    ${obj.gap > 0 ? '+' : ''}${obj.gap}
                </td>
                <td class="px-3 py-2 text-sm">
                    <span class="px-2 py-1 text-xs rounded-full ${impactClass} bg-opacity-20">
                        ${obj.impact}
                    </span>
                </td>
            `;

            tableBody.appendChild(row);
        });
    }

    // Mettre à jour la progression globale
    function updateProgress() {
        let completedDFs = 0;

        designFactors.forEach(df => {
            const dfNumber = df.code.replace('DF', '');
            const inputs = evaluationData[`DF${dfNumber}`].inputs;
            const hasNonZeroInputs = inputs.some(input => input > 0);

            if (hasNonZeroInputs) {
                completedDFs++;
            }
        });

        const percentage = Math.round((completedDFs / designFactors.length) * 100);
        document.getElementById('progress-bar').style.width = percentage + '%';
        document.getElementById('progress-text').textContent = `${completedDFs}/${designFactors.length} complétés`;
    }

    // Mettre à jour tous les graphiques
    function updateAllCharts() {
        designFactors.forEach(df => {
            const dfNumber = df.code.replace('DF', '');
            updateDFCharts(dfNumber);
            updateDFStats(dfNumber);
        });
    }

    // Mettre à jour l'IA Bundle
    function updateAIBundle(dfNumber, inputs, scores) {
        const recommendationEl = document.getElementById(`ai-recommendation-df${dfNumber}`);
        const aiScoreEl = document.getElementById(`ai-score-df${dfNumber}`);
        const aiRiskEl = document.getElementById(`ai-risk-df${dfNumber}`);
        const aiPriorityEl = document.getElementById(`ai-priority-df${dfNumber}`);

        if (!recommendationEl) return;

        // Calculer les métriques IA
        const avgInput = inputs.reduce((a, b) => a + b, 0) / inputs.length;
        const avgScore = scores.reduce((a, b) => a + b, 0) / scores.length;
        const completion = inputs.filter(i => i > 0).length / inputs.length;

        // Score IA (0-100)
        const aiScore = Math.round((avgScore / 5) * 100);

        // Niveau de risque
        let riskLevel = 'Faible';
        let riskColor = 'text-green-600';
        if (avgScore < 2) {
            riskLevel = 'Élevé';
            riskColor = 'text-red-600';
        } else if (avgScore < 3.5) {
            riskLevel = 'Moyen';
            riskColor = 'text-yellow-600';
        }

        // Priorité IA
        let priority = 'Basse';
        if (completion > 0.8 && avgScore > 3) {
            priority = 'Haute';
        } else if (completion > 0.5) {
            priority = 'Moyenne';
        }

        // Recommandation intelligente
        let recommendation = '';
        if (completion < 0.3) {
            recommendation = `🔍 Complétez d'abord l'évaluation (${Math.round(completion * 100)}% terminé)`;
        } else if (avgScore < 2) {
            recommendation = `⚠️ Scores critiques détectés. Priorisez l'amélioration des processus.`;
        } else if (avgScore > 4) {
            recommendation = `✅ Excellente performance ! Maintenez ces standards élevés.`;
        } else {
            recommendation = `📈 Performance correcte. Identifiez les domaines d'amélioration.`;
        }

        // Mettre à jour l'interface
        recommendationEl.textContent = recommendation;
        if (aiScoreEl) aiScoreEl.textContent = aiScore;
        if (aiRiskEl) {
            aiRiskEl.textContent = riskLevel;
            aiRiskEl.className = `text-lg font-bold ${riskColor}`;
        }
        if (aiPriorityEl) aiPriorityEl.textContent = priority;
    }

    // Mettre à jour le statut de validation
    function updateValidationStatus(dfNumber, completion) {
        const statusEl = document.getElementById(`validation-status-df${dfNumber}`);
        const indicatorEl = document.getElementById(`status-indicator-df${dfNumber}`);

        let isValidated = completion >= 80; // 80% de complétude minimum

        if (statusEl) {
            if (isValidated) {
                statusEl.className = 'inline-flex items-center px-4 py-2 rounded-full bg-green-100 text-green-800';
                statusEl.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span>DF Validé</span>';
            } else {
                statusEl.className = 'inline-flex items-center px-4 py-2 rounded-full bg-yellow-100 text-yellow-800';
                statusEl.innerHTML = '<i class="fas fa-clock mr-2"></i><span>En cours de validation</span>';
            }
        }

        if (indicatorEl) {
            indicatorEl.style.backgroundColor = isValidated ? '#10B981' : '#6B7280';
        }

        // Mettre à jour le compteur global
        updateGlobalProgress();
    }

    // Mettre à jour le progrès global
    function updateGlobalProgress() {
        let completedCount = 0;

        for (let i = 1; i <= 10; i++) {
            const dfData = evaluationData[`DF${i}`];
            if (dfData && dfData.inputs) {
                const completion = dfData.inputs.filter(input => input > 0).length / dfData.inputs.length;
                if (completion >= 0.8) completedCount++;
            }
        }

        const countEl = document.getElementById('completed-count');
        const resultsTab = document.getElementById('results-tab');

        if (countEl) countEl.textContent = `${completedCount}/10`;

        // Activer l'onglet résultats si tous les DF sont validés
        if (resultsTab) {
            if (completedCount === 10) {
                resultsTab.disabled = false;
                resultsTab.classList.remove('disabled:opacity-50');
                resultsTab.classList.add('bg-green-600', 'text-white');
                resultsTab.classList.remove('bg-green-100', 'text-green-700');
            } else {
                resultsTab.disabled = true;
                resultsTab.classList.add('disabled:opacity-50');
            }
        }
    }

    // Fonctions utilitaires
    function calculateRelativeImportance(score, baseline) {
        return Math.abs(score - baseline) / baseline * 100;
    }

    function getDomain(objective) {
        if (objective.startsWith('APO')) return 'Align, Plan, Organize';
        if (objective.startsWith('BAI')) return 'Build, Acquire, Implement';
        if (objective.startsWith('DSS')) return 'Deliver, Service, Support';
        if (objective.startsWith('MEA')) return 'Monitor, Evaluate, Assess';
        return 'Unknown';
    }

    function getImpactLevel(score) {
        if (score >= 4) return 'High';
        if (score >= 2.5) return 'Medium';
        return 'Low';
    }

    // Fonctions de tri pour les tableaux d'objectifs
    function sortDFObjectives(dfNumber, sortBy) {
        const tableBody = document.getElementById(`objectives-table-df${dfNumber}`);
        if (!tableBody) return;

        const rows = Array.from(tableBody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            let aVal, bVal;

            switch(sortBy) {
                case 'score':
                    aVal = parseFloat(a.cells[2].textContent);
                    bVal = parseFloat(b.cells[2].textContent);
                    break;
                case 'ri':
                    aVal = parseFloat(a.cells[4].textContent);
                    bVal = parseFloat(b.cells[4].textContent);
                    break;
                case 'gap':
                    aVal = parseFloat(a.cells[5].textContent);
                    bVal = parseFloat(b.cells[5].textContent);
                    break;
                default:
                    return 0;
            }

            return bVal - aVal; // Tri décroissant
        });

        // Réinsérer les lignes triées
        tableBody.innerHTML = '';
        rows.forEach(row => tableBody.appendChild(row));
    }

    // Fonction pour afficher les résultats finaux
    function switchToResults() {
        // Masquer tous les DF
        document.querySelectorAll('.df-content').forEach(content => {
            content.style.display = 'none';
        });

        // Afficher la section résultats
        let resultsSection = document.getElementById('results-section');
        if (!resultsSection) {
            resultsSection = createResultsSection();
        }
        resultsSection.style.display = 'block';

        // Mettre à jour les onglets
        document.querySelectorAll('.df-tab').forEach(tab => {
            tab.classList.remove('bg-blue-600', 'text-white');
            tab.classList.add('bg-gray-100', 'text-gray-700');
        });

        document.getElementById('tab-results').classList.remove('bg-green-100', 'text-green-700');
        document.getElementById('tab-results').classList.add('bg-green-600', 'text-white');

        // Générer les résultats finaux
        generateFinalResults();
    }

    // Créer la section des résultats finaux
    function createResultsSection() {
        const container = document.querySelector('.container');
        const resultsSection = document.createElement('div');
        resultsSection.id = 'results-section';
        resultsSection.className = 'df-content';
        resultsSection.style.display = 'none';

        resultsSection.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-trophy mr-3 text-yellow-500"></i>
                        Résultats Finaux COBIT 2019
                    </h2>
                    <p class="text-gray-600">Analyse complète de votre évaluation des 10 Design Factors</p>
                </div>

                <!-- Canvas de résultats -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Graphique radar global -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-chart-area mr-2 text-blue-600"></i>
                                Vue d'ensemble - Radar Chart
                            </h3>
                        </div>
                        <div class="p-4">
                            <canvas id="final-radar-chart" width="400" height="400"></canvas>
                        </div>
                    </div>

                    <!-- Métriques globales -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-analytics mr-2 text-green-600"></i>
                                Métriques Globales
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-3xl font-bold text-blue-600" id="global-score">0.0</div>
                                    <div class="text-sm text-gray-600">Score Global</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <div class="text-3xl font-bold text-green-600" id="maturity-level">0</div>
                                    <div class="text-sm text-gray-600">Niveau Maturité</div>
                                </div>
                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                    <div class="text-3xl font-bold text-purple-600" id="total-objectives">0</div>
                                    <div class="text-sm text-gray-600">Objectifs Impactés</div>
                                </div>
                                <div class="text-center p-4 bg-orange-50 rounded-lg">
                                    <div class="text-3xl font-bold text-orange-600" id="completion-rate">0%</div>
                                    <div class="text-sm text-gray-600">Taux Complétude</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau de bord des DF -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-table mr-2 text-indigo-600"></i>
                            Tableau de Bord des Design Factors
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">DF</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score Moyen</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Complétude</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité IA</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="df-summary-table">
                                    <!-- Contenu généré dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recommandations IA globales -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-brain mr-2 text-purple-600"></i>
                            Recommandations IA Globales
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg">
                            <div id="global-recommendations" class="space-y-3">
                                <!-- Recommandations générées dynamiquement -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(resultsSection);
        return resultsSection;
    }

    // Générer les résultats finaux
    function generateFinalResults() {
        let globalScore = 0;
        let totalObjectives = 0;
        let completedDFs = 0;
        let dfSummaries = [];

        // Calculer les métriques globales
        for (let i = 1; i <= 10; i++) {
            const dfData = evaluationData[`DF${i}`];
            if (dfData && dfData.scores && dfData.inputs) {
                const avgScore = dfData.scores.reduce((a, b) => a + b, 0) / dfData.scores.length;
                const completion = dfData.inputs.filter(input => input > 0).length / dfData.inputs.length;
                const affectedObjectives = dfData.scores.filter(score => score > 0.1).length;

                globalScore += avgScore;
                totalObjectives += affectedObjectives;

                if (completion >= 0.8) completedDFs++;

                // Déterminer la priorité IA
                let priority = 'Basse';
                let priorityColor = 'text-green-600';
                if (completion > 0.8 && avgScore > 3) {
                    priority = 'Haute';
                    priorityColor = 'text-green-600';
                } else if (avgScore < 2) {
                    priority = 'Critique';
                    priorityColor = 'text-red-600';
                } else if (completion > 0.5) {
                    priority = 'Moyenne';
                    priorityColor = 'text-yellow-600';
                }

                dfSummaries.push({
                    df: `DF${i}`,
                    title: designFactors.find(df => df.code === `DF${i}`)?.title || '',
                    avgScore: avgScore.toFixed(2),
                    completion: Math.round(completion * 100),
                    status: completion >= 0.8 ? 'Validé' : 'En cours',
                    priority,
                    priorityColor
                });
            }
        }

        globalScore = globalScore / 10;
        const maturityLevel = Math.round(globalScore);
        const completionRate = Math.round((completedDFs / 10) * 100);

        // Mettre à jour les métriques globales
        document.getElementById('global-score').textContent = globalScore.toFixed(1);
        document.getElementById('maturity-level').textContent = maturityLevel;
        document.getElementById('total-objectives').textContent = totalObjectives;
        document.getElementById('completion-rate').textContent = completionRate + '%';

        // Remplir le tableau de bord des DF
        const tableBody = document.getElementById('df-summary-table');
        tableBody.innerHTML = '';

        dfSummaries.forEach(df => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            const statusClass = df.status === 'Validé' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';

            row.innerHTML = `
                <td class="px-4 py-3 text-sm font-medium">${df.df}</td>
                <td class="px-4 py-3 text-sm">${df.title}</td>
                <td class="px-4 py-3 text-sm font-bold text-blue-600">${df.avgScore}</td>
                <td class="px-4 py-3 text-sm">${df.completion}%</td>
                <td class="px-4 py-3 text-sm">
                    <span class="px-2 py-1 text-xs rounded-full ${statusClass}">
                        ${df.status}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm font-medium ${df.priorityColor}">${df.priority}</td>
            `;

            tableBody.appendChild(row);
        });

        // Générer les recommandations IA globales
        generateGlobalRecommendations(globalScore, completionRate, dfSummaries);

        // Créer le graphique radar final
        createFinalRadarChart(dfSummaries);
    }

    // Générer les recommandations IA globales
    function generateGlobalRecommendations(globalScore, completionRate, dfSummaries) {
        const recommendationsEl = document.getElementById('global-recommendations');
        const recommendations = [];

        // Analyse globale
        if (completionRate < 100) {
            recommendations.push({
                icon: '⚠️',
                type: 'warning',
                text: `Complétez l'évaluation des ${10 - Math.round(completionRate/10)} DF restants pour une analyse complète.`
            });
        }

        if (globalScore < 2) {
            recommendations.push({
                icon: '🚨',
                type: 'critical',
                text: 'Score global critique. Mise en place urgente d\'un plan d\'amélioration requis.'
            });
        } else if (globalScore < 3) {
            recommendations.push({
                icon: '📈',
                type: 'improvement',
                text: 'Score global modéré. Identifiez les domaines prioritaires pour l\'amélioration.'
            });
        } else if (globalScore >= 4) {
            recommendations.push({
                icon: '🏆',
                type: 'success',
                text: 'Excellente performance globale ! Maintenez ces standards élevés.'
            });
        }

        // Recommandations spécifiques par DF
        const criticalDFs = dfSummaries.filter(df => df.priority === 'Critique');
        if (criticalDFs.length > 0) {
            recommendations.push({
                icon: '🎯',
                type: 'action',
                text: `Priorisez l'amélioration des DF critiques : ${criticalDFs.map(df => df.df).join(', ')}`
            });
        }

        const highPriorityDFs = dfSummaries.filter(df => df.priority === 'Haute');
        if (highPriorityDFs.length > 0) {
            recommendations.push({
                icon: '✅',
                type: 'success',
                text: `Excellente performance sur : ${highPriorityDFs.map(df => df.df).join(', ')}`
            });
        }

        // Afficher les recommandations
        recommendationsEl.innerHTML = '';
        recommendations.forEach(rec => {
            const div = document.createElement('div');
            div.className = `flex items-start space-x-3 p-3 rounded-lg ${getRecommendationClass(rec.type)}`;
            div.innerHTML = `
                <span class="text-lg">${rec.icon}</span>
                <span class="text-sm font-medium">${rec.text}</span>
            `;
            recommendationsEl.appendChild(div);
        });
    }

    function getRecommendationClass(type) {
        switch(type) {
            case 'critical': return 'bg-red-100 text-red-800';
            case 'warning': return 'bg-yellow-100 text-yellow-800';
            case 'improvement': return 'bg-blue-100 text-blue-800';
            case 'success': return 'bg-green-100 text-green-800';
            case 'action': return 'bg-purple-100 text-purple-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    // Créer le graphique radar final
    function createFinalRadarChart(dfSummaries) {
        const ctx = document.getElementById('final-radar-chart');
        if (!ctx) return;

        const labels = dfSummaries.map(df => df.df);
        const scores = dfSummaries.map(df => parseFloat(df.avgScore));

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Scores Moyens par DF',
                    data: scores,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(59, 130, 246)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Vue d\'ensemble des Design Factors'
                    }
                }
            }
        });
    }

    // Fonction de reset pour un DF
    function resetDF(dfNumber) {
        if (confirm(`Êtes-vous sûr de vouloir réinitialiser DF${dfNumber} ?`)) {
            // Réinitialiser les inputs
            const inputs = document.querySelectorAll(`input[data-df="${dfNumber}"]`);
            inputs.forEach(input => {
                input.value = 0;
            });

            // Réinitialiser les données
            evaluationData[`DF${dfNumber}`] = {
                inputs: new Array(inputs.length).fill(0),
                scores: [],
                baselines: []
            };

            // Mettre à jour l'affichage
            updateDFData(dfNumber);
            updateDFCharts(dfNumber);
            updateDFStats(dfNumber);
            updateGlobalProgress();
        }
    }

    // Sauvegarder les données d'un DF
    function saveDFData(dfNumber) {
        const inputs = evaluationData[`DF${dfNumber}`].inputs;

        fetch('/cobit/save-evaluation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                step: dfNumber,
                inputs: inputs
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                CobitUtils.showNotification(`DF${dfNumber} sauvegardé avec succès`, 'success');
            } else {
                CobitUtils.showNotification('Erreur lors de la sauvegarde', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            CobitUtils.showNotification('Erreur lors de la sauvegarde', 'error');
        });
    }

    // Réinitialiser un DF
    function resetDF(dfNumber) {
        if (confirm(`Êtes-vous sûr de vouloir réinitialiser DF${dfNumber} ?`)) {
            const df = designFactors.find(d => d.code === `DF${dfNumber}`);
            if (df) {
                evaluationData[`DF${dfNumber}`].inputs = [...df.defaults];

                // Mettre à jour les contrôles
                df.labels.forEach((label, index) => {
                    const input = document.getElementById(`input-df${dfNumber}-${index}`);
                    const valueDisplay = document.getElementById(`value-df${dfNumber}-${index}`);

                    if (input && valueDisplay) {
                        input.value = df.defaults[index];
                        valueDisplay.textContent = df.defaults[index];
                    }
                });

                updateDFCharts(dfNumber);
                updateDFStats(dfNumber);
                updateProgress();

                CobitUtils.showNotification(`DF${dfNumber} réinitialisé`, 'info');
            }
        }
    }

    // Calculer les résultats globaux
    function calculateGlobalResults() {
        console.log('Calculating global results...');

        const globalScores = new Array(cobitData.objectives.length).fill(0);
        const globalBaselines = new Array(cobitData.objectives.length).fill(0);
        let activeDFs = 0;

        // Vérifier que designFactors est un array
        if (!Array.isArray(designFactors)) {
            console.error('designFactors is not an array:', designFactors);
            return;
        }

        // Agréger tous les DF
        designFactors.forEach(df => {
            if (!df || !df.code) {
                console.error('Invalid DF in global calculation:', df);
                return;
            }

            const dfNumber = df.code.replace('DF', '');
            const dfData = evaluationData[`DF${dfNumber}`];

            if (!dfData) {
                console.warn(`No data for DF${dfNumber}`);
                return;
            }

            const scores = dfData.scores || [];
            const baselines = dfData.baselines || [];

            if (scores.length > 0) {
                activeDFs++;
                scores.forEach((score, index) => {
                    globalScores[index] += score || 0;
                    globalBaselines[index] += baselines[index] || 2.5;
                });
            }
        });

        // Calculer les moyennes
        if (activeDFs > 0) {
            globalScores.forEach((score, index) => {
                globalScores[index] = Math.round((score / activeDFs) * 100) / 100;
                globalBaselines[index] = Math.round((globalBaselines[index] / activeDFs) * 100) / 100;
            });
        }

        // Calculer les priorités
        const results = [];
        let highPriority = 0, mediumPriority = 0, lowPriority = 0;

        cobitData.objectives.forEach((objective, index) => {
            const score = globalScores[index];
            const baseline = globalBaselines[index];
            const gap = score - baseline;
            const absGap = Math.abs(gap);

            let priority = 'L';
            if (absGap > baseline * 0.5) {
                priority = 'H';
                highPriority++;
            } else if (absGap > baseline * 0.2) {
                priority = 'M';
                mediumPriority++;
            } else {
                lowPriority++;
            }

            results.push({
                objective,
                domain: getDomain(objective),
                score: score,
                baseline: baseline,
                gap: Math.round(gap * 100) / 100,
                priority
            });
        });

        // Mettre à jour l'affichage
        document.getElementById('total-objectives').textContent = cobitData.objectives.length;
        document.getElementById('high-priority').textContent = highPriority;
        document.getElementById('medium-priority').textContent = mediumPriority;
        document.getElementById('low-priority').textContent = lowPriority;

        // Créer les graphiques globaux
        createGlobalRadarChart(globalScores, globalBaselines);
        createGapAnalysisChart(results);

        // Remplir le tableau
        populateResultsTable(results);
    }

    // Créer le graphique radar global
    function createGlobalRadarChart(scores, baselines) {
        const ctx = document.getElementById('globalRadarChart');
        if (!ctx) return;

        if (charts.globalRadar) {
            charts.globalRadar.destroy();
        }

        const domainData = calculateGlobalDomainAverages(scores, baselines);

        charts.globalRadar = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: domainData.labels,
                datasets: [
                    {
                        label: 'Scores Globaux',
                        data: domainData.avgData,
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Baseline',
                        data: domainData.baselineData,
                        backgroundColor: 'rgba(107, 114, 128, 0.2)',
                        borderColor: 'rgba(107, 114, 128, 1)',
                        pointBackgroundColor: 'rgba(107, 114, 128, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Calculer les moyennes globales par domaine
    function calculateGlobalDomainAverages(scores, baselines) {
        const domainAverages = {};
        const domainBaselines = {};
        const domainCounts = {};

        // Initialiser
        Object.keys(cobitData.domains).forEach(domain => {
            domainAverages[domain] = 0;
            domainBaselines[domain] = 0;
            domainCounts[domain] = 0;
        });

        // Calculer
        cobitData.objectives.forEach((objective, index) => {
            const domain = getDomain(objective);
            if (domain) {
                domainAverages[domain] += scores[index] || 0;
                domainBaselines[domain] += baselines[index] || 0;
                domainCounts[domain]++;
            }
        });

        // Finaliser
        const labels = [];
        const avgData = [];
        const baselineData = [];

        Object.keys(cobitData.domains).forEach(domain => {
            if (domainCounts[domain] > 0) {
                labels.push(domain);
                avgData.push(Math.round((domainAverages[domain] / domainCounts[domain]) * 100) / 100);
                baselineData.push(Math.round((domainBaselines[domain] / domainCounts[domain]) * 100) / 100);
            }
        });

        return { labels, avgData, baselineData };
    }

    // Créer le graphique d'analyse des écarts
    function createGapAnalysisChart(results) {
        const ctx = document.getElementById('gapAnalysisChart');
        if (!ctx) return;

        if (charts.gapAnalysis) {
            charts.gapAnalysis.destroy();
        }

        // Trier par écart absolu et prendre les 15 premiers
        const sortedResults = results.sort((a, b) => Math.abs(b.gap) - Math.abs(a.gap)).slice(0, 15);

        charts.gapAnalysis = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sortedResults.map(r => r.objective),
                datasets: [{
                    label: 'Écart (Score - Baseline)',
                    data: sortedResults.map(r => r.gap),
                    backgroundColor: sortedResults.map(r =>
                        r.gap >= 0 ? 'rgba(34, 197, 94, 0.7)' : 'rgba(239, 68, 68, 0.7)'
                    ),
                    borderColor: sortedResults.map(r =>
                        r.gap >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)'
                    ),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
    }

    // Remplir le tableau des résultats
    function populateResultsTable(results) {
        const tbody = document.getElementById('resultsTableBody');
        if (!tbody) {
            console.warn('Table body not found');
            return;
        }

        tbody.innerHTML = '';

        results.forEach(result => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            const priorityClass = result.priority === 'H' ? 'bg-red-100 text-red-800' :
                                 result.priority === 'M' ? 'bg-yellow-100 text-yellow-800' :
                                 'bg-green-100 text-green-800';

            const priorityText = CobitUtils.getPriorityText(result.priority);

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${result.objective}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${result.domain}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${CobitUtils.formatNumber(result.score)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${CobitUtils.formatNumber(result.baseline)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm ${result.gap >= 0 ? 'text-green-600' : 'text-red-600'}">
                    ${result.gap > 0 ? '+' : ''}${CobitUtils.formatNumber(result.gap)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${priorityClass}">
                        ${priorityText}
                    </span>
                </td>
            `;

            tbody.appendChild(row);
        });

        console.log(`Table populated with ${results.length} results`);
    }

    // Fonctions utilitaires
    function saveAllData() {
        let savedCount = 0;
        const totalDFs = designFactors.length;

        designFactors.forEach((df, index) => {
            const dfNumber = df.code.replace('DF', '');
            setTimeout(() => {
                saveDFData(dfNumber);
                savedCount++;

                if (savedCount === totalDFs) {
                    CobitUtils.showNotification('Toutes les données sauvegardées', 'success');
                }
            }, index * 200);
        });
    }

    function exportData() {
        const exportData = {
            timestamp: new Date().toISOString(),
            version: '1.0',
            designFactors: designFactors,
            evaluationData: evaluationData
        };

        CobitExport.exportToJSON(exportData, `cobit-evaluation-${new Date().toISOString().split('T')[0]}.json`);
    }

    function resetAllData() {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les données ?')) {
            designFactors.forEach(df => {
                const dfNumber = df.code.replace('DF', '');
                resetDF(dfNumber);
            });

            CobitUtils.showNotification('Toutes les données réinitialisées', 'info');
        }
    }

    function exportResults() {
        calculateGlobalResults();

        // Préparer les données pour l'export
        const exportData = {
            timestamp: new Date().toISOString(),
            summary: {
                totalObjectives: document.getElementById('total-objectives')?.textContent || '0',
                highPriority: document.getElementById('high-priority')?.textContent || '0',
                mediumPriority: document.getElementById('medium-priority')?.textContent || '0',
                lowPriority: document.getElementById('low-priority')?.textContent || '0'
            },
            designFactors: designFactors.map(df => ({
                code: df.code,
                title: df.title,
                inputs: evaluationData[df.code] ? evaluationData[df.code].inputs : []
            })),
            evaluationData: evaluationData
        };

        CobitExport.exportToJSON(exportData, `cobit-results-${new Date().toISOString().split('T')[0]}.json`);
    }

    function exportJSON() {
        calculateGlobalResults();
        exportData();
    }

    function sortResults(criteria) {
        const tbody = document.getElementById('resultsTableBody');
        if (!tbody) {
            console.warn('Table body not found for sorting');
            return;
        }

        const rows = Array.from(tbody.querySelectorAll('tr'));
        if (rows.length === 0) {
            CobitUtils.showNotification('Aucune donnée à trier', 'warning');
            return;
        }

        rows.sort((a, b) => {
            let aValue, bValue;

            switch (criteria) {
                case 'objective':
                    aValue = a.cells[0].textContent.trim();
                    bValue = b.cells[0].textContent.trim();
                    return aValue.localeCompare(bValue);

                case 'priority':
                    aValue = a.cells[5].textContent.trim();
                    bValue = b.cells[5].textContent.trim();
                    const priorityOrder = { 'Haute': 3, 'Moyenne': 2, 'Faible': 1 };
                    return (priorityOrder[bValue] || 0) - (priorityOrder[aValue] || 0);

                case 'gap':
                    aValue = parseFloat(a.cells[4].textContent.replace('+', ''));
                    bValue = parseFloat(b.cells[4].textContent.replace('+', ''));
                    return Math.abs(bValue) - Math.abs(aValue);

                case 'score':
                    aValue = parseFloat(a.cells[2].textContent);
                    bValue = parseFloat(b.cells[2].textContent);
                    return bValue - aValue;

                default:
                    return 0;
            }
        });

        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));

        CobitUtils.showNotification(`Tableau trié par ${criteria}`, 'info');
    }
</script>
@endpush
