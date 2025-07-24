@extends('layouts.app')

@section('title', 'COBIT 2019 - Évaluation')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cobit-enhanced.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/cobit-evaluation.js') }}"></script>
@endpush

@section('content')
<div class="fade-in">
    <!-- Header avec navigation par onglets -->
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex justify-between items-center">
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
    </div>

    <!-- Contenu des Design Factors -->
    @foreach($designFactors as $df)
    <div id="df{{ $df->getNumberFromCode() }}-content" class="df-content" style="{{ $df->getNumberFromCode() == 1 ? 'display: block;' : 'display: none;' }}">
        <div class="card mb-6">
            <!-- En-tête du DF -->
            <div class="card-header">
                <div class="flex justify-between items-start">
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
                    <!-- Section des paramètres -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-sliders-h mr-2 text-blue-600"></i>
                            Paramètres d'évaluation
                        </h3>
                        
                        <!-- Paramètres d'entrée -->
                        <div class="space-y-6">
                            @foreach($df->parameters as $index => $param)
                            @php
                                $min = $param['min'] ?? 0;
                                $max = $param['max'] ?? 5;
                                $default = $param['default'] ?? 0;
                            @endphp
                            <div class="parameter-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $param['label'] }}
                                    <span class="text-blue-600 font-bold ml-2" id="value-df{{ $df->getNumberFromCode() }}-{{ $index }}">{{ $default }}</span>
                                </label>
                                <input 
                                    type="range" 
                                    min="{{ $min }}" 
                                    max="{{ $max }}" 
                                    value="{{ $default }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                    id="input-df{{ $df->getNumberFromCode() }}-{{ $index }}"
                                    data-df="{{ $df->getNumberFromCode() }}"
                                    data-index="{{ $index }}"
                                >
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>{{ $min }}</span>
                                    <span>{{ $max }}</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">{{ $param['description'] ?? '' }}</p>
                            </div>
                            @endforeach
                        </div>

                        <!-- Actions pour ce DF -->
                        <div class="mt-8 flex space-x-4">
                            <button data-df="{{ $df->getNumberFromCode() }}" class="btn btn-success save-df-btn">
                                <i class="fas fa-save mr-2"></i>Sauvegarder {{ $df->code }}
                            </button>
                            <button data-df="{{ $df->getNumberFromCode() }}" class="btn btn-secondary reset-df-btn">
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
                                    <button data-df="{{ $df->getNumberFromCode() }}" data-sort="score" class="btn btn-xs btn-secondary sort-objectives-btn">
                                        <i class="fas fa-sort-numeric-down mr-1"></i>Score
                                    </button>
                                    <button data-df="{{ $df->getNumberFromCode() }}" data-sort="ri" class="btn btn-xs btn-secondary sort-objectives-btn">
                                        <i class="fas fa-sort-amount-down mr-1"></i>RI
                                    </button>
                                    <button data-df="{{ $df->getNumberFromCode() }}" data-sort="gap" class="btn btn-xs btn-secondary sort-objectives-btn">
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

                        <!-- Graphiques -->
                        <div class="grid grid-cols-1 gap-4 mt-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-md font-semibold">Graphique en Barres</h4>
                                </div>
                                <div class="p-4">
                                    <canvas id="bar-chart-df{{ $df->getNumberFromCode() }}" width="400" height="200"></canvas>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-md font-semibold">Graphique Radar</h4>
                                </div>
                                <div class="p-4">
                                    <canvas id="radar-chart-df{{ $df->getNumberFromCode() }}" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Meta token pour CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
