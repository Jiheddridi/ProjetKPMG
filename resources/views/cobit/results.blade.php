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
        
        <!-- Gap Analysis Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-xl font-semibold text-gray-900">Analyse des Écarts</h3>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="gapChart"></canvas>
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
        createGapChart();
        @endif
    });
    
    function createDomainRadarChart() {
        const ctx = document.getElementById('domainRadarChart');
        if (!ctx) return;
        
        const domainData = @json($finalResults['domainAverages']);
        
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
    
    function createGapChart() {
        const ctx = document.getElementById('gapChart');
        if (!ctx) return;
        
        const objectives = @json($finalResults['objectives']);
        const labels = objectives.map(obj => obj.objective);
        const gaps = objectives.map(obj => obj.gap);
        
        charts.gap = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels.slice(0, 15), // Limiter à 15 pour la lisibilité
                datasets: [{
                    label: 'Écart (Score - Baseline)',
                    data: gaps.slice(0, 15),
                    backgroundColor: gaps.slice(0, 15).map(gap => 
                        gap >= 0 ? 'rgba(34, 197, 94, 0.7)' : 'rgba(239, 68, 68, 0.7)'
                    ),
                    borderColor: gaps.slice(0, 15).map(gap => 
                        gap >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)'
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
