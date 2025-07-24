@extends('layouts.app')

@section('title', 'COBIT 2019 - Résultats')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Résultats de l'Évaluation COBIT 2019</h1>
                    <p class="text-gray-600 mt-1">Analyse complète de votre système de gouvernance IT</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportPDF()" class="btn btn-primary">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </button>
                    <button onclick="exportJSON()" class="btn btn-secondary">
                        <i class="fas fa-download mr-2"></i>Sauvegarder
                    </button>
                    <a href="{{ route('cobit.evaluation') }}" class="btn btn-success">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(isset($finalResults['debug_info']))
    <!-- Informations de Debug pour les Graphiques -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-chart-line mr-2 text-blue-600"></i>État des Graphiques et Données
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $finalResults['debug_info']['total_dfs'] }}/10</div>
                    <div class="text-sm text-gray-600">Design Factors</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $finalResults['debug_info']['ai_generated_dfs'] }}</div>
                    <div class="text-sm text-gray-600">Générés par IA</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $finalResults['debug_info']['completed_dfs'] }}</div>
                    <div class="text-sm text-gray-600">Complétés</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ count($finalResults['objectives']) }}</div>
                    <div class="text-sm text-gray-600">Objectifs Calculés</div>
                </div>
            </div>

            @if($finalResults['debug_info']['ai_generated_dfs'] > 0)
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-robot text-green-600 mr-2"></i>
                    <span class="text-green-800 font-medium">
                        ✅ Graphiques mis à jour avec les données de l'Agent IA Expert
                    </span>
                </div>
                <div class="text-sm text-green-700 mt-1">
                    Les graphiques ci-dessous reflètent les valeurs personnalisées générées par l'IA selon votre profil d'entreprise.
                </div>
            </div>
            @else
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    <span class="text-yellow-800 font-medium">
                        ⚠️ Graphiques basés sur des données manuelles
                    </span>
                </div>
                <div class="text-sm text-yellow-700 mt-1">
                    Pour des graphiques personnalisés, utilisez l'Agent IA Expert lors de la création de l'évaluation.
                </div>
            </div>
            @endif

            <div class="text-xs text-gray-500 mt-3">
                Dernière mise à jour: {{ \Carbon\Carbon::parse($finalResults['debug_info']['calculation_timestamp'])->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
    @endif

    @if(isset($finalResults))
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $finalResults['summary']['totalObjectives'] }}</div>
                <div class="text-sm text-gray-600">Objectifs Évalués</div>
            </div>
        </div>
        
        <div class="card">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="text-2xl font-bold text-red-600">{{ $finalResults['summary']['highPriority'] }}</div>
                <div class="text-sm text-gray-600">Priorité Haute</div>
            </div>
        </div>
        
        <div class="card">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                </div>
                <div class="text-2xl font-bold text-yellow-600">{{ $finalResults['summary']['mediumPriority'] }}</div>
                <div class="text-sm text-gray-600">Priorité Moyenne</div>
            </div>
        </div>
        
        <div class="card">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="text-2xl font-bold text-green-600">{{ $finalResults['summary']['lowPriority'] }}</div>
                <div class="text-sm text-gray-600">Priorité Faible</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Domain Radar Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-xl font-semibold text-gray-900">Performance par Domaine</h3>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="domainRadarChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Performance par Objectifs Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-xl font-semibold text-gray-900">Performance par chaque Objectives</h3>
                <p class="text-gray-600 text-sm mt-1">Les meilleurs objectifs COBIT proposés</p>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="objectivesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Matrix -->
    <div class="card mb-8">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Matrice des Priorités</h3>
            <p class="text-gray-600 mt-1">Objectifs classés par priorité d'amélioration</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- High Priority -->
                <div>
                    <h4 class="font-semibold text-red-600 mb-3 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Priorité Haute
                    </h4>
                    <div class="space-y-2">
                        @foreach($finalResults['objectives'] as $obj)
                            @if($obj['priority'] === 'H')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-red-900">{{ $obj['objective'] }}</span>
                                    <span class="text-sm text-red-600">Gap: {{ $obj['gap'] }}</span>
                                </div>
                                <div class="text-xs text-red-700 mt-1">
                                    Score: {{ $obj['score'] }} | Baseline: {{ $obj['baseline'] }}
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <!-- Medium Priority -->
                <div>
                    <h4 class="font-semibold text-yellow-600 mb-3 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>Priorité Moyenne
                    </h4>
                    <div class="space-y-2">
                        @foreach($finalResults['objectives'] as $obj)
                            @if($obj['priority'] === 'M')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-yellow-900">{{ $obj['objective'] }}</span>
                                    <span class="text-sm text-yellow-600">Gap: {{ $obj['gap'] }}</span>
                                </div>
                                <div class="text-xs text-yellow-700 mt-1">
                                    Score: {{ $obj['score'] }} | Baseline: {{ $obj['baseline'] }}
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <!-- Low Priority -->
                <div>
                    <h4 class="font-semibold text-green-600 mb-3 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>Priorité Faible
                    </h4>
                    <div class="space-y-2">
                        @foreach($finalResults['objectives'] as $obj)
                            @if($obj['priority'] === 'L')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-green-900">{{ $obj['objective'] }}</span>
                                    <span class="text-sm text-green-600">Gap: {{ $obj['gap'] }}</span>
                                </div>
                                <div class="text-xs text-green-700 mt-1">
                                    Score: {{ $obj['score'] }} | Baseline: {{ $obj['baseline'] }}
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Résultats Détaillés</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Objectif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Baseline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Écart</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($finalResults['objectives'] as $obj)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $obj['objective'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $obj['score'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $obj['baseline'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $obj['gap'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $obj['gap'] > 0 ? '+' : '' }}{{ $obj['gap'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $obj['priority'] === 'H' ? 'bg-red-100 text-red-800' : 
                                       ($obj['priority'] === 'M' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $obj['priority'] === 'H' ? 'Haute' : ($obj['priority'] === 'M' ? 'Moyenne' : 'Faible') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @else
    <!-- No Results -->
    <div class="card">
        <div class="p-8 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-bar text-gray-400 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Aucun Résultat Disponible</h2>
            <p class="text-gray-600 mb-6">
                Vous devez d'abord compléter une évaluation pour voir les résultats.
            </p>
            <a href="{{ route('cobit.evaluation') }}" class="btn btn-primary text-lg px-8 py-3">
                <i class="fas fa-play mr-2"></i>Commencer l'Évaluation
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    let charts = {};
    
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($finalResults))
        createDomainRadarChart();
        createObjectivesChart();
        @endif
    });
    
    function createDomainRadarChart() {
        const ctx = document.getElementById('domainRadarChart');
        if (!ctx) return;

        const domainData = @json($finalResults['domainAverages']);

        // Debug des données pour les graphiques
        console.log('🔍 Données Radar Chart:', domainData);
        console.log('📊 Labels:', domainData.labels);
        console.log('📈 Scores actuels:', domainData.avgData);
        console.log('📉 Baselines:', domainData.baselineData);

        // Vérifier si les données ne sont pas toutes à zéro
        const hasRealData = domainData.avgData && domainData.avgData.some(val => val > 0);
        if (!hasRealData) {
            console.warn('⚠️ ATTENTION: Toutes les données du radar chart sont à zéro !');
        } else {
            console.log('✅ Données radar valides détectées');
        }

        charts.domainRadar = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: domainData.labels,
                datasets: [
                    {
                        label: 'Scores Actuels',
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
                        max: 5
                    }
                }
            }
        });
    }
    
    function createObjectivesChart() {
        const ctx = document.getElementById('objectivesChart');
        if (!ctx) return;

        const objectives = @json($finalResults['objectives']);

        // Sélectionner les meilleurs objectifs (score le plus élevé)
        const sortedObjectives = objectives
            .sort((a, b) => b.score - a.score)
            .slice(0, 10); // Top 10 des meilleurs objectifs

        const labels = sortedObjectives.map(obj => obj.objective);
        const scores = sortedObjectives.map(obj => obj.score);
        const priorities = sortedObjectives.map(obj => obj.priority || 3);

        // Debug des données pour le chart des objectifs
        console.log('🔍 Données Objectifs Chart:', sortedObjectives.length, 'meilleurs objectifs');
        console.log('📊 Premiers scores:', scores.slice(0, 5));
        console.log('📈 Score max:', Math.max(...scores));
        console.log('📈 Score min:', Math.min(...scores));

        // Vérifier si les données ne sont pas toutes à zéro
        const hasRealScores = scores.some(val => val > 0.01);
        if (!hasRealScores) {
            console.warn('⚠️ ATTENTION: Tous les scores sont à zéro !');
        } else {
            console.log('✅ Données objectifs valides détectées');
        }

        // Couleurs selon la priorité
        const backgroundColors = priorities.map(priority => {
            if (priority >= 4) return 'rgba(34, 197, 94, 0.8)'; // Vert pour haute priorité
            if (priority >= 3) return 'rgba(59, 130, 246, 0.8)'; // Bleu pour priorité moyenne
            return 'rgba(245, 158, 11, 0.8)'; // Orange pour priorité faible
        });

        const borderColors = priorities.map(priority => {
            if (priority >= 4) return 'rgb(34, 197, 94)';
            if (priority >= 3) return 'rgb(59, 130, 246)';
            return 'rgb(245, 158, 11)';
        });

        charts.objectives = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Score de Performance',
                    data: scores,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const score = context.parsed.y;
                                const priority = priorities[index];
                                const priorityText = priority >= 4 ? 'Haute' : priority >= 3 ? 'Moyenne' : 'Faible';
                                return [
                                    `Score: ${score.toFixed(2)}/5`,
                                    `Priorité: ${priorityText} (${priority})`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        title: {
                            display: true,
                            text: 'Score de Performance (0-5)'
                        },
                        ticks: {
                            stepSize: 0.5
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
    
    function exportPDF() {
        showNotification('Export PDF en cours de développement...', 'info');
        // TODO: Implémenter l'export PDF
    }
    
    function exportJSON() {
        const data = {
            timestamp: new Date().toISOString(),
            results: @json($finalResults ?? []),
            evaluationData: @json($evaluationData ?? [])
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `cobit-evaluation-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        showNotification('Données exportées avec succès', 'success');
    }
</script>
@endpush
