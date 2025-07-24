/**
 * COBIT Interactive Charts - Graphiques Interactifs en Temps Réel
 * Gestion des graphiques pour tous les Design Factors (DF1-DF10)
 */

// Objet global pour les graphiques interactifs
const CobitInteractiveCharts = {
    // Stockage des instances de graphiques
    charts: {},
    
    // Données actuelles
    currentData: {
        scores: [],
        baselines: [],
        domainAverages: {
            current: [],
            baseline: []
        },
        objectives: []
    },
    
    // Configuration des filtres
    filterSettings: {
        sortBy: 'score',
        sortOrder: 'desc',
        topCount: 15,
        domain: 'all'
    },
    
    // Couleurs par domaine COBIT
    domainColors: {
        'EDM': { bg: 'rgba(239, 68, 68, 0.8)', border: 'rgb(239, 68, 68)' },
        'APO': { bg: 'rgba(59, 130, 246, 0.8)', border: 'rgb(59, 130, 246)' },
        'BAI': { bg: 'rgba(16, 185, 129, 0.8)', border: 'rgb(16, 185, 129)' },
        'DSS': { bg: 'rgba(245, 158, 11, 0.8)', border: 'rgb(245, 158, 11)' },
        'MEA': { bg: 'rgba(139, 92, 246, 0.8)', border: 'rgb(139, 92, 246)' }
    },
    
    /**
     * Initialisation des graphiques interactifs
     */
    init: function() {
        console.log('🚀 Initialisation des graphiques interactifs COBIT');
        this.setupEventListeners();
        this.initializeCharts();
    },
    
    /**
     * Configuration des écouteurs d'événements
     */
    setupEventListeners: function() {
        // Écouter les changements d'inputs
        document.addEventListener('input', (e) => {
            if (e.target.matches('.df-input')) {
                this.handleInputChange(e.target);
            }
        });
        
        // Écouter les clics sur les boutons de filtre
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-filter-action]')) {
                this.handleFilterAction(e.target);
            }
            
            // Boutons de tri des objectifs
            if (e.target.matches('[data-sort-objectives]')) {
                const sortBy = e.target.dataset.sortObjectives;
                const sortOrder = e.target.dataset.sortOrder || 'desc';
                this.sortObjectives(sortBy, sortOrder);
            }
            
            // Boutons de filtre top N
            if (e.target.matches('[data-top-filter]')) {
                const topCount = parseInt(e.target.dataset.topFilter);
                this.filterTopObjectives(topCount);
            }
        });
        
        // Écouter les événements personnalisés pour les mises à jour
        window.addEventListener('cobitDataUpdate', (e) => {
            console.log('📊 Mise à jour des données reçue:', e.detail);
            this.updateChartsWithNewData(e.detail);
        });
        
        // Écouter les changements dans localStorage (pour les autres onglets)
        window.addEventListener('storage', (e) => {
            if (e.key === 'cobit_chart_update') {
                const updateData = JSON.parse(e.newValue);
                this.updateChartsWithNewData(updateData.data);
            }
        });
    },
    
    /**
     * Initialiser les graphiques au chargement
     */
    initializeCharts: function() {
        // Récupérer les données initiales
        const dfNumber = document.querySelector('[data-df-number]')?.dataset.dfNumber;
        
        if (dfNumber) {
            // Charger les données depuis l'API
            this.loadDFData(dfNumber);
        } else {
            // Créer des graphiques avec des données par défaut
            this.createDefaultCharts();
        }
    },
    
    /**
     * Charger les données du Design Factor depuis l'API
     */
    loadDFData: function(dfNumber) {
        console.log(`📡 Chargement des données pour DF${dfNumber}`);
        
        // Récupérer les données depuis l'API
        fetch(`/cobit/api/calculate/${dfNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✅ Données chargées avec succès:', data);
                    this.currentData.scores = data.scores || [];
                    this.currentData.baselines = data.baselines || [];
                    this.currentData.domainAverages = data.domainAverages || {
                        current: [0, 0, 0, 0, 0],
                        baseline: [2.5, 2.5, 2.5, 2.5, 2.5]
                    };
                    this.currentData.objectives = data.objectives || [];
                    
                    // Créer les graphiques avec les données réelles
                    this.createRadarChart();
                    this.createDomainBarChart();
                    this.createObjectivesTable();
                    
                    // Afficher un message de succès
                    this.showStatusMessage('✅ Graphiques générés avec les données actuelles', 'success');
                } else {
                    console.warn('⚠️ Erreur lors du chargement des données:', data.message);
                    this.createDefaultCharts();
                }
            })
            .catch(error => {
                console.error('❌ Erreur API:', error);
                this.createDefaultCharts();
            });
    },
    
    /**
     * Créer des graphiques avec des données par défaut
     */
    createDefaultCharts: function() {
        console.log('⚠️ Utilisation des données par défaut pour les graphiques');
        
        // Données par défaut
        this.currentData = {
            scores: new Array(40).fill(2.5),
            baselines: new Array(40).fill(2.5),
            domainAverages: {
                current: [2.5, 2.5, 2.5, 2.5, 2.5],
                baseline: [2.5, 2.5, 2.5, 2.5, 2.5]
            },
            objectives: this.getDefaultObjectives()
        };
        
        // Créer les graphiques
        this.createRadarChart();
        this.createDomainBarChart();
        this.createObjectivesTable();
        
        // Afficher un message d'avertissement
        this.showStatusMessage('⚠️ Graphiques générés avec des données par défaut', 'warning');
    },
    
    /**
     * Créer le graphique radar (Vue d'ensemble)
     */
    createRadarChart: function() {
        const canvas = document.getElementById('radar-chart');
        if (!canvas) return;
        
        // Détruire le graphique existant si nécessaire
        if (this.charts.radar) {
            this.charts.radar.destroy();
        }
        
        console.log('🔄 Création du Radar Chart avec données:', this.currentData.domainAverages);
        
        // Créer le nouveau graphique radar
        const ctx = canvas.getContext('2d');
        this.charts.radar = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
                datasets: [{
                    label: 'Scores Actuels',
                    data: this.currentData.domainAverages.current,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(59, 130, 246)',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }, {
                    label: 'Baseline',
                    data: this.currentData.domainAverages.baseline,
                    backgroundColor: 'rgba(156, 163, 175, 0.1)',
                    borderColor: 'rgb(156, 163, 175)',
                    borderWidth: 1,
                    pointBackgroundColor: 'rgb(156, 163, 175)',
                    pointBorderColor: '#fff',
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        min: 0,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.r.toFixed(2)}/5`;
                            }
                        }
                    }
                }
            }
        });
    },

    /**
     * Créer le graphique en barres par domaine
     */
    createDomainBarChart: function() {
        const canvas = document.getElementById('bar-chart');
        if (!canvas) return;

        // Détruire le graphique existant si nécessaire
        if (this.charts.bar) {
            this.charts.bar.destroy();
        }

        console.log('📊 Création du Bar Chart avec données:', this.currentData.domainAverages);

        // Créer le nouveau graphique en barres
        const ctx = canvas.getContext('2d');
        this.charts.bar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
                datasets: [{
                    label: 'Scores par Domaine',
                    data: this.currentData.domainAverages.current,
                    backgroundColor: [
                        this.domainColors.EDM.bg,
                        this.domainColors.APO.bg,
                        this.domainColors.BAI.bg,
                        this.domainColors.DSS.bg,
                        this.domainColors.MEA.bg
                    ],
                    borderColor: [
                        this.domainColors.EDM.border,
                        this.domainColors.APO.border,
                        this.domainColors.BAI.border,
                        this.domainColors.DSS.border,
                        this.domainColors.MEA.border
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
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
                                return `Score: ${context.parsed.y.toFixed(2)}/5`;
                            }
                        }
                    }
                }
            }
        });
    },

    /**
     * Créer le tableau des objectifs avec filtres
     */
    createObjectivesTable: function() {
        const container = document.getElementById('objectives-container');
        if (!container) return;

        console.log('📋 Création du tableau des objectifs');

        // Créer la structure HTML pour les filtres et le tableau
        container.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-blue-900">
                        <i class="fas fa-list-ol mr-2"></i>
                        Objectifs COBIT Impactés (40 objectifs)
                    </h3>
                    <div class="text-sm text-gray-600">
                        <span id="objectives-count">${this.currentData.objectives.length}</span> objectifs
                    </div>
                </div>

                <!-- Filtres -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tri -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trier par:</label>
                            <div class="flex space-x-2">
                                <button data-sort-objectives="score" data-sort-order="desc"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-sort-numeric-down mr-1"></i>Score ↓
                                </button>
                                <button data-sort-objectives="score" data-sort-order="asc"
                                        class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-sort-numeric-up mr-1"></i>Score ↑
                                </button>
                                <button data-sort-objectives="impact" data-sort-order="desc"
                                        class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                                    <i class="fas fa-sort-amount-down mr-1"></i>Impact
                                </button>
                            </div>
                        </div>

                        <!-- Top N -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Afficher:</label>
                            <div class="flex space-x-2">
                                <button data-top-filter="3" class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200 transition-colors">
                                    Top 3
                                </button>
                                <button data-top-filter="5" class="px-3 py-1 text-xs bg-orange-100 text-orange-800 rounded hover:bg-orange-200 transition-colors">
                                    Top 5
                                </button>
                                <button data-top-filter="10" class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition-colors">
                                    Top 10
                                </button>
                                <button data-top-filter="15" class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                                    Top 15
                                </button>
                                <button data-top-filter="40" class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors">
                                    Tous
                                </button>
                            </div>
                        </div>

                        <!-- Domaine -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Domaine:</label>
                            <select id="domain-filter" class="text-xs border border-gray-300 rounded px-2 py-1">
                                <option value="all">Tous les domaines</option>
                                <option value="EDM">EDM - Gouvernance</option>
                                <option value="APO">APO - Alignement</option>
                                <option value="BAI">BAI - Construction</option>
                                <option value="DSS">DSS - Livraison</option>
                                <option value="MEA">MEA - Surveillance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tableau des objectifs -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Rang
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Objectif
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Domaine
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Score
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Impact
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Écart
                                </th>
                            </tr>
                        </thead>
                        <tbody id="objectives-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Les lignes seront générées dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        // Remplir le tableau avec les données
        this.updateObjectivesTable();

        // Ajouter l'écouteur pour le filtre de domaine
        document.getElementById('domain-filter').addEventListener('change', (e) => {
            this.filterSettings.domain = e.target.value;
            this.updateObjectivesTable();
        });
    },

    /**
     * Gérer les changements d'inputs
     */
    handleInputChange: function(input) {
        console.log('🔄 Changement d\'input détecté:', input.name, input.value);

        // Déclencher le recalcul des scores
        this.recalculateScores();
    },

    /**
     * Recalculer les scores en temps réel
     */
    recalculateScores: function() {
        const dfNumber = document.querySelector('[data-df-number]')?.dataset.dfNumber;
        if (!dfNumber) return;

        // Récupérer toutes les valeurs d'inputs
        const inputs = [];
        document.querySelectorAll('.df-input').forEach(input => {
            inputs.push(parseFloat(input.value) || 0);
        });

        console.log('🔢 Recalcul avec inputs:', inputs);

        // Envoyer les données à l'API pour recalcul
        fetch('/cobit/api/update-inputs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                df_number: dfNumber,
                inputs: inputs
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Scores recalculés:', data);
                this.updateChartsWithNewData(data);
            }
        })
        .catch(error => {
            console.error('❌ Erreur lors du recalcul:', error);
        });
    },

    /**
     * Mettre à jour les graphiques avec de nouvelles données
     */
    updateChartsWithNewData: function(data) {
        console.log('🔄 Mise à jour des graphiques avec nouvelles données:', data);

        // Mettre à jour les données locales
        if (data.scores) this.currentData.scores = data.scores;
        if (data.baselines) this.currentData.baselines = data.baselines;
        if (data.domainAverages) this.currentData.domainAverages = data.domainAverages;
        if (data.objectives) this.currentData.objectives = data.objectives;

        // Mettre à jour le graphique radar
        if (this.charts.radar && this.currentData.domainAverages) {
            this.charts.radar.data.datasets[0].data = this.currentData.domainAverages.current;
            this.charts.radar.data.datasets[1].data = this.currentData.domainAverages.baseline;
            this.charts.radar.update('active');
        }

        // Mettre à jour le graphique en barres
        if (this.charts.bar && this.currentData.domainAverages) {
            this.charts.bar.data.datasets[0].data = this.currentData.domainAverages.current;
            this.charts.bar.update('active');
        }

        // Mettre à jour le tableau des objectifs
        this.updateObjectivesTable();

        // Afficher un message de succès
        this.showStatusMessage('✅ Graphiques mis à jour en temps réel', 'success');
    },

    /**
     * Mettre à jour le tableau des objectifs
     */
    updateObjectivesTable: function() {
        const tableBody = document.getElementById('objectives-table-body');
        if (!tableBody) return;

        // Préparer les objectifs avec les données actuelles
        let objectives = this.prepareObjectivesData();

        // Appliquer les filtres et le tri
        objectives = this.filterAndSortObjectives(objectives);

        // Vider le tableau
        tableBody.innerHTML = '';

        // Remplir le tableau avec les objectifs filtrés
        objectives.forEach((objective, index) => {
            const row = document.createElement('tr');
            row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';

            // Déterminer la classe de couleur pour l'impact
            let impactClass = 'text-gray-600';
            if (objective.impact > 0.5) impactClass = 'text-green-600 font-medium';
            else if (objective.impact < -0.5) impactClass = 'text-red-600 font-medium';

            // Déterminer la classe de couleur pour le domaine
            let domainClass = 'text-gray-600';
            if (objective.code.startsWith('EDM')) domainClass = 'text-red-600';
            else if (objective.code.startsWith('APO')) domainClass = 'text-blue-600';
            else if (objective.code.startsWith('BAI')) domainClass = 'text-green-600';
            else if (objective.code.startsWith('DSS')) domainClass = 'text-yellow-600';
            else if (objective.code.startsWith('MEA')) domainClass = 'text-purple-600';

            row.innerHTML = `
                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${index + 1}
                </td>
                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                    <div class="font-medium">${objective.code}</div>
                    <div class="text-xs text-gray-500">${objective.name}</div>
                </td>
                <td class="px-4 py-2 whitespace-nowrap text-sm ${domainClass}">
                    ${objective.domain}
                </td>
                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2"
                             style="background-color: ${this.getScoreColor(objective.score)}">
                            ${objective.score.toFixed(1)}
                        </div>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full"
                                 style="width: ${objective.score * 20}%; background-color: ${this.getScoreColor(objective.score)}"></div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-2 whitespace-nowrap text-sm ${impactClass}">
                    ${objective.impact > 0 ? '+' : ''}${objective.impact.toFixed(2)}
                </td>
                <td class="px-4 py-2 whitespace-nowrap text-sm">
                    <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full"
                             style="width: ${Math.abs(objective.impact) * 20}%;
                                    background-color: ${objective.impact >= 0 ? '#10b981' : '#ef4444'}"></div>
                    </div>
                </td>
            `;

            tableBody.appendChild(row);
        });

        // Mettre à jour le compteur d'objectifs
        const countElement = document.getElementById('objectives-count');
        if (countElement) {
            countElement.textContent = objectives.length;
        }
    },

    /**
     * Préparer les données des objectifs
     */
    prepareObjectivesData: function() {
        // Si nous avons des objectifs définis, les utiliser
        if (this.currentData.objectives && this.currentData.objectives.length > 0) {
            return this.currentData.objectives.map((objective, index) => {
                const score = this.currentData.scores[index] || 0;
                const baseline = this.currentData.baselines[index] || 2.5;
                const impact = score - baseline;

                return {
                    code: objective.code || `Objectif ${index + 1}`,
                    name: objective.name || `Description de l'objectif ${index + 1}`,
                    domain: objective.code ? objective.code.substring(0, 3) : 'N/A',
                    score: score,
                    baseline: baseline,
                    impact: impact
                };
            });
        }

        // Sinon, créer des objectifs par défaut
        return this.getDefaultObjectives();
    },

    /**
     * Filtrer et trier les objectifs
     */
    filterAndSortObjectives: function(objectives) {
        let filtered = [...objectives];

        // Filtrer par domaine
        if (this.filterSettings.domain !== 'all') {
            filtered = filtered.filter(obj => obj.domain === this.filterSettings.domain);
        }

        // Trier selon le critère sélectionné
        if (this.filterSettings.sortBy === 'score') {
            filtered.sort((a, b) => {
                return this.filterSettings.sortOrder === 'desc'
                    ? b.score - a.score
                    : a.score - b.score;
            });
        } else if (this.filterSettings.sortBy === 'impact') {
            filtered.sort((a, b) => {
                return this.filterSettings.sortOrder === 'desc'
                    ? Math.abs(b.impact) - Math.abs(a.impact)
                    : Math.abs(a.impact) - Math.abs(b.impact);
            });
        }

        // Limiter au nombre d'objectifs sélectionné
        if (this.filterSettings.topCount < filtered.length) {
            filtered = filtered.slice(0, this.filterSettings.topCount);
        }

        return filtered;
    },

    /**
     * Gérer les actions de filtrage
     */
    handleFilterAction: function(element) {
        const action = element.dataset.filterAction;

        if (action === 'sort') {
            this.filterSettings.sortBy = element.dataset.sortBy || 'score';
            this.filterSettings.sortOrder = element.dataset.sortOrder || 'desc';
        } else if (action === 'top') {
            this.filterSettings.topCount = parseInt(element.dataset.topCount) || 15;
        } else if (action === 'domain') {
            this.filterSettings.domain = element.dataset.domain || 'all';
        }

        // Mettre à jour le tableau
        this.updateObjectivesTable();
    },

    /**
     * Trier les objectifs
     */
    sortObjectives: function(sortBy, sortOrder) {
        this.filterSettings.sortBy = sortBy;
        this.filterSettings.sortOrder = sortOrder;

        // Mettre à jour l'apparence des boutons de tri
        document.querySelectorAll('[data-sort-objectives]').forEach(button => {
            button.classList.remove('bg-blue-100', 'text-blue-800');
            button.classList.add('bg-gray-100', 'text-gray-800');
        });

        // Mettre en évidence le bouton actif
        const activeButton = document.querySelector(`[data-sort-objectives="${sortBy}"][data-sort-order="${sortOrder}"]`);
        if (activeButton) {
            activeButton.classList.remove('bg-gray-100', 'text-gray-800');
            activeButton.classList.add('bg-blue-100', 'text-blue-800');
        }

        // Mettre à jour le tableau
        this.updateObjectivesTable();
    },

    /**
     * Filtrer les objectifs par nombre (top N)
     */
    filterTopObjectives: function(topCount) {
        this.filterSettings.topCount = topCount;

        // Mettre à jour l'apparence des boutons de filtre
        document.querySelectorAll('[data-top-filter]').forEach(button => {
            button.classList.remove('bg-blue-100', 'text-blue-800');
            button.classList.add('bg-gray-100', 'text-gray-800');
        });

        // Mettre en évidence le bouton actif
        const activeButton = document.querySelector(`[data-top-filter="${topCount}"]`);
        if (activeButton) {
            activeButton.classList.remove('bg-gray-100', 'text-gray-800');
            activeButton.classList.add('bg-blue-100', 'text-blue-800');
        }

        // Mettre à jour le tableau
        this.updateObjectivesTable();
    },

    /**
     * Obtenir la couleur en fonction du score
     */
    getScoreColor: function(score) {
        if (score >= 4) return '#10b981'; // vert
        if (score >= 3) return '#3b82f6'; // bleu
        if (score >= 2) return '#f59e0b'; // orange
        return '#ef4444'; // rouge
    },

    /**
     * Afficher un message de statut
     */
    showStatusMessage: function(message, type = 'info') {
        console.log(`📢 ${type.toUpperCase()}: ${message}`);

        // Créer ou récupérer le conteneur de messages
        let container = document.getElementById('cobit-status-messages');
        if (!container) {
            container = document.createElement('div');
            container.id = 'cobit-status-messages';
            container.className = 'fixed bottom-4 right-4 z-50 flex flex-col space-y-2';
            document.body.appendChild(container);
        }

        // Créer le message
        const messageElement = document.createElement('div');
        messageElement.className = `px-4 py-2 rounded-lg shadow-lg text-sm font-medium transition-all transform translate-y-0 opacity-100 flex items-center space-x-2 ${
            type === 'success' ? 'bg-green-100 text-green-800 border-l-4 border-green-500' :
            type === 'warning' ? 'bg-yellow-100 text-yellow-800 border-l-4 border-yellow-500' :
            type === 'error' ? 'bg-red-100 text-red-800 border-l-4 border-red-500' :
            'bg-blue-100 text-blue-800 border-l-4 border-blue-500'
        }`;

        const icon = document.createElement('span');
        icon.className = type === 'success' ? 'fas fa-check-circle' :
                        type === 'warning' ? 'fas fa-exclamation-triangle' :
                        type === 'error' ? 'fas fa-times-circle' :
                        'fas fa-info-circle';

        const text = document.createElement('span');
        text.textContent = message;

        messageElement.appendChild(icon);
        messageElement.appendChild(text);
        container.appendChild(messageElement);

        // Supprimer le message après 3 secondes
        setTimeout(() => {
            messageElement.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => {
                container.removeChild(messageElement);
            }, 300);
        }, 3000);
    },

    /**
     * Obtenir des objectifs par défaut
     */
    getDefaultObjectives: function() {
        const domains = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
        const objectives = [];

        // Créer 40 objectifs par défaut
        for (let i = 0; i < 40; i++) {
            const domain = domains[Math.floor(i / 8)];
            const number = (i % 8) + 1;
            const code = `${domain}0${number}`;

            objectives.push({
                code: code,
                name: `${domain} Objectif ${number}`,
                domain: domain,
                score: 2.5,
                baseline: 2.5,
                impact: 0
            });
        }

        return objectives;
    }
};

// Initialiser les graphiques au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    CobitInteractiveCharts.init();
});
