/**
 * COBIT Utilities - Fonctions utilitaires pour l'application COBIT
 */

// Namespace pour les utilitaires COBIT
window.CobitUtils = {
    /**
     * Afficher une notification
     */
    showNotification: function(message, type = 'info') {
        // Créer l'élément de notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
        
        // Définir les couleurs selon le type
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-black',
            info: 'bg-blue-500 text-white'
        };
        
        notification.className += ` ${colors[type] || colors.info}`;
        
        // Définir les icônes selon le type
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠',
            info: 'ℹ'
        };
        
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="text-lg mr-2">${icons[type] || icons.info}</span>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg hover:opacity-75">×</button>
            </div>
        `;
        
        // Ajouter au DOM
        document.body.appendChild(notification);
        
        // Animation d'entrée
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Suppression automatique après 5 secondes
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    },

    /**
     * Formater un nombre avec des décimales
     */
    formatNumber: function(number, decimals = 2) {
        return Number(number).toFixed(decimals);
    },

    /**
     * Obtenir la couleur selon la priorité
     */
    getPriorityColor: function(priority) {
        const colors = {
            'H': 'text-red-600',
            'M': 'text-yellow-600',
            'L': 'text-green-600'
        };
        return colors[priority] || 'text-gray-600';
    },

    /**
     * Obtenir le texte de priorité
     */
    getPriorityText: function(priority) {
        const texts = {
            'H': 'Haute',
            'M': 'Moyenne',
            'L': 'Faible'
        };
        return texts[priority] || 'Inconnue';
    },

    /**
     * Valider les inputs d'un DF
     */
    validateDFInputs: function(inputs, min = 0, max = 5) {
        if (!Array.isArray(inputs)) return false;
        
        return inputs.every(input => {
            const num = Number(input);
            return !isNaN(num) && num >= min && num <= max;
        });
    },

    /**
     * Générer un ID unique
     */
    generateId: function() {
        return 'cobit_' + Math.random().toString(36).substr(2, 9);
    },

    /**
     * Sauvegarder dans le localStorage
     */
    saveToStorage: function(key, data) {
        try {
            localStorage.setItem(key, JSON.stringify(data));
            return true;
        } catch (error) {
            console.error('Erreur de sauvegarde:', error);
            return false;
        }
    },

    /**
     * Charger depuis le localStorage
     */
    loadFromStorage: function(key) {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : null;
        } catch (error) {
            console.error('Erreur de chargement:', error);
            return null;
        }
    },

    /**
     * Effacer du localStorage
     */
    clearStorage: function(key) {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (error) {
            console.error('Erreur de suppression:', error);
            return false;
        }
    }
};

// Namespace pour les exports
window.CobitExport = {
    /**
     * Exporter en JSON
     */
    exportToJSON: function(data, filename = 'cobit-export.json') {
        try {
            const jsonString = JSON.stringify(data, null, 2);
            const blob = new Blob([jsonString], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            CobitUtils.showNotification('Export JSON réussi', 'success');
        } catch (error) {
            console.error('Erreur d\'export JSON:', error);
            CobitUtils.showNotification('Erreur lors de l\'export JSON', 'error');
        }
    },

    /**
     * Exporter en CSV
     */
    exportToCSV: function(data, filename = 'cobit-export.csv') {
        try {
            let csvContent = '';
            
            // En-têtes
            if (data.length > 0) {
                csvContent += Object.keys(data[0]).join(',') + '\n';
                
                // Données
                data.forEach(row => {
                    csvContent += Object.values(row).map(value => 
                        typeof value === 'string' ? `"${value}"` : value
                    ).join(',') + '\n';
                });
            }
            
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            CobitUtils.showNotification('Export CSV réussi', 'success');
        } catch (error) {
            console.error('Erreur d\'export CSV:', error);
            CobitUtils.showNotification('Erreur lors de l\'export CSV', 'error');
        }
    }
};

// Gestionnaire d'erreurs global pour JavaScript
window.addEventListener('error', function(event) {
    console.error('Erreur JavaScript:', event.error);
    CobitUtils.showNotification('Une erreur JavaScript s\'est produite', 'error');
});

// Gestionnaire pour les erreurs de promesses non gérées
window.addEventListener('unhandledrejection', function(event) {
    console.error('Promesse rejetée:', event.reason);
    CobitUtils.showNotification('Erreur de traitement asynchrone', 'error');
});

console.log('COBIT Utils loaded successfully');
