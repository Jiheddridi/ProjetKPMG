// COBIT Evaluation JavaScript
let currentDF = 1;
let evaluationData = {};
let charts = {};
let cobitData = {};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('COBIT Evaluation App with AI features initialized');
    
    // Initialiser les event listeners
    initializeEventListeners();
    
    // Charger les données COBIT
    loadCOBITData();
    
    // Initialiser les données d'évaluation
    initializeEvaluationData();
    
    // Afficher le premier DF
    switchToDF(1);
    
    // Mettre à jour le progrès global
    updateGlobalProgress();
});

// Initialiser les event listeners
function initializeEventListeners() {
    // Event listeners pour les onglets DF
    document.querySelectorAll('.df-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const dfNumber = this.getAttribute('data-df');
            if (dfNumber) {
                switchToDF(parseInt(dfNumber));
            }
        });
    });
    
    // Event listeners pour les inputs
    document.querySelectorAll('input[type="range"]').forEach(input => {
        input.addEventListener('input', function() {
            const dfNumber = this.getAttribute('data-df');
            if (dfNumber) {
                updateDFData(parseInt(dfNumber));
            }
        });
    });
}

// Charger les données COBIT
function loadCOBITData() {
    // Données COBIT 2019 simplifiées
    cobitData = {
        objectives: [
            'EDM01', 'EDM02', 'EDM03', 'EDM04', 'EDM05',
            'APO01', 'APO02', 'APO03', 'APO04', 'APO05', 'APO06', 'APO07', 'APO08', 'APO09', 'APO10', 'APO11', 'APO12', 'APO13', 'APO14',
            'BAI01', 'BAI02', 'BAI03', 'BAI04', 'BAI05', 'BAI06', 'BAI07', 'BAI08', 'BAI09', 'BAI10', 'BAI11',
            'DSS01', 'DSS02', 'DSS03', 'DSS04', 'DSS05', 'DSS06',
            'MEA01', 'MEA02', 'MEA03', 'MEA04'
        ],
        dfMap: generateDFMaps()
    };
}

// Générer les mappings DF vers objectifs
function generateDFMaps() {
    const dfMaps = {};
    
    for (let df = 1; df <= 10; df++) {
        dfMaps[`DF${df}`] = generateMatrix(40, 3, 0.1, 0.3);
    }
    
    return dfMaps;
}

// Générer une matrice pour un DF
function generateMatrix(objectives, inputs, min, max) {
    const matrix = [];
    
    for (let i = 0; i < objectives; i++) {
        const row = [];
        for (let j = 0; j < inputs; j++) {
            row.push(Math.random() * (max - min) + min);
        }
        matrix.push(row);
    }
    
    return matrix;
}

// Initialiser les données d'évaluation
function initializeEvaluationData() {
    for (let i = 1; i <= 10; i++) {
        evaluationData[`DF${i}`] = {
            inputs: [0, 0, 0],
            scores: [],
            baselines: []
        };
    }
}

// MATRICE DE POIDS AVANCEE COBIT (exemple, à adapter pour chaque DF)
const staticDFMap = {
    DF1: [
        [1, 0, 0], [1, 0, 0], [1, 0, 0], [1, 0, 0], [1, 0, 0], // EDM01-05
        [0, 1, 0], [0, 1, 0], [0, 1, 0], [0, 1, 0], [0, 1, 0], [0, 1, 0], [0, 1, 0], [0, 1, 0], [0, 1, 0], // APO01-14
        [0, 0, 1], [0, 0, 1], [0, 0, 1], [0, 0, 1], [0, 0, 1], [0, 0, 1], [0, 0, 1], [0, 0, 1], [0, 0, 1], [0, 0, 1], // BAI01-11
        [1, 1, 0], [1, 1, 0], [1, 1, 0], [1, 1, 0], [1, 1, 0], [1, 1, 0], // DSS01-06
        [0, 1, 1], [0, 1, 1], [0, 1, 1], [0, 1, 1] // MEA01-04
    ],
    // DF2, DF3, ... à compléter selon la cartographie COBIT
};
// BASELINE AVANCEE COBIT (modifiable par domaine/objectif)
const staticBaselines = [
    3, 3, 3, 3, 3, // EDM
    3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, // APO
    3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, // BAI
    3, 3, 3, 3, 3, 3, // DSS
    3, 3, 3, 3 // MEA
];

