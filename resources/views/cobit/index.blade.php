@extends('layouts.app')

@section('title', 'COBIT 2019 Design Toolkit - Accueil')

@section('content')
<div class="fade-in">
    <!-- Hero Section -->
    <div class="card">
        <div class="card-header">
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cogs text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">COBIT 2019 Design Toolkit</h1>
                <p class="text-lg text-gray-600 mb-6">
                    Évaluez et optimisez votre gouvernance IT avec le framework COBIT 2019
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('cobit.evaluation') }}" class="btn btn-primary text-lg px-8 py-3">
                        <i class="fas fa-play mr-2"></i>Commencer l'évaluation
                    </a>
                    <button onclick="showDemo()" class="btn btn-secondary text-lg px-8 py-3">
                        <i class="fas fa-info-circle mr-2"></i>Voir la démo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Feature 1 -->
        <div class="card">
            <div class="p-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Évaluation Interactive</h3>
                <p class="text-gray-600">
                    Évaluez vos Design Factors avec une interface intuitive et des graphiques en temps réel.
                </p>
            </div>
        </div>

        <!-- Feature 2 -->
        <div class="card">
            <div class="p-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-pie text-green-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Visualisations Avancées</h3>
                <p class="text-gray-600">
                    Graphiques radar, barres et heatmaps pour une analyse complète de vos résultats.
                </p>
            </div>
        </div>

        <!-- Feature 3 -->
        <div class="card">
            <div class="p-6">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-file-export text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Export & Sauvegarde</h3>
                <p class="text-gray-600">
                    Exportez vos résultats en PDF et sauvegardez vos évaluations pour un suivi continu.
                </p>
            </div>
        </div>
    </div>

    <!-- Design Factors Overview -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-2xl font-bold text-gray-900">Design Factors COBIT 2019</h2>
            <p class="text-gray-600 mt-2">
                Les facteurs de conception qui influencent votre système de gouvernance
            </p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($designFactors as $key => $df)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                            {{ str_replace('DF', '', $key) }}
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $df['title'] }}</h3>
                    </div>
                    <div class="space-y-2">
                        @foreach($df['labels'] as $label)
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            {{ $label }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Domains Overview -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-2xl font-bold text-gray-900">Domaines COBIT 2019</h2>
            <p class="text-gray-600 mt-2">
                Les cinq domaines de gouvernance et de gestion
            </p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @foreach($domains as $domain => $objectives)
                <div class="text-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold text-lg">{{ $domain }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $domain }}</h3>
                    <p class="text-sm text-gray-600">{{ count($objectives) }} objectifs</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-2xl font-bold text-gray-900">Actions Rapides</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('cobit.evaluation') }}" class="btn btn-primary p-4 text-center">
                    <i class="fas fa-play text-2xl mb-2"></i>
                    <div class="font-semibold">Nouvelle Évaluation</div>
                    <div class="text-sm opacity-75">Commencer une nouvelle évaluation</div>
                </a>
                
                <button onclick="loadSavedEvaluation()" class="btn btn-secondary p-4 text-center">
                    <i class="fas fa-folder-open text-2xl mb-2"></i>
                    <div class="font-semibold">Charger Évaluation</div>
                    <div class="text-sm opacity-75">Reprendre une évaluation sauvegardée</div>
                </button>
                
                <a href="{{ route('cobit.results') }}" class="btn btn-success p-4 text-center">
                    <i class="fas fa-chart-bar text-2xl mb-2"></i>
                    <div class="font-semibold">Voir Résultats</div>
                    <div class="text-sm opacity-75">Consulter les derniers résultats</div>
                </a>
                
                <button onclick="showHelp()" class="btn btn-secondary p-4 text-center">
                    <i class="fas fa-question-circle text-2xl mb-2"></i>
                    <div class="font-semibold">Aide</div>
                    <div class="text-sm opacity-75">Guide d'utilisation</div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Demo Modal -->
<div id="demoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Démo COBIT 2019 Design Toolkit</h3>
                <button onclick="closeDemoModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <p class="text-gray-600">
                    Cette application vous permet d'évaluer votre système de gouvernance IT selon le framework COBIT 2019.
                </p>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2">Processus d'évaluation :</h4>
                    <ol class="list-decimal list-inside space-y-1 text-blue-800">
                        <li>Évaluez chaque Design Factor (DF1 à DF5)</li>
                        <li>Visualisez les résultats en temps réel</li>
                        <li>Analysez les gaps et priorités</li>
                        <li>Exportez votre rapport final</li>
                    </ol>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeDemoModal()" class="btn btn-secondary">Fermer</button>
                    <a href="{{ route('cobit.evaluation') }}" class="btn btn-primary">Commencer</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showDemo() {
        document.getElementById('demoModal').classList.remove('hidden');
    }
    
    function closeDemoModal() {
        document.getElementById('demoModal').classList.add('hidden');
    }
    
    function loadSavedEvaluation() {
        // TODO: Implémenter le chargement d'évaluation
        showNotification('Fonctionnalité en cours de développement', 'info');
    }
    
    function showHelp() {
        // TODO: Implémenter l'aide
        showNotification('Guide d\'aide en cours de développement', 'info');
    }
    
    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('demoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDemoModal();
        }
    });
</script>
@endpush
