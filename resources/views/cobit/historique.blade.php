@extends('layouts.app')

@section('title', 'Historique des Évaluations COBIT 2019')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-history text-blue-900 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Historique des Évaluations</h1>
                        <p class="text-blue-200">Consultez vos évaluations COBIT 2019 précédentes</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if(isset($user))
                        <div class="text-sm text-blue-200">
                            <i class="fas fa-user mr-1"></i>
                            {{ $user['name'] ?? 'Utilisateur' }} ({{ $user['role'] ?? 'N/A' }})
                        </div>
                    @endif
                    <a href="{{ route('cobit.home') }}" class="bg-white text-blue-900 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                        <i class="fas fa-home mr-2"></i>Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <div class="container mx-auto px-6 py-8">
        <!-- Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Mes Évaluations</h2>
                <p class="text-gray-600">{{ count($evaluations) }} évaluation(s) trouvée(s)</p>
            </div>
            <a href="{{ route('cobit.home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Nouvelle Évaluation
            </a>
        </div>

        <!-- Liste des évaluations -->
        <div class="grid gap-6">
            @forelse($evaluations as $evaluation)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-building text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $evaluation['company_name'] }}</h3>
                                <p class="text-gray-600">{{ $evaluation['company_size'] }}</p>
                                @if(isset($evaluation['user_name']))
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-user mr-1"></i>{{ $evaluation['user_name'] }}
                                    @if(isset($evaluation['user_role']))
                                        ({{ $evaluation['user_role'] }})
                                    @endif
                                </p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $evaluation['status'] === 'Terminée' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas {{ $evaluation['status'] === 'Terminée' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                {{ $evaluation['status'] }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <!-- Progression -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600">Progression</span>
                                <span class="text-sm font-bold text-gray-900">{{ $evaluation['completed_dfs'] }}/{{ $evaluation['total_dfs'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" 
                                     style="width: {{ ($evaluation['completed_dfs'] / $evaluation['total_dfs']) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Score Global -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600">Score Global</span>
                                <span class="text-lg font-bold text-blue-600">{{ $evaluation['score_global'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all" 
                                     style="width: {{ $evaluation['score_global'] }}%"></div>
                            </div>
                        </div>

                        <!-- Date de création -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <span class="text-sm font-medium text-gray-600 block mb-1">Date de création</span>
                            <span class="text-sm font-bold text-gray-900">{{ $evaluation['created_at'] }}</span>
                        </div>

                        <!-- Actions -->
                        <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-center">
                            <div class="flex space-x-2">
                                @if($evaluation['status'] === 'En cours')
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-play mr-1"></i>Continuer
                                    </button>
                                @else
                                    @if(is_numeric($evaluation['id']))
                                        <a href="{{ route('cobit.historique.canvas', $evaluation['id']) }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors">
                                            <i class="fas fa-eye mr-1"></i>Canvas
                                        </a>
                                    @else
                                        <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors" onclick="alert('Canvas de démonstration')">
                                            <i class="fas fa-eye mr-1"></i>Canvas
                                        </button>
                                    @endif
                                @endif
                                <button class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Design Factors complétés -->
                    @if($evaluation['status'] === 'Terminée')
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-600 mb-3">Design Factors Évalués</h4>
                        <div class="flex flex-wrap gap-2">
                            @for($i = 1; $i <= 10; $i++)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>DF{{ $i }}
                                </span>
                            @endfor
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-history text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune évaluation trouvée</h3>
                <p class="text-gray-600 mb-6">Vous n'avez pas encore d'évaluations COBIT 2019.</p>
                <a href="{{ route('cobit.home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Commencer une évaluation
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Chatbot -->
    @include('components.chatbot')
</div>

<style>
.kmpg-blue { color: #00338D; }
.kmpg-bg { background-color: #00338D; }

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
</style>
@endsection
