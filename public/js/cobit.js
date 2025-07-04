/**
 * COBIT 2019 Design Toolkit - JavaScript Principal
 */

// Configuration globale
window.CobitApp = {
    charts: {},
    currentStep: 1,
    totalSteps: 5,
    evaluationData: {},
    
    // Configuration des couleurs
    colors: {
        primary: '#1E40AF',
        secondary: '#3B82F6',
        accent: '#10B981',
        danger: '#DC2626',
        warning: '#F59E0B',
        success: '#059669',
        gray: '#6B7280'
    },
    
    // Configuration Chart.js par défaut
    chartDefaults: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            }
        },
        animation: {
            duration: 300
        }
    }
};

/**
 * Utilitaires généraux
 */
const CobitUtils = {
    /**
     * Afficher une notification
     */
    showNotification(message, type = 'info', duration = 3000) {
        const container = document.getElementById('notification-container') || document.body;
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-${this.getNotificationIcon(type)} mr-3"></i>
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        container.appendChild(notification);
        
        // Afficher la notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Masquer automatiquement
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    },
    
    /**
     * Obtenir l'icône pour le type de notification
     */
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    },
    
    /**
     * Requête AJAX avec gestion d'erreurs
     */
    async apiRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        };
        
        try {
            const response = await fetch(url, { ...defaultOptions, ...options });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Erreur API:', error);
            this.showNotification(`Erreur: ${error.message}`, 'error');
            throw error;
        }
    },
    
    /**
     * Formater un nombre avec décimales
     */
    formatNumber(number, decimals = 2) {
        return Number(number).toFixed(decimals);
    },
    
    /**
     * Débounce pour limiter les appels de fonction
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

/**
 * Gestionnaire de graphiques
 */
const CobitCharts = {
    /**
     * Créer un graphique radar
     */
    createRadarChart(canvasId, data, options = {}) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.warn(`Canvas ${canvasId} non trouvé`);
            return null;
        }
        
        // Détruire le graphique existant
        if (window.CobitApp.charts[canvasId]) {
            window.CobitApp.charts[canvasId].destroy();
        }
        
        const defaultOptions = {
            type: 'radar',
            data: {
                labels: data.labels || [],
                datasets: data.datasets || []
            },
            options: {
                ...window.CobitApp.chartDefaults,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        angleLines: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                ...options
            }
        };
        
        window.CobitApp.charts[canvasId] = new Chart(ctx, defaultOptions);
        return window.CobitApp.charts[canvasId];
    },
    
    /**
     * Créer un graphique en barres
     */
    createBarChart(canvasId, data, options = {}) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.warn(`Canvas ${canvasId} non trouvé`);
            return null;
        }
        
        // Détruire le graphique existant
        if (window.CobitApp.charts[canvasId]) {
            window.CobitApp.charts[canvasId].destroy();
        }
        
        const defaultOptions = {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: data.datasets || []
            },
            options: {
                ...window.CobitApp.chartDefaults,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45
                        }
                    }
                },
                ...options
            }
        };
        
        window.CobitApp.charts[canvasId] = new Chart(ctx, defaultOptions);
        return window.CobitApp.charts[canvasId];
    },
    
    /**
     * Mettre à jour un graphique existant
     */
    updateChart(canvasId, newData) {
        const chart = window.CobitApp.charts[canvasId];
        if (!chart) {
            console.warn(`Graphique ${canvasId} non trouvé`);
            return;
        }
        
        // Mettre à jour les données
        if (newData.labels) {
            chart.data.labels = newData.labels;
        }
        
        if (newData.datasets) {
            chart.data.datasets = newData.datasets;
        }
        
        chart.update('none'); // Pas d'animation pour les mises à jour en temps réel
    },
    
    /**
     * Détruire tous les graphiques
     */
    destroyAllCharts() {
        Object.values(window.CobitApp.charts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        window.CobitApp.charts = {};
    }
};

/**
 * Gestionnaire d'évaluation
 */
const CobitEvaluation = {
    /**
     * Sauvegarder l'étape actuelle
     */
    async saveCurrentStep(step, inputs) {
        try {
            const response = await CobitUtils.apiRequest('/cobit/save-evaluation', {
                method: 'POST',
                body: JSON.stringify({
                    step: step,
                    inputs: inputs
                })
            });
            
            if (response.success) {
                CobitUtils.showNotification('Données sauvegardées avec succès', 'success');
                return true;
            }
        } catch (error) {
            CobitUtils.showNotification('Erreur lors de la sauvegarde', 'error');
            return false;
        }
    },
    
    /**
     * Mettre à jour les inputs en temps réel
     */
    async updateInputs(dfNumber, inputs) {
        try {
            const response = await CobitUtils.apiRequest('/cobit/api/update-inputs', {
                method: 'POST',
                body: JSON.stringify({
                    dfNumber: dfNumber,
                    inputs: inputs
                })
            });
            
            if (response.success) {
                return response;
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour:', error);
            return null;
        }
    },
    
    /**
     * Réinitialiser toutes les données
     */
    async resetAllData() {
        if (!confirm('Êtes-vous sûr de vouloir réinitialiser toutes les données ? Cette action est irréversible.')) {
            return false;
        }
        
        try {
            const response = await CobitUtils.apiRequest('/cobit/reset', {
                method: 'DELETE'
            });
            
            if (response.success) {
                CobitUtils.showNotification('Données réinitialisées', 'success');
                return true;
            }
        } catch (error) {
            CobitUtils.showNotification('Erreur lors de la réinitialisation', 'error');
            return false;
        }
    }
};

/**
 * Gestionnaire d'export
 */
const CobitExport = {
    /**
     * Exporter en JSON
     */
    exportToJSON(data, filename = null) {
        const exportData = {
            timestamp: new Date().toISOString(),
            version: '1.0',
            data: data
        };
        
        const blob = new Blob([JSON.stringify(exportData, null, 2)], { 
            type: 'application/json' 
        });
        
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename || `cobit-evaluation-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        CobitUtils.showNotification('Données exportées avec succès', 'success');
    },
    
    /**
     * Importer depuis JSON
     */
    async importFromJSON(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                try {
                    const data = JSON.parse(e.target.result);
                    resolve(data);
                } catch (error) {
                    reject(new Error('Fichier JSON invalide'));
                }
            };
            
            reader.onerror = function() {
                reject(new Error('Erreur lors de la lecture du fichier'));
            };
            
            reader.readAsText(file);
        });
    }
};

/**
 * Initialisation de l'application
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('COBIT 2019 Design Toolkit initialisé');
    
    // Configuration globale des graphiques Chart.js
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#374151';
        Chart.defaults.borderColor = 'rgba(0, 0, 0, 0.1)';
    }
    
    // Gestionnaire global d'erreurs
    window.addEventListener('error', function(e) {
        console.error('Erreur JavaScript:', e.error);
        CobitUtils.showNotification('Une erreur inattendue s\'est produite', 'error');
    });
    
    // Gestionnaire pour les clics sur les modals
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.style.display = 'none';
        }
    });
});

// Exposer les utilitaires globalement
window.CobitUtils = CobitUtils;
window.CobitCharts = CobitCharts;
window.CobitEvaluation = CobitEvaluation;
window.CobitExport = CobitExport;