// Changer vers un DF spécifique
function switchToDF(dfNumber) {
    currentDF = dfNumber;
    
    // Masquer tous les contenus DF
    document.querySelectorAll('.df-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Afficher le DF sélectionné
    const dfContent = document.getElementById(`df${dfNumber}-content`);
    if (dfContent) {
        dfContent.style.display = 'block';
    }
    
    // Mettre à jour les onglets
    document.querySelectorAll('.df-tab').forEach(tab => {
        tab.classList.remove('bg-blue-600', 'text-white');
        tab.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    const activeTab = document.getElementById(`tab-df${dfNumber}`);
    if (activeTab) {
        activeTab.classList.remove('bg-gray-100', 'text-gray-700');
        activeTab.classList.add('bg-blue-600', 'text-white');
    }
    
    // Mettre à jour les données et graphiques
    updateDFData(dfNumber);
    updateDFCharts(dfNumber);
    updateDFStats(dfNumber);
}

// Mettre à jour les données d'un DF (ajout du calcul de risque)
function updateDFData(dfNumber) {
    const inputs = [];
    
    // Récupérer les valeurs des inputs
    document.querySelectorAll(`input[data-df="${dfNumber}"]`).forEach(input => {
        inputs.push(parseFloat(input.value) || 0);
        
        // Mettre à jour l'affichage de la valeur
        const valueDisplay = document.getElementById(input.id + '-value');
        if (valueDisplay) {
            valueDisplay.textContent = input.value;
        }
    });
    
    // Calculer les scores
    const scores = calculateCOBITScores(dfNumber, inputs);
    const baselines = getCOBITBaselines();
    const risks = calculateCOBITRisks(scores, baselines);
    
    // Sauvegarder les données
    evaluationData[`DF${dfNumber}`] = {
        inputs: inputs,
        scores: scores,
        baselines: baselines,
        risks: risks
    };
    
    // Envoyer au serveur
    fetch('/cobit/api/update-inputs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            df: dfNumber,
            inputs: inputs
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(`DF${dfNumber} data updated successfully`);
        }
    })
    .catch(error => {
        console.error('Error updating DF data:', error);
    });
}

// Calculer les scores COBIT (optimisé)
function calculateCOBITScores(dfNumber, inputs) {
    const dfKey = `DF${dfNumber}`;
    const dfMap = staticDFMap[dfKey];
    if (!dfMap) {
        console.error(`No advanced COBIT map for ${dfKey}`);
        return [];
    }
    const scores = dfMap.map((weights, objIndex) => {
        return weights.reduce((sum, weight, inputIndex) => sum + (inputs[inputIndex] || 0) * weight, 0);
    });
    return scores;
}

// Calculer la baseline COBIT (optimisé)
function getCOBITBaselines() {
    return staticBaselines.slice(); // Copie défensive
}

// Calculer le risque/écart pour chaque objectif
function calculateCOBITRisks(scores, baselines) {
    return scores.map((score, i) => {
        const baseline = baselines[i];
        if (score < baseline - 0.5) return 'Risque élevé';
        if (score > baseline + 0.5) return 'Surperformance';
        return 'Conforme';
    });
}

// Mettre à jour les graphiques d'un DF
function updateDFCharts(dfNumber) {
    const dfData = evaluationData[`DF${dfNumber}`];
    if (!dfData) return;
    
    // Graphique en barres
    updateBarChart(dfNumber, dfData.scores);
    
    // Graphique radar
    updateRadarChart(dfNumber, dfData.scores, dfData.baselines);
    
    // Graphique en secteurs
    updatePieChart(dfNumber, dfData.scores);
}

// Mettre à jour le graphique en barres
function updateBarChart(dfNumber, scores) {
    const ctx = document.getElementById(`bar-chart-df${dfNumber}`);
    if (!ctx) return;
    
    if (charts[`bar-df${dfNumber}`]) {
        charts[`bar-df${dfNumber}`].destroy();
    }
    
    charts[`bar-df${dfNumber}`] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: cobitData.objectives.slice(0, 10), // Afficher seulement les 10 premiers
            datasets: [{
                label: 'Scores',
                data: scores.slice(0, 10),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5
                }
            }
        }
    });
}

// Mettre à jour le graphique radar
function updateRadarChart(dfNumber, scores, baselines) {
    const ctx = document.getElementById(`radar-chart-df${dfNumber}`);
    if (!ctx) return;
    
    if (charts[`radar-df${dfNumber}`]) {
        charts[`radar-df${dfNumber}`].destroy();
    }
    
    // Calculer les moyennes par domaine
    const domainData = calculateDomainAverages(scores, baselines);
    
    charts[`radar-df${dfNumber}`] = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
            datasets: [{
                label: 'Scores Actuels',
                data: domainData.current,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 2
            }, {
                label: 'Baseline',
                data: domainData.baseline,
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
                    max: 5
                }
            }
        }
    });
}

// Calculer les moyennes par domaine
function calculateDomainAverages(scores, baselines) {
    const domains = {
        'EDM': [0, 1, 2, 3, 4],
        'APO': [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
        'BAI': [19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29],
        'DSS': [30, 31, 32, 33, 34, 35],
        'MEA': [36, 37, 38, 39]
    };
    
    const current = [];
    const baseline = [];
    
    Object.values(domains).forEach(indices => {
        let currentSum = 0;
        let baselineSum = 0;
        
        indices.forEach(index => {
            currentSum += scores[index] || 0;
            baselineSum += baselines[index] || 0;
        });
        
        current.push(currentSum / indices.length);
        baseline.push(baselineSum / indices.length);
    });
    
    return { current, baseline };
}

// Fonctions globales pour les événements onclick
window.switchToDF = switchToDF;
window.resetDF = function(dfNumber) {
    if (confirm(`Êtes-vous sûr de vouloir réinitialiser DF${dfNumber} ?`)) {
        // Réinitialiser les inputs
        document.querySelectorAll(`input[data-df="${dfNumber}"]`).forEach(input => {
            input.value = 0;
            const valueDisplay = document.getElementById(input.id + '-value');
            if (valueDisplay) {
                valueDisplay.textContent = '0';
            }
        });
        
        // Mettre à jour les données
        updateDFData(dfNumber);
        updateDFCharts(dfNumber);
        updateDFStats(dfNumber);
        updateGlobalProgress();
    }
};

window.saveDFData = function(dfNumber) {
    updateDFData(dfNumber);
    alert(`Données DF${dfNumber} sauvegardées !`);
};

window.sortDFObjectives = function(dfNumber, sortBy) {
    console.log(`Sorting DF${dfNumber} objectives by ${sortBy}`);
    // Implémentation du tri sera ajoutée
};

window.switchToResults = function() {
    console.log('Switching to results');
    // Implémentation des résultats sera ajoutée
};

// Fonctions de mise à jour des statistiques et de l'IA
window.updateDFStats = function(dfNumber) {
    console.log(`Updating stats for DF${dfNumber}`);
    // Implémentation sera ajoutée
};

window.updateGlobalProgress = function() {
    console.log('Updating global progress');
    // Implémentation sera ajoutée
};
