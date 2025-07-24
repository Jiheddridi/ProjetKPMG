<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBIT 2019 - Évaluations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .kpmg-blue { color: #00338D; }
        .kpmg-bg { background-color: #00338D; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .pulse-animation { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="kpmg-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white p-2 rounded">
                        <svg width="40" height="20" viewBox="0 0 200 100" xmlns="http://www.w3.org/2000/svg">
                            <text x="10" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#00338D">KPMG</text>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">COBIT 2019 Framework</h1>
                        <p class="text-blue-200 text-sm">Plateforme d'Évaluation des Design Factors</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if(isset($user) && $user)
                        <span class="text-sm">Bonjour, {{ $user['name'] ?? 'Utilisateur' }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-blue-200 hover:text-white transition-colors">
                                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('cobit.historique') }}" class="text-sm text-blue-200 hover:text-white transition-colors mr-4">
                        <i class="fas fa-history mr-1"></i>Historique
                    </a>
                    <span class="text-sm">Powered by KPMG Advisory</span>
                    <div class="w-2 h-2 bg-green-400 rounded-full pulse-animation"></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <div class="animate-float">
                <i class="fas fa-cogs text-6xl mb-6 opacity-80"></i>
            </div>
            <h1 class="text-5xl font-bold mb-6">
                Évaluation des Design Factors
                <span class="block text-3xl text-blue-200 mt-2">COBIT 2019 Framework</span>
            </h1>
            <p class="text-xl mb-8 max-w-3xl mx-auto leading-relaxed">
                Optimisez votre gouvernance IT avec notre plateforme d'évaluation avancée. 
                Analysez les 10 Design Factors critiques et obtenez des recommandations personnalisées 
                basées sur les meilleures pratiques COBIT 2019.
            </p>
            <div class="flex justify-center space-x-4">
                <button onclick="startEvaluation()" class="bg-white kpmg-blue px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition-all transform hover:scale-105">
                    <i class="fas fa-play mr-2"></i>Commencer l'Évaluation
                </button>
                <button onclick="viewDemo()" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-blue-600 transition-all">
                    <i class="fas fa-eye mr-2"></i>Voir la Démo
                </button>
                <!-- Remote Agent Button -->
                <button onclick="openRemoteAgentModal()" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Remote agent
                </button>
            </div>
        </div>
    </section>

    <!-- Évaluations Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-4xl font-bold kpmg-blue mb-4">Mes Évaluations COBIT</h2>
                    <p class="text-xl text-gray-600">
                        Gérez vos évaluations des Design Factors et consultez vos canvas générés.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button onclick="startEvaluation()" class="kpmg-bg text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition-all transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Évaluation
                    </button>
                    @if($evaluations->count() >= 2)
                    <button onclick="goToComparison()" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-purple-700 hover:to-blue-700 transition-all transform hover:scale-105">
                        <i class="fas fa-balance-scale mr-2"></i>Comparer Évaluations
                    </button>
                    @endif
                </div>
            </div>

            @if($evaluations->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($evaluations as $evaluation)
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover border-2 border-gray-200 hover:border-blue-300 transition-all"
                         data-evaluation-id="{{ $evaluation->id }}"
                         data-evaluation-name="{{ $evaluation->nom_entreprise }}"
                         data-evaluation-size="{{ $evaluation->taille_entreprise }}"
                         data-evaluation-score="{{ $evaluation->score_global ?? 0 }}"
                         data-evaluation-maturity="{{ round($evaluation->score_global ?? 0) }}"
                         data-evaluation-date="{{ $evaluation->updated_at->format('d/m/Y') }}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $evaluation->nom_entreprise }}</h3>
                                <p class="text-sm text-gray-600 mb-1">{{ $evaluation->taille_entreprise }}</p>
                                @if($evaluation->contraintes)
                                <p class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $evaluation->contraintes }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                @if($evaluation->completed)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Terminée
                                    </span>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-green-600">{{ number_format($evaluation->score_global, 1) }}/5</div>
                                        <div class="text-xs text-gray-500">Score global</div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>En cours
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Progression avec cercles -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Progression des Design Factors</span>
                                <span>{{ $evaluation->getCompletedDFsCount() }}/10</span>
                            </div>
                            <div class="flex space-x-1 mb-2">
                                @for($i = 1; $i <= 10; $i++)
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                        @if($evaluation->isDFCompleted($i))
                                            bg-green-500 text-white
                                        @elseif($i == $evaluation->current_df && !$evaluation->completed)
                                            bg-blue-500 text-white
                                        @else
                                            bg-gray-200 text-gray-600
                                        @endif">
                                        {{ $i }}
                                    </div>
                                @endfor
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" 
                                     style="width: {{ $evaluation->getProgressPercentage() }}%"></div>
                            </div>
                        </div>

                        <!-- Informations -->
                        <div class="text-xs text-gray-500 mb-4 space-y-1">
                            <p><i class="fas fa-user mr-2 w-4"></i>{{ $evaluation->user_name ?? 'Utilisateur' }}</p>
                            <p><i class="fas fa-calendar mr-2 w-4"></i>{{ $evaluation->created_at->format('d/m/Y H:i') }}</p>
                            <p><i class="fas fa-clock mr-2 w-4"></i>Dernière mise à jour: {{ $evaluation->updated_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            @if($evaluation->completed)
                                <a href="{{ route('cobit.evaluation.canvas', $evaluation->id) }}"
                                   class="flex-1 bg-green-600 text-white px-4 py-2 rounded text-sm text-center hover:bg-green-700 transition-colors">
                                    <i class="fas fa-chart-area mr-1"></i>Voir Canvas
                                </a>
                                <a href="{{ route('cobit.evaluation.df', ['id' => $evaluation->id, 'df' => 1]) }}"
                                   class="flex-1 bg-blue-500 text-white px-4 py-2 rounded text-sm text-center hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-info-circle mr-1"></i>Consulter Détails
                                </a>
                            @else
                                <a href="{{ route('cobit.evaluation.df', ['id' => $evaluation->id, 'df' => $evaluation->current_df]) }}"
                                   class="flex-1 bg-blue-600 text-white px-4 py-2 rounded text-sm text-center hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-play mr-1"></i>Continuer DF{{ $evaluation->current_df }}
                                </a>
                            @endif
                            <button onclick="deleteEvaluation({{ $evaluation->id }})"
                                    class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="text-gray-400 mb-6">
                        <i class="fas fa-clipboard-list text-8xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-600 mb-4">Aucune évaluation trouvée</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">
                        Commencez votre première évaluation COBIT 2019 pour analyser les Design Factors 
                        et générer votre canvas personnalisé.
                    </p>
                    <button onclick="startEvaluation()" class="kpmg-bg text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition-all transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Commencer ma Première Évaluation
                    </button>
                </div>
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold kpmg-blue mb-4">Fonctionnalités Avancées</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <i class="fas fa-brain text-4xl text-purple-600 mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">IA Intégrée</h3>
                    <p class="text-gray-600">Recommandations intelligentes basées sur vos évaluations</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <i class="fas fa-chart-line text-4xl text-green-600 mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">Graphiques Temps Réel</h3>
                    <p class="text-gray-600">Visualisations interactives qui s'adaptent à vos données</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <i class="fas fa-target text-4xl text-red-600 mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">Objectifs COBIT</h3>
                    <p class="text-gray-600">Mapping automatique vers les 40 objectifs COBIT 2019</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="kpmg-bg text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <p class="text-blue-200">&copy; 2025 KPMG Advisory. Tous droits réservés.</p>
        </div>
    </footer>



    <!-- Modal pour nouvelle évaluation -->
    <div id="evaluationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold kpmg-blue mb-2">Nouvelle Évaluation COBIT</h3>
                <p class="text-gray-600">Veuillez renseigner les informations de votre entreprise</p>
            </div>
            
            <form id="evaluationForm" onsubmit="submitEvaluation(event)">
                <div class="mb-4">
                    <label for="nom_entreprise" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de l'entreprise *
                    </label>
                    <input type="text" id="nom_entreprise" name="nom_entreprise" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: KPMG Advisory">
                </div>
                
                <div class="mb-4">
                    <label for="taille_entreprise" class="block text-sm font-medium text-gray-700 mb-2">
                        Taille de l'entreprise *
                    </label>
                    <select id="taille_entreprise" name="taille_entreprise" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionnez la taille</option>
                        <option value="PME">PME (< 250 employés)</option>
                        <option value="Moyenne entreprise">Moyenne entreprise (250-1000 employés)</option>
                        <option value="Grande entreprise">Grande entreprise (> 1000 employés)</option>
                        <option value="Multinationale">Multinationale</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="contraintes" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraintes spécifiques (optionnel)
                    </label>
                    <input type="text" id="contraintes" name="contraintes"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: Conformité RGPD, Budget limité...">
                </div>

                <!-- Section d'upload de fichiers pour l'IA -->
                <div class="mb-6 border-t pt-4">
                    <div class="flex items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-purple-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Assistant IA COBIT (optionnel)</h4>
                            <p class="text-xs text-gray-500">Joignez vos documents pour une analyse automatique</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-center w-full">
                            <label for="document-upload" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-lg mb-1"></i>
                                    <p class="text-xs text-gray-500"><span class="font-semibold">Cliquez pour joindre</span> vos fichiers</p>
                                    <p class="text-xs text-gray-400">PDF, Excel (max. 10MB)</p>
                                </div>
                                <input id="document-upload" type="file" class="hidden" multiple accept=".pdf,.xlsx,.xls" onchange="handleFileUpload(this)">
                            </label>
                        </div>

                        <!-- Zone d'affichage des fichiers uploadés -->
                        <div id="uploaded-files" class="hidden">
                            <div class="text-xs text-gray-600 mb-2">Fichiers sélectionnés :</div>
                            <div id="file-list" class="space-y-1"></div>
                        </div>

                        <!-- Bouton d'analyse IA -->
                        <button type="button" id="ai-analyze-btn" class="hidden w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:from-purple-700 hover:to-blue-700 transition-all" onclick="analyzeDocuments()">
                            <i class="fas fa-magic mr-2"></i>Analyser avec l'IA et pré-remplir les paramètres
                        </button>

                        <!-- Zone de statut de l'analyse -->
                        <div id="ai-status" class="hidden text-sm"></div>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="button" onclick="closeEvaluationModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 kpmg-bg text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Commencer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Remote Agent Modal -->
    <div id="remoteAgentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold kpmg-blue mb-2">Remote Agent COBIT</h3>
                <p class="text-gray-600">Déposez un fichier .txt à analyser par l'IA COBIT 2019</p>
            </div>
            <form id="remoteAgentForm">
                <div class="mb-4">
                    <label for="remote-txt-upload" class="block text-sm font-medium text-gray-700 mb-2">Fichier .txt</label>
                    <div id="remoteDropZone" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-blue-400 rounded-lg cursor-pointer bg-blue-50 hover:bg-blue-100 transition-all">
                        <input id="remote-txt-upload" type="file" accept=".txt" class="hidden" onchange="handleRemoteFileUpload(this)">
                        <div id="remoteDropZoneText" class="flex flex-col items-center justify-center pt-2 pb-2">
                            <i class="fas fa-file-alt text-blue-400 text-lg mb-1"></i>
                            <p class="text-xs text-blue-600"><span class="font-semibold">Glissez-déposez</span> ou cliquez pour sélectionner un fichier .txt</p>
                        </div>
                        <div id="remoteFileName" class="text-xs text-gray-700 mt-2 hidden"></div>
                    </div>
                </div>
                <div id="remoteAgentStatus" class="text-sm mb-4 hidden"></div>
                <div class="flex space-x-4">
                    <button type="button" onclick="closeRemoteAgentModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Annuler</button>
                    <button type="submit" id="remoteAgentSubmit" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-md hover:from-blue-700 hover:to-purple-700 transition-all font-bold">Envoyer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Commencer l'évaluation
        function startEvaluation() {
            document.getElementById('evaluationModal').classList.remove('hidden');
        }

        // Voir la démo
        function viewDemo() {
            alert('Fonctionnalité de démo en cours de développement');
        }

        // Fermer le modal
        function closeEvaluationModal() {
            document.getElementById('evaluationModal').classList.add('hidden');
        }

        // Variables globales pour l'IA
        let uploadedFiles = [];
        let aiAnalysisResults = null;

        // Gérer l'upload de fichiers
        function handleFileUpload(input) {
            const files = Array.from(input.files);
            uploadedFiles = files;

            if (files.length > 0) {
                displayUploadedFiles(files);
                document.getElementById('ai-analyze-btn').classList.remove('hidden');
            } else {
                document.getElementById('uploaded-files').classList.add('hidden');
                document.getElementById('ai-analyze-btn').classList.add('hidden');
            }
        }

        // Afficher les fichiers uploadés
        function displayUploadedFiles(files) {
            const fileList = document.getElementById('file-list');
            const uploadedFilesDiv = document.getElementById('uploaded-files');

            fileList.innerHTML = '';
            files.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between bg-white p-2 rounded border text-xs';
                fileItem.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-file-${getFileIcon(file.name)} text-blue-600 mr-2"></i>
                        <span class="text-gray-700">${file.name}</span>
                        <span class="text-gray-400 ml-2">(${formatFileSize(file.size)})</span>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fileList.appendChild(fileItem);
            });

            uploadedFilesDiv.classList.remove('hidden');
        }

        // Supprimer un fichier
        function removeFile(index) {
            uploadedFiles.splice(index, 1);

            if (uploadedFiles.length > 0) {
                displayUploadedFiles(uploadedFiles);
            } else {
                document.getElementById('uploaded-files').classList.add('hidden');
                document.getElementById('ai-analyze-btn').classList.add('hidden');
            }

            // Mettre à jour l'input file
            const input = document.getElementById('document-upload');
            const dt = new DataTransfer();
            uploadedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

        // Obtenir l'icône du fichier
        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            switch(ext) {
                case 'pdf': return 'pdf';
                case 'xlsx':
                case 'xls': return 'excel';
                default: return 'alt';
            }
        }

        // Formater la taille du fichier
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Analyser les documents avec l'IA
        function analyzeDocuments() {
            if (uploadedFiles.length === 0) {
                alert('Veuillez sélectionner au moins un fichier à analyser.');
                return;
            }

            const statusDiv = document.getElementById('ai-status');
            const analyzeBtn = document.getElementById('ai-analyze-btn');

            // Afficher le statut de chargement
            statusDiv.className = 'text-sm text-blue-600 bg-blue-50 p-3 rounded-md border border-blue-200';
            statusDiv.innerHTML = `
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                    <span>🤖 Analyse IA en cours... Extraction et traitement des données COBIT</span>
                </div>
            `;
            statusDiv.classList.remove('hidden');
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Analyse en cours...';

            // Préparer les données pour l'envoi
            const formData = new FormData();
            uploadedFiles.forEach((file, index) => {
                formData.append(`documents[${index}]`, file);
            });
            formData.append('_token', '{{ csrf_token() }}');

            // Envoyer les fichiers pour analyse
            fetch('/cobit/ai-analyze', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    aiAnalysisResults = data.analysis;
                    displayAnalysisResults(data.analysis);
                } else {
                    throw new Error(data.message || 'Erreur lors de l\'analyse');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                statusDiv.className = 'text-sm text-red-600 bg-red-50 p-3 rounded-md border border-red-200';
                statusDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Erreur lors de l'analyse: ${error.message}</span>
                    </div>
                `;
            })
            .finally(() => {
                analyzeBtn.disabled = false;
                analyzeBtn.innerHTML = '<i class="fas fa-magic mr-2"></i>Analyser avec l\'IA et pré-remplir les paramètres';
            });
        }

        // Afficher les résultats de l'analyse
        function displayAnalysisResults(analysis) {
            const statusDiv = document.getElementById('ai-status');

            statusDiv.className = 'text-sm text-green-600 bg-green-50 p-3 rounded-md border border-green-200';
            statusDiv.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-medium">✅ Analyse terminée avec succès !</span>
                    </div>
                    <div class="text-xs text-green-700">
                        <div>📊 <strong>${analysis.documents_analyzed}</strong> document(s) analysé(s)</div>
                        <div>🎯 <strong>${analysis.df_suggestions}</strong> Design Factors pré-configurés</div>
                        <div>📈 Score de maturité estimé: <strong>${analysis.estimated_maturity}/5</strong></div>
                    </div>
                    <div class="text-xs text-green-600 mt-2">
                        💡 Les paramètres seront automatiquement appliqués lors de la création de l'évaluation
                    </div>
                </div>
            `;
        }

        // Soumettre le formulaire d'évaluation (modifié pour inclure l'IA)
        function submitEvaluation(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const data = {
                nom_entreprise: formData.get('nom_entreprise'),
                taille_entreprise: formData.get('taille_entreprise'),
                contraintes: formData.get('contraintes'),
                ai_analysis: aiAnalysisResults, // Inclure les résultats de l'IA
                _token: '{{ csrf_token() }}'
            };

            fetch('/cobit/evaluation/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Si l'IA a généré des données, mettre à jour les graphiques immédiatement
                    if (data.chart_data) {
                        console.log('🔄 MISE À JOUR GRAPHIQUES IMMÉDIATE APRÈS CRÉATION ÉVALUATION');
                        console.log('📊 Données graphiques reçues:', data.chart_data);

                        // Déclencher la mise à jour temps réel des graphiques
                        updateAllOpenCharts(data.chart_data);

                        // Notification spéciale pour la création d'évaluation
                        showEvaluationCreatedNotification(data.ai_applied);
                    }

                    closeEvaluationModal();
                    window.location.href = data.redirect;
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }

        // Aller à la page de comparaison - Alternative simple
        function goToComparison() {
            // Créer une interface de sélection directe dans la page
            createInlineComparisonInterface();
        }

        // Créer l'interface de comparaison directement dans la page
        function createInlineComparisonInterface() {
            // Récupérer les données des évaluations depuis le DOM
            const evaluationCards = document.querySelectorAll('[data-evaluation-id]');
            const evaluations = Array.from(evaluationCards).map(card => ({
                id: parseInt(card.dataset.evaluationId),
                name: card.dataset.evaluationName,
                size: card.dataset.evaluationSize,
                score: parseFloat(card.dataset.evaluationScore),
                maturity: parseInt(card.dataset.evaluationMaturity),
                date: card.dataset.evaluationDate
            })).filter(eval => eval.score > 0); // Seulement les évaluations complétées

            if (evaluations.length < 2) {
                alert('Au moins 2 évaluations complétées sont nécessaires pour la comparaison');
                return;
            }

            // Créer l'interface de sélection
            const comparisonHTML = `
                <div id="inlineComparison" class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-lg p-6 mt-8 mb-8">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-purple-700 mb-2">
                            <i class="fas fa-balance-scale mr-2"></i>
                            Comparer les Évaluations COBIT
                        </h3>
                        <p class="text-gray-600">Sélectionnez au moins 2 évaluations pour les comparer</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        ${evaluations.map(eval => `
                            <label class="flex items-center p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-purple-300 cursor-pointer transition-colors">
                                <input type="checkbox" class="inline-comparison-checkbox mr-3" value="${eval.id}"
                                       data-name="${eval.name}" data-score="${eval.score}" data-maturity="${eval.maturity}"
                                       onchange="updateInlineComparisonButton()">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800">${eval.name}</div>
                                    <div class="text-sm text-gray-600">${eval.size}</div>
                                    <div class="text-sm text-blue-600">
                                        Score: ${eval.score}/5 | Maturité: ${eval.maturity}
                                    </div>
                                    <div class="text-xs text-gray-500">${eval.date}</div>
                                </div>
                            </label>
                        `).join('')}
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span id="inlineSelectedCount">0</span> évaluation(s) sélectionnée(s)
                        </div>
                        <div class="space-x-3">
                            <button onclick="closeInlineComparison()"
                                    class="bg-gray-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </button>
                            <button id="inlineCompareBtn" onclick="performInlineComparison()"
                                    class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:from-purple-700 hover:to-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="fas fa-chart-line mr-2"></i>Comparer Maintenant
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Insérer l'interface après le header
            const headerSection = document.querySelector('.gradient-bg').parentNode;
            const comparisonDiv = document.createElement('div');
            comparisonDiv.innerHTML = comparisonHTML;
            headerSection.appendChild(comparisonDiv);

            // Scroll vers l'interface
            document.getElementById('inlineComparison').scrollIntoView({ behavior: 'smooth' });
        }

        // Fermer l'interface de comparaison inline
        function closeInlineComparison() {
            const comparisonDiv = document.getElementById('inlineComparison');
            if (comparisonDiv) {
                comparisonDiv.parentNode.removeChild(comparisonDiv);
            }
        }

        // Mettre à jour le bouton de comparaison inline
        function updateInlineComparisonButton() {
            const checked = document.querySelectorAll('.inline-comparison-checkbox:checked');
            const button = document.getElementById('inlineCompareBtn');
            const count = document.getElementById('inlineSelectedCount');

            if (count) count.textContent = checked.length;
            if (button) button.disabled = checked.length < 2;
        }

        // Effectuer la comparaison inline (analyse JavaScript pure)
        function performInlineComparison() {
            const checked = document.querySelectorAll('.inline-comparison-checkbox:checked');

            if (checked.length < 2) {
                alert('Veuillez sélectionner au moins 2 évaluations');
                return;
            }

            // Récupérer les données des évaluations sélectionnées
            const selectedEvaluations = Array.from(checked).map(cb => ({
                id: parseInt(cb.value),
                name: cb.dataset.name,
                score: parseFloat(cb.dataset.score),
                maturity: parseInt(cb.dataset.maturity)
            }));

            // Fermer l'interface de sélection
            closeInlineComparison();

            // Effectuer l'analyse comparative locale
            const analysis = performLocalComparison(selectedEvaluations);

            // Afficher les résultats
            displayLocalComparisonResults(analysis);
        }

        // Analyse comparative locale avancée (inspirée du modèle Ollama)
        function performLocalComparison(evaluations) {
            // Trier par score décroissant
            const sortedEvaluations = evaluations.sort((a, b) => b.score - a.score);

            // Calculer les statistiques de base
            const scores = evaluations.map(e => e.score);
            const avgScore = scores.reduce((a, b) => a + b, 0) / scores.length;
            const maxScore = Math.max(...scores);
            const minScore = Math.min(...scores);
            const scoreVariance = calculateVariance(scores, avgScore);

            // Analyser la distribution des niveaux de maturité
            const maturityDistribution = analyzeMaturityDistribution(evaluations);

            // Calculer les critères COBIT avancés
            const cobitCriteria = calculateCOBITCriteria(evaluations);

            // Analyser les gaps et opportunités
            const gapAnalysis = performGapAnalysis(evaluations, avgScore);

            // Évaluer l'impact stratégique
            const strategicImpact = assessStrategicImpact(evaluations);

            // Générer le classement avec critères multiples
            const ranking = generateAdvancedRanking(sortedEvaluations, avgScore, cobitCriteria);

            // Identifier la meilleure entreprise avec justification détaillée
            const bestCompany = sortedEvaluations[0];
            const recommendation = generateAdvancedRecommendation(bestCompany, avgScore, gapAnalysis, strategicImpact);

            return {
                summary: `Analyse comparative COBIT de ${evaluations.length} entreprises. Écart de performance: ${(maxScore - minScore).toFixed(1)} points. Variance: ${scoreVariance.toFixed(2)}.`,
                ranking: ranking,
                bestCompany: bestCompany,
                recommendation: recommendation,
                statistics: {
                    count: evaluations.length,
                    avgScore: avgScore,
                    maxScore: maxScore,
                    minScore: minScore,
                    variance: scoreVariance,
                    standardDeviation: Math.sqrt(scoreVariance)
                },
                maturityDistribution: maturityDistribution,
                cobitCriteria: cobitCriteria,
                gapAnalysis: gapAnalysis,
                strategicImpact: strategicImpact,
                chartData: prepareChartData(evaluations, cobitCriteria, maturityDistribution)
            };
        }

        // Calculer la variance des scores
        function calculateVariance(scores, mean) {
            const squaredDiffs = scores.map(score => Math.pow(score - mean, 2));
            return squaredDiffs.reduce((a, b) => a + b, 0) / scores.length;
        }

        // Analyser la distribution des niveaux de maturité
        function analyzeMaturityDistribution(evaluations) {
            const distribution = {};
            const maturityLabels = {
                1: 'Initial',
                2: 'Géré',
                3: 'Défini',
                4: 'Quantitatif',
                5: 'Optimisé'
            };

            evaluations.forEach(eval => {
                const level = eval.maturity;
                if (!distribution[level]) {
                    distribution[level] = { count: 0, companies: [], label: maturityLabels[level] };
                }
                distribution[level].count++;
                distribution[level].companies.push(eval.name);
            });

            return distribution;
        }

        // Calculer les critères COBIT avancés
        function calculateCOBITCriteria(evaluations) {
            return evaluations.map(eval => {
                const governanceIndex = calculateGovernanceIndex(eval.score, eval.maturity);
                const riskLevel = assessRiskLevel(eval.score);
                const complianceScore = calculateComplianceScore(eval.score, eval.maturity);
                const innovationPotential = assessInnovationPotential(eval.score, eval.maturity);

                return {
                    company: eval.name,
                    governanceIndex: governanceIndex,
                    riskLevel: riskLevel,
                    complianceScore: complianceScore,
                    innovationPotential: innovationPotential,
                    overallRating: (governanceIndex + complianceScore + innovationPotential) / 3
                };
            });
        }

        // Calculer l'indice de gouvernance
        function calculateGovernanceIndex(score, maturity) {
            // Formule basée sur COBIT 2019: Score pondéré par la maturité
            const baseIndex = (score / 5) * 100;
            const maturityBonus = (maturity - 1) * 5; // Bonus de 5% par niveau au-dessus d'Initial
            return Math.min(100, baseIndex + maturityBonus);
        }

        // Évaluer le niveau de risque
        function assessRiskLevel(score) {
            if (score >= 4.5) return { level: 'Très Faible', value: 10, color: '#10B981' };
            if (score >= 4.0) return { level: 'Faible', value: 25, color: '#84CC16' };
            if (score >= 3.5) return { level: 'Modéré', value: 50, color: '#F59E0B' };
            if (score >= 3.0) return { level: 'Élevé', value: 75, color: '#F97316' };
            return { level: 'Critique', value: 90, color: '#EF4444' };
        }

        // Calculer le score de conformité
        function calculateComplianceScore(score, maturity) {
            // Score de conformité basé sur les standards COBIT
            const baseCompliance = score * 20; // Score sur 100
            const maturityFactor = maturity / 5; // Facteur de maturité
            return Math.round(baseCompliance * maturityFactor);
        }

        // Évaluer le potentiel d'innovation
        function assessInnovationPotential(score, maturity) {
            // Potentiel d'innovation basé sur la maturité et la performance
            if (maturity >= 4 && score >= 4.0) return 85 + (score - 4) * 30; // Haute innovation
            if (maturity >= 3 && score >= 3.5) return 60 + (score - 3.5) * 20; // Innovation modérée
            if (maturity >= 2 && score >= 3.0) return 40 + (score - 3.0) * 15; // Innovation basique
            return 20 + score * 5; // Innovation limitée
        }

        // Générer une justification basée sur le score
        function getScoreJustification(score, avgScore) {
            if (score >= 4.5) return 'Excellence en gouvernance IT - Leader du marché';
            if (score >= 4.0) return 'Très bonne maturité COBIT - Performance solide';
            if (score >= 3.5) return 'Bonne gouvernance - Axes d\'amélioration identifiés';
            if (score >= 3.0) return 'Maturité moyenne - Potentiel d\'optimisation important';
            if (score >= 2.5) return 'Gouvernance basique - Transformation nécessaire';
            return 'Gouvernance initiale - Restructuration complète requise';
        }

        // Analyser les gaps et opportunités
        function performGapAnalysis(evaluations, avgScore) {
            return evaluations.map(eval => {
                const gapToAverage = avgScore - eval.score;
                const gapToBest = Math.max(...evaluations.map(e => e.score)) - eval.score;
                const improvementPotential = calculateImprovementPotential(eval.score, eval.maturity);

                return {
                    company: eval.name,
                    gapToAverage: gapToAverage,
                    gapToBest: gapToBest,
                    improvementPotential: improvementPotential,
                    priority: gapToBest > 1.0 ? 'Haute' : gapToBest > 0.5 ? 'Moyenne' : 'Faible'
                };
            });
        }

        // Calculer le potentiel d'amélioration
        function calculateImprovementPotential(score, maturity) {
            const maxPossibleScore = 5.0;
            const currentGap = maxPossibleScore - score;
            const maturityFactor = maturity / 5; // Plus la maturité est élevée, plus l'amélioration est facile
            return Math.round(currentGap * maturityFactor * 100);
        }

        // Évaluer l'impact stratégique
        function assessStrategicImpact(evaluations) {
            return evaluations.map(eval => {
                const businessValue = calculateBusinessValue(eval.score, eval.maturity);
                const digitalReadiness = assessDigitalReadiness(eval.score, eval.maturity);
                const competitiveAdvantage = calculateCompetitiveAdvantage(eval.score, eval.maturity);

                return {
                    company: eval.name,
                    businessValue: businessValue,
                    digitalReadiness: digitalReadiness,
                    competitiveAdvantage: competitiveAdvantage,
                    strategicScore: (businessValue + digitalReadiness + competitiveAdvantage) / 3
                };
            });
        }

        // Calculer la valeur business
        function calculateBusinessValue(score, maturity) {
            // Valeur business basée sur la gouvernance IT
            const baseValue = score * 20; // Score sur 100
            const maturityMultiplier = 1 + (maturity - 1) * 0.2; // Multiplicateur de maturité
            return Math.round(baseValue * maturityMultiplier);
        }

        // Évaluer la préparation digitale
        function assessDigitalReadiness(score, maturity) {
            if (maturity >= 4 && score >= 4.0) return 90; // Très prêt
            if (maturity >= 3 && score >= 3.5) return 70; // Prêt
            if (maturity >= 2 && score >= 3.0) return 50; // Partiellement prêt
            return 30; // Non prêt
        }

        // Calculer l'avantage concurrentiel
        function calculateCompetitiveAdvantage(score, maturity) {
            const governanceAdvantage = score >= 4.0 ? 30 : score >= 3.5 ? 20 : 10;
            const maturityAdvantage = maturity >= 4 ? 25 : maturity >= 3 ? 15 : 5;
            const innovationAdvantage = (score + maturity) >= 7 ? 20 : 10;
            return governanceAdvantage + maturityAdvantage + innovationAdvantage;
        }

        // Générer un classement avancé
        function generateAdvancedRanking(sortedEvaluations, avgScore, cobitCriteria) {
            return sortedEvaluations.map((eval, index) => {
                const criteria = cobitCriteria.find(c => c.company === eval.name);
                return {
                    position: index + 1,
                    company: eval.name,
                    score: eval.score,
                    maturity: eval.maturity,
                    governanceIndex: criteria.governanceIndex,
                    riskLevel: criteria.riskLevel,
                    complianceScore: criteria.complianceScore,
                    innovationPotential: criteria.innovationPotential,
                    overallRating: criteria.overallRating,
                    justification: getAdvancedJustification(eval.score, eval.maturity, criteria)
                };
            });
        }

        // Justification avancée basée sur les critères COBIT
        function getAdvancedJustification(score, maturity, criteria) {
            let justification = '';

            if (criteria.governanceIndex >= 90) {
                justification = 'Excellence en gouvernance IT - Leader du secteur';
            } else if (criteria.governanceIndex >= 75) {
                justification = 'Gouvernance solide - Performance supérieure';
            } else if (criteria.governanceIndex >= 60) {
                justification = 'Gouvernance correcte - Améliorations possibles';
            } else {
                justification = 'Gouvernance faible - Transformation nécessaire';
            }

            // Ajouter des détails sur les risques
            justification += ` | Risque: ${criteria.riskLevel.level}`;

            // Ajouter le potentiel d'innovation
            if (criteria.innovationPotential >= 80) {
                justification += ' | Innovation: Élevée';
            } else if (criteria.innovationPotential >= 60) {
                justification += ' | Innovation: Modérée';
            } else {
                justification += ' | Innovation: Limitée';
            }

            return justification;
        }

        // Générer une recommandation avancée
        function generateAdvancedRecommendation(bestCompany, avgScore, gapAnalysis, strategicImpact) {
            const bestGap = gapAnalysis.find(g => g.company === bestCompany.name);
            const bestImpact = strategicImpact.find(s => s.company === bestCompany.name);

            const recommendations = [];
            const strategicActions = [];

            // Recommandations basées sur le score
            if (bestCompany.score >= 4.5) {
                recommendations.push('Maintenir l\'excellence opérationnelle');
                recommendations.push('Devenir un centre d\'expertise COBIT');
                recommendations.push('Partager les bonnes pratiques avec l\'écosystème');
                strategicActions.push('Développer une stratégie de leadership technologique');
            } else if (bestCompany.score >= 4.0) {
                recommendations.push('Optimiser les processus existants');
                recommendations.push('Viser l\'excellence dans les domaines faibles');
                recommendations.push('Mettre en place une amélioration continue');
                strategicActions.push('Investir dans l\'innovation et la transformation digitale');
            } else if (bestCompany.score >= 3.5) {
                recommendations.push('Renforcer les contrôles de gouvernance');
                recommendations.push('Améliorer la mesure de performance');
                recommendations.push('Développer les compétences IT');
                strategicActions.push('Moderniser l\'infrastructure et les processus');
            } else {
                recommendations.push('Restructurer la gouvernance IT');
                recommendations.push('Implémenter les contrôles de base');
                recommendations.push('Former les équipes aux bonnes pratiques');
                strategicActions.push('Lancer un programme de transformation IT');
            }

            // Recommandations basées sur l'impact stratégique
            if (bestImpact.digitalReadiness >= 80) {
                strategicActions.push('Exploiter la maturité digitale pour l\'innovation');
            } else if (bestImpact.digitalReadiness >= 60) {
                strategicActions.push('Accélérer la transformation digitale');
            } else {
                strategicActions.push('Développer les capacités digitales de base');
            }

            return {
                bestCompany: bestCompany.name,
                whyBest: `Score global optimal (${bestCompany.score}/5), maturité ${bestCompany.maturity}, et potentiel stratégique élevé`,
                nextSteps: recommendations,
                strategicActions: strategicActions,
                riskMitigation: 'Surveillance continue des KPIs COBIT, audits réguliers, et mise à jour des évaluations trimestrielles',
                businessValue: bestImpact.businessValue,
                competitiveAdvantage: bestImpact.competitiveAdvantage
            };
        }

        // Préparer les données pour les graphiques
        function prepareChartData(evaluations, cobitCriteria, maturityDistribution) {
            return {
                scoreComparison: evaluations.map(e => ({ name: e.name, score: e.score })),
                maturityDistribution: Object.keys(maturityDistribution).map(level => ({
                    level: parseInt(level),
                    label: maturityDistribution[level].label,
                    count: maturityDistribution[level].count
                })),
                governanceIndex: cobitCriteria.map(c => ({ name: c.company, index: c.governanceIndex })),
                riskLevels: cobitCriteria.map(c => ({ name: c.company, risk: c.riskLevel.value, color: c.riskLevel.color })),
                innovationPotential: cobitCriteria.map(c => ({ name: c.company, potential: c.innovationPotential })),
                complianceScores: cobitCriteria.map(c => ({ name: c.company, compliance: c.complianceScore }))
            };
        }

        // Afficher les résultats de comparaison locale avec graphiques avancés
        function displayLocalComparisonResults(analysis) {
            const resultsHTML = `
                <div id="localComparisonResults" class="bg-white rounded-xl shadow-lg p-6 mt-8 mb-8">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-green-700 mb-2">
                            <i class="fas fa-chart-line mr-2"></i>
                            Analyse Comparative COBIT Avancée
                        </h3>
                        <p class="text-gray-600">${analysis.summary}</p>
                    </div>

                    <!-- Statistiques globales étendues -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">${analysis.statistics.count}</div>
                            <div class="text-sm text-gray-600">Entreprises</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">${analysis.statistics.avgScore.toFixed(1)}</div>
                            <div class="text-sm text-gray-600">Score Moyen</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">${analysis.statistics.maxScore.toFixed(1)}</div>
                            <div class="text-sm text-gray-600">Meilleur Score</div>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">${analysis.statistics.minScore.toFixed(1)}</div>
                            <div class="text-sm text-gray-600">Score Minimum</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">${analysis.statistics.variance.toFixed(2)}</div>
                            <div class="text-sm text-gray-600">Variance</div>
                        </div>
                        <div class="text-center p-4 bg-indigo-50 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">${analysis.statistics.standardDeviation.toFixed(2)}</div>
                            <div class="text-sm text-gray-600">Écart-Type</div>
                        </div>
                    </div>

                    <!-- Graphiques de comparaison -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Graphique des scores -->
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6">
                            <h4 class="text-lg font-bold text-blue-700 mb-4">📊 Comparaison des Scores COBIT</h4>
                            <canvas id="scoresChart" width="400" height="300"></canvas>
                        </div>

                        <!-- Distribution des niveaux de maturité -->
                        <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-bold text-green-700 mb-4">🎯 Distribution Maturité</h4>
                            <canvas id="maturityChart" width="400" height="300"></canvas>
                        </div>

                        <!-- Indice de gouvernance -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6">
                            <h4 class="text-lg font-bold text-purple-700 mb-4">🏛️ Indice de Gouvernance</h4>
                            <canvas id="governanceChart" width="400" height="300"></canvas>
                        </div>

                        <!-- Niveaux de risque -->
                        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6">
                            <h4 class="text-lg font-bold text-orange-700 mb-4">⚠️ Évaluation des Risques</h4>
                            <canvas id="riskChart" width="400" height="300"></canvas>
                        </div>
                    </div>

                    <!-- Critères COBIT détaillés -->
                    <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-6 mb-8">
                        <h4 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-cogs mr-2"></i>
                            Critères COBIT Avancés
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entreprise</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gouvernance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conformité</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Innovation</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Risque</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note Globale</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${analysis.ranking.map(rank => `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">${rank.company}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${rank.governanceIndex}%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-600">${rank.governanceIndex.toFixed(0)}%</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${rank.complianceScore >= 80 ? 'bg-green-100 text-green-800' : rank.complianceScore >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                                                    ${rank.complianceScore}%
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="bg-purple-600 h-2 rounded-full" style="width: ${rank.innovationPotential}%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-600">${rank.innovationPotential.toFixed(0)}%</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: ${rank.riskLevel.color}20; color: ${rank.riskLevel.color}">
                                                    ${rank.riskLevel.level}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-lg font-bold text-blue-600">${rank.overallRating.toFixed(1)}/100</div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Classement -->
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6 mb-8">
                        <h4 class="text-xl font-bold text-blue-700 mb-4 flex items-center">
                            <i class="fas fa-trophy mr-2"></i>
                            Classement des Entreprises
                        </h4>
                        <div class="space-y-3">
                            ${analysis.ranking.map(rank => `
                                <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm">
                                    <div class="flex items-center">
                                        <span class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full flex items-center justify-center text-lg font-bold mr-4">
                                            ${rank.position}
                                        </span>
                                        <div>
                                            <div class="font-semibold text-gray-800 text-lg">${rank.company}</div>
                                            <div class="text-sm text-gray-600">${rank.justification}</div>
                                            <div class="text-xs text-blue-600">Niveau de maturité: ${rank.maturity}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-blue-600">${rank.score}/5</div>
                                        <div class="text-sm text-gray-500">Score COBIT</div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Analyse des gaps et opportunités -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 mb-8">
                        <h4 class="text-xl font-bold text-yellow-700 mb-4 flex items-center">
                            <i class="fas fa-chart-gap mr-2"></i>
                            Analyse des Gaps et Opportunités
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            ${analysis.gapAnalysis.map(gap => `
                                <div class="bg-white rounded-lg p-4 border-l-4 ${gap.priority === 'Haute' ? 'border-red-500' : gap.priority === 'Moyenne' ? 'border-yellow-500' : 'border-green-500'}">
                                    <h5 class="font-bold text-gray-800 mb-2">${gap.company}</h5>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Gap vs Moyenne:</span>
                                            <span class="font-medium ${gap.gapToAverage > 0 ? 'text-red-600' : 'text-green-600'}">
                                                ${gap.gapToAverage > 0 ? '+' : ''}${gap.gapToAverage.toFixed(1)}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Gap vs Meilleur:</span>
                                            <span class="font-medium text-blue-600">${gap.gapToBest.toFixed(1)}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Potentiel:</span>
                                            <span class="font-medium text-purple-600">${gap.improvementPotential}%</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Priorité:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${gap.priority === 'Haute' ? 'bg-red-100 text-red-800' : gap.priority === 'Moyenne' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                                                ${gap.priority}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Impact stratégique -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 mb-8">
                        <h4 class="text-xl font-bold text-indigo-700 mb-4 flex items-center">
                            <i class="fas fa-chess-king mr-2"></i>
                            Impact Stratégique
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            ${analysis.strategicImpact.map(impact => `
                                <div class="bg-white rounded-lg p-4">
                                    <h5 class="font-bold text-gray-800 mb-3">${impact.company}</h5>
                                    <div class="space-y-3">
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm text-gray-600">Valeur Business</span>
                                                <span class="text-sm font-medium">${impact.businessValue}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: ${impact.businessValue}%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm text-gray-600">Préparation Digitale</span>
                                                <span class="text-sm font-medium">${impact.digitalReadiness}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${impact.digitalReadiness}%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm text-gray-600">Avantage Concurrentiel</span>
                                                <span class="text-sm font-medium">${impact.competitiveAdvantage}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: ${impact.competitiveAdvantage}%"></div>
                                            </div>
                                        </div>
                                        <div class="pt-2 border-t">
                                            <div class="text-center">
                                                <span class="text-lg font-bold text-indigo-600">${impact.strategicScore.toFixed(1)}</span>
                                                <span class="text-sm text-gray-600 ml-1">Score Stratégique</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Recommandation finale enrichie -->
                    <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-6 mb-6">
                        <h4 class="text-xl font-bold text-green-700 mb-4 flex items-center">
                            <i class="fas fa-star mr-2"></i>
                            Recommandation Stratégique Finale
                        </h4>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-lg p-6 border-l-4 border-green-500">
                                <h5 class="text-lg font-bold text-green-700 mb-2">🏆 Entreprise Leader</h5>
                                <p class="text-xl font-bold text-gray-900 mb-2">${analysis.recommendation.bestCompany}</p>
                                <p class="text-gray-700 mb-4">${analysis.recommendation.whyBest}</p>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="text-center p-2 bg-green-50 rounded">
                                        <div class="font-bold text-green-600">${analysis.recommendation.businessValue}%</div>
                                        <div class="text-gray-600">Valeur Business</div>
                                    </div>
                                    <div class="text-center p-2 bg-blue-50 rounded">
                                        <div class="font-bold text-blue-600">${analysis.recommendation.competitiveAdvantage}%</div>
                                        <div class="text-gray-600">Avantage Concurrentiel</div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-white rounded-lg p-4">
                                    <h5 class="font-bold text-green-700 mb-3">📋 Actions Opérationnelles</h5>
                                    <ul class="space-y-2">
                                        ${analysis.recommendation.nextSteps.map(step => `
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                                <span class="text-gray-700 text-sm">${step}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>

                                <div class="bg-white rounded-lg p-4">
                                    <h5 class="font-bold text-blue-700 mb-3">🚀 Actions Stratégiques</h5>
                                    <ul class="space-y-2">
                                        ${analysis.recommendation.strategicActions.map(action => `
                                            <li class="flex items-start">
                                                <i class="fas fa-rocket text-blue-500 mr-2 mt-1"></i>
                                                <span class="text-gray-700 text-sm">${action}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-4 mt-4">
                            <h5 class="font-bold text-orange-700 mb-2">⚠️ Gestion des Risques</h5>
                            <p class="text-gray-700 text-sm">${analysis.recommendation.riskMitigation}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-center">
                        <button onclick="closeLocalComparisonResults()"
                                class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors mr-4">
                            <i class="fas fa-times mr-2"></i>Fermer les Résultats
                        </button>
                        <button onclick="goToComparison()"
                                class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:from-purple-700 hover:to-blue-700 transition-colors">
                            <i class="fas fa-redo mr-2"></i>Nouvelle Comparaison
                        </button>
                    </div>
                </div>
            `;

            // Insérer les résultats après le header
            const headerSection = document.querySelector('.gradient-bg').parentNode;
            const resultsDiv = document.createElement('div');
            resultsDiv.innerHTML = resultsHTML;
            headerSection.appendChild(resultsDiv);

            // Scroll vers les résultats
            document.getElementById('localComparisonResults').scrollIntoView({ behavior: 'smooth' });

            // Créer les graphiques après un court délai pour s'assurer que les canvas sont dans le DOM
            setTimeout(() => {
                createAdvancedCharts(analysis.chartData);
            }, 100);
        }

        // Créer les graphiques avancés
        function createAdvancedCharts(chartData) {
            // Graphique des scores COBIT
            const scoresCtx = document.getElementById('scoresChart');
            if (scoresCtx) {
                new Chart(scoresCtx, {
                    type: 'bar',
                    data: {
                        labels: chartData.scoreComparison.map(d => d.name),
                        datasets: [{
                            label: 'Score COBIT',
                            data: chartData.scoreComparison.map(d => d.score),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Scores COBIT par Entreprise' }
                        },
                        scales: {
                            y: { beginAtZero: true, max: 5 }
                        }
                    }
                });
            }

            // Graphique de distribution de maturité
            const maturityCtx = document.getElementById('maturityChart');
            if (maturityCtx) {
                new Chart(maturityCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.maturityDistribution.map(d => `Niveau ${d.level} - ${d.label}`),
                        datasets: [{
                            data: chartData.maturityDistribution.map(d => d.count),
                            backgroundColor: [
                                '#EF4444', '#F97316', '#EAB308', '#22C55E', '#3B82F6'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            title: { display: true, text: 'Distribution des Niveaux de Maturité' }
                        }
                    }
                });
            }

            // Graphique de l'indice de gouvernance
            const governanceCtx = document.getElementById('governanceChart');
            if (governanceCtx) {
                new Chart(governanceCtx, {
                    type: 'radar',
                    data: {
                        labels: chartData.governanceIndex.map(d => d.name),
                        datasets: [{
                            label: 'Indice de Gouvernance',
                            data: chartData.governanceIndex.map(d => d.index),
                            backgroundColor: 'rgba(147, 51, 234, 0.2)',
                            borderColor: 'rgba(147, 51, 234, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(147, 51, 234, 1)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Indice de Gouvernance IT' }
                        },
                        scales: {
                            r: { beginAtZero: true, max: 100 }
                        }
                    }
                });
            }

            // Graphique des niveaux de risque
            const riskCtx = document.getElementById('riskChart');
            if (riskCtx) {
                new Chart(riskCtx, {
                    type: 'horizontalBar',
                    data: {
                        labels: chartData.riskLevels.map(d => d.name),
                        datasets: [{
                            label: 'Niveau de Risque',
                            data: chartData.riskLevels.map(d => d.risk),
                            backgroundColor: chartData.riskLevels.map(d => d.color),
                            borderColor: chartData.riskLevels.map(d => d.color),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Évaluation des Risques IT' }
                        },
                        scales: {
                            x: { beginAtZero: true, max: 100 }
                        }
                    }
                });
            }
        }

        // Fermer les résultats de comparaison locale
        function closeLocalComparisonResults() {
            const resultsDiv = document.getElementById('localComparisonResults');
            if (resultsDiv) {
                resultsDiv.parentNode.removeChild(resultsDiv);
            }
        }



        // Supprimer une évaluation
        function deleteEvaluation(evaluationId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?')) {
                fetch(`/cobit/evaluation/${evaluationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                });
            }
        }

        // Mettre à jour tous les graphiques ouverts (fonction pour temps réel)
        function updateAllOpenCharts(chartData) {
            console.log('📡 ENVOI SIGNAL MISE À JOUR GRAPHIQUES TEMPS RÉEL');
            console.log('📊 Données à envoyer:', chartData);

            // Utiliser localStorage pour communiquer entre les onglets
            localStorage.setItem('cobit_chart_update', JSON.stringify({
                data: chartData,
                timestamp: Date.now()
            }));

            // Déclencher un événement personnalisé pour l'onglet actuel
            window.dispatchEvent(new CustomEvent('cobitChartsUpdate', {
                detail: chartData
            }));

            console.log('✅ Signal de mise à jour envoyé à tous les graphiques');
        }

        // Notification spéciale pour la création d'évaluation avec IA
        function showEvaluationCreatedNotification(aiApplied) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-green-500 to-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 max-w-md';

            if (aiApplied) {
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-robot mr-3 text-xl"></i>
                        <div>
                            <div class="font-semibold">🚀 Évaluation créée avec IA !</div>
                            <div class="text-sm opacity-90">Graphiques mis à jour en temps réel</div>
                        </div>
                    </div>
                `;
            } else {
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <div>
                            <div class="font-semibold">✅ Évaluation créée !</div>
                            <div class="text-sm opacity-90">Prête pour la saisie</div>
                        </div>
                    </div>
                `;
            }

            document.body.appendChild(notification);

            // Supprimer la notification après 4 secondes
            setTimeout(() => {
                notification.remove();
            }, 4000);

            console.log('🎉 Notification création évaluation affichée');
        }

        // Remote Agent Modal Logic
        function openRemoteAgentModal() {
            document.getElementById('remoteAgentModal').classList.remove('hidden');
        }
        function closeRemoteAgentModal() {
            document.getElementById('remoteAgentModal').classList.add('hidden');
            resetRemoteAgentModal();
        }
        function resetRemoteAgentModal() {
            document.getElementById('remote-txt-upload').value = '';
            document.getElementById('remoteFileName').classList.add('hidden');
            document.getElementById('remoteFileName').innerText = '';
            document.getElementById('remoteAgentStatus').classList.add('hidden');
            document.getElementById('remoteAgentStatus').innerText = '';
            document.getElementById('remoteAgentSubmit').disabled = false;
        }
        // Drag & Drop for .txt
        const dropZone = document.getElementById('remoteDropZone');
        if (dropZone) {
            dropZone.addEventListener('click', () => document.getElementById('remote-txt-upload').click());
            dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('bg-blue-100'); });
            dropZone.addEventListener('dragleave', e => { e.preventDefault(); dropZone.classList.remove('bg-blue-100'); });
            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                dropZone.classList.remove('bg-blue-100');
                const files = e.dataTransfer.files;
                if (files.length && files[0].type === 'text/plain') {
                    document.getElementById('remote-txt-upload').files = files;
                    handleRemoteFileUpload(document.getElementById('remote-txt-upload'));
                }
            });
        }
        function handleRemoteFileUpload(input) {
            const file = input.files[0];
            if (file) {
                document.getElementById('remoteFileName').innerText = file.name;
                document.getElementById('remoteFileName').classList.remove('hidden');
            } else {
                document.getElementById('remoteFileName').classList.add('hidden');
                document.getElementById('remoteFileName').innerText = '';
            }
        }
        document.getElementById('remoteAgentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('remote-txt-upload');
            const statusDiv = document.getElementById('remoteAgentStatus');
            const submitBtn = document.getElementById('remoteAgentSubmit');
            if (!fileInput.files.length) {
                statusDiv.className = 'text-sm text-red-600 bg-red-50 p-3 rounded-md border border-red-200 mb-4';
                statusDiv.innerText = 'Veuillez sélectionner un fichier .txt.';
                statusDiv.classList.remove('hidden');
                return;
            }
            submitBtn.disabled = true;
            statusDiv.className = 'text-sm text-blue-600 bg-blue-50 p-3 rounded-md border border-blue-200 mb-4';
            statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi et analyse en cours...';
            statusDiv.classList.remove('hidden');
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            fetch('https://zeddnnekz77.app.n8n.cloud/webhook/txt-analysis', {
                method: 'POST',
                body: formData
            })
            .then(async r => {
                const text = await r.text();
                console.log('Réponse brute n8n:', text);
                try {
                    // Si la réponse contient 'workflow was started', afficher le message complet
                    if (text.toLowerCase().includes('workflow was started')) {
                        return {
                            status: 'success',
                            message: 'workflow was started and email will be sent in 10 seconds ,thank you for your patience.'
                        };
                    }
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Réponse non JSON: ' + text);
                }
            })
            .then(data => {
                statusDiv.classList.remove('hidden');
                if (
                    (data && data.status === 'success') ||
                    (typeof data.message === 'string' && data.message.toLowerCase().includes('workflow was started'))
                ) {
                    statusDiv.className = 'text-sm text-green-600 bg-green-50 p-3 rounded-md border border-green-200 mb-4';
                    statusDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Succès : workflow was started and Email will be sent in 10 seconds ,Thank you for your patience .';
                } else {
                    throw new Error(data.message || 'Erreur lors de la génération du rapport.');
                }
            })
            .catch(error => {
                statusDiv.className = 'text-sm text-red-600 bg-red-50 p-3 rounded-md border border-red-200 mb-4';
                statusDiv.innerHTML = `<i class=\"fas fa-exclamation-triangle mr-2\"></i>Erreur : ${error.message}`;
            })
            .finally(() => {
                submitBtn.disabled = false;
            });
        });

        window.addEventListener('storage', function(event) {
            if (event.key === 'cobit_df_update') {
                // Ici, rechargez les graphiques
                location.reload(); // ou mieux, rechargez juste les données des graphiques
            }
        });

        // Fonction à adapter selon votre code
        function reloadAllDFCharts() {
            // Exemples :
            // - Refaites un fetch AJAX pour récupérer les nouvelles données
            // - Redessinez les graphiques avec Chart.js
            // - Ou rechargez la page : location.reload();
        }
    </script>

    <!-- Intégration du Chatbot COBIT 2019 -->
    @include('components.chatbot')
</body>
</html>
