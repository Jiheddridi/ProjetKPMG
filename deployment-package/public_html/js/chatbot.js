/**
 * Widget Chatbot COBIT 2019 pour Laravel
 * Intégration avec l'API FastAPI via proxy Laravel
 */

class CobitChatbot {
    constructor() {
        this.isOpen = false;
        this.isLoading = false;
        this.messages = [];
        this.suggestions = [];
        
        // URLs de l'API Laravel
        this.apiUrls = {
            health: '/cobit/chatbot/health',
            query: '/cobit/chatbot/query',
            suggestions: '/cobit/chatbot/suggestions'
        };
        
        // Initialisation
        this.init();
    }

    /**
     * Initialisation du chatbot
     */
    async init() {
        this.createWidget();
        this.bindEvents();
        await this.checkHealth();
        await this.loadSuggestions();
        this.showWelcomeMessage();
    }

    /**
     * Création du widget HTML
     */
    createWidget() {
        const container = document.createElement('div');
        container.className = 'chatbot-container';
        container.innerHTML = `
            <!-- Bouton de toggle -->
            <button class="chatbot-toggle" id="chatbot-toggle">
                <i class="fas fa-comments"></i>
                <div class="chatbot-notification" id="chatbot-notification" style="display: none;">1</div>
            </button>

            <!-- Widget principal -->
            <div class="chatbot-widget" id="chatbot-widget">
                <!-- Header -->
                <div class="chatbot-header">
                    <div class="chatbot-header-content">
                        <div class="chatbot-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <h3 class="chatbot-title">Assistant COBIT 2019</h3>
                            <p class="chatbot-subtitle">Expert en gouvernance IT</p>
                        </div>
                    </div>
                    <button class="chatbot-close" id="chatbot-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Zone de messages -->
                <div class="chatbot-messages" id="chatbot-messages">
                    <!-- Les messages seront ajoutés ici dynamiquement -->
                </div>

                <!-- Zone de saisie -->
                <div class="chatbot-input-area">
                    <div class="chatbot-input-container">
                        <textarea 
                            class="chatbot-input" 
                            id="chatbot-input" 
                            placeholder="Posez votre question sur COBIT 2019..."
                            rows="1"
                        ></textarea>
                        <button class="chatbot-send-btn" id="chatbot-send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="chatbot-suggestions" id="chatbot-suggestions">
                        <!-- Les suggestions seront ajoutées ici -->
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(container);
        
        // Références aux éléments
        this.elements = {
            toggle: document.getElementById('chatbot-toggle'),
            widget: document.getElementById('chatbot-widget'),
            close: document.getElementById('chatbot-close'),
            messages: document.getElementById('chatbot-messages'),
            input: document.getElementById('chatbot-input'),
            send: document.getElementById('chatbot-send'),
            suggestions: document.getElementById('chatbot-suggestions'),
            notification: document.getElementById('chatbot-notification')
        };
    }

    /**
     * Liaison des événements
     */
    bindEvents() {
        // Toggle du widget
        this.elements.toggle.addEventListener('click', () => this.toggle());
        this.elements.close.addEventListener('click', () => this.close());

        // Envoi de message
        this.elements.send.addEventListener('click', () => this.sendMessage());
        this.elements.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Auto-resize du textarea
        this.elements.input.addEventListener('input', () => {
            this.elements.input.style.height = 'auto';
            this.elements.input.style.height = Math.min(this.elements.input.scrollHeight, 100) + 'px';
        });

        // Fermeture en cliquant à l'extérieur
        document.addEventListener('click', (e) => {
            if (this.isOpen && !e.target.closest('.chatbot-container')) {
                this.close();
            }
        });
    }

    /**
     * Vérification de l'état du chatbot
     */
    async checkHealth() {
        try {
            const response = await fetch(this.apiUrls.health);
            const data = await response.json();
            
            if (data.chatbot_available) {
                console.log('✅ Chatbot COBIT disponible');
                this.hideNotification();
            } else {
                console.warn('⚠️ Chatbot COBIT non disponible');
                this.showNotification();
            }
        } catch (error) {
            console.error('❌ Erreur lors de la vérification du chatbot:', error);
            this.showNotification();
        }
    }

    /**
     * Chargement des suggestions
     */
    async loadSuggestions() {
        try {
            const response = await fetch(this.apiUrls.suggestions);
            const data = await response.json();
            
            if (data.status === 'success') {
                this.suggestions = data.suggestions;
                this.renderSuggestions();
            }
        } catch (error) {
            console.error('Erreur lors du chargement des suggestions:', error);
        }
    }

    /**
     * Affichage des suggestions
     */
    renderSuggestions() {
        if (!this.suggestions.length) return;

        // Prendre quelques suggestions aléatoires
        const randomSuggestions = [];
        this.suggestions.forEach(category => {
            if (category.questions && category.questions.length > 0) {
                const randomQuestion = category.questions[Math.floor(Math.random() * category.questions.length)];
                randomSuggestions.push(randomQuestion);
            }
        });

        // Limiter à 3 suggestions
        const displaySuggestions = randomSuggestions.slice(0, 3);

        this.elements.suggestions.innerHTML = displaySuggestions
            .map(suggestion => `
                <button class="chatbot-suggestion" onclick="cobitChatbot.selectSuggestion('${suggestion.replace(/'/g, "\\'")}')">
                    ${suggestion}
                </button>
            `).join('');
    }

    /**
     * Sélection d'une suggestion
     */
    selectSuggestion(suggestion) {
        this.elements.input.value = suggestion;
        this.sendMessage();
    }

    /**
     * Message de bienvenue
     */
    showWelcomeMessage() {
        const welcomeHtml = `
            <div class="chatbot-welcome">
                <div class="chatbot-welcome-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="chatbot-welcome-title">Bonjour ! 👋</h3>
                <p class="chatbot-welcome-text">
                    Je suis votre assistant COBIT 2019. Je peux répondre à vos questions sur la gouvernance et la gestion IT selon le framework COBIT.
                </p>
            </div>
        `;
        
        this.elements.messages.innerHTML = welcomeHtml;
    }

    /**
     * Toggle du widget
     */
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    /**
     * Ouverture du widget
     */
    open() {
        this.isOpen = true;
        this.elements.widget.classList.add('active');
        this.elements.input.focus();
        this.hideNotification();
    }

    /**
     * Fermeture du widget
     */
    close() {
        this.isOpen = false;
        this.elements.widget.classList.remove('active');
    }

    /**
     * Affichage de la notification
     */
    showNotification() {
        this.elements.notification.style.display = 'flex';
    }

    /**
     * Masquage de la notification
     */
    hideNotification() {
        this.elements.notification.style.display = 'none';
    }

    /**
     * Envoi d'un message
     */
    async sendMessage() {
        const message = this.elements.input.value.trim();
        if (!message || this.isLoading) return;

        // Ajouter le message utilisateur
        this.addMessage(message, 'user');
        this.elements.input.value = '';
        this.elements.input.style.height = 'auto';

        // Désactiver l'interface pendant le traitement
        this.setLoading(true);

        try {
            // Afficher l'indicateur de frappe
            this.showTyping();

            // Envoyer la requête
            const response = await fetch(this.apiUrls.query, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ question: message })
            });

            const data = await response.json();

            // Masquer l'indicateur de frappe
            this.hideTyping();

            if (data.status === 'success') {
                // Ajouter la réponse du bot
                this.addMessage(data.answer, 'bot');
            } else {
                // Afficher l'erreur
                this.addMessage(
                    `Désolé, une erreur s'est produite : ${data.message}`,
                    'bot',
                    true
                );
            }

        } catch (error) {
            console.error('Erreur lors de l\'envoi du message:', error);
            this.hideTyping();
            this.addMessage(
                'Désolé, je ne peux pas répondre pour le moment. Veuillez réessayer plus tard.',
                'bot',
                true
            );
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Ajout d'un message à la conversation
     */
    addMessage(content, sender, isError = false) {
        const messageId = 'msg-' + Date.now();
        const time = new Date().toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        const messageHtml = `
            <div class="chatbot-message ${sender}" id="${messageId}">
                <div class="chatbot-message-avatar">
                    ${sender === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>'}
                </div>
                <div class="chatbot-message-content ${isError ? 'chatbot-error' : ''}">
                    ${this.formatMessage(content)}
                    <div class="chatbot-message-time">${time}</div>
                </div>
            </div>
        `;

        this.elements.messages.insertAdjacentHTML('beforeend', messageHtml);
        this.scrollToBottom();

        // Ajouter à l'historique
        this.messages.push({
            id: messageId,
            content,
            sender,
            timestamp: new Date(),
            isError
        });
    }

    /**
     * Formatage du message (support basique du markdown)
     */
    formatMessage(content) {
        return content
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }

    /**
     * Affichage de l'indicateur de frappe
     */
    showTyping() {
        const typingHtml = `
            <div class="chatbot-message bot" id="typing-indicator">
                <div class="chatbot-message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chatbot-message-content">
                    <div class="chatbot-typing">
                        <div class="chatbot-typing-dots">
                            <div class="chatbot-typing-dot"></div>
                            <div class="chatbot-typing-dot"></div>
                            <div class="chatbot-typing-dot"></div>
                        </div>
                        <span style="margin-left: 8px; font-size: 12px; color: #6B7280;">
                            Assistant en train d'écrire...
                        </span>
                    </div>
                </div>
            </div>
        `;

        this.elements.messages.insertAdjacentHTML('beforeend', typingHtml);
        this.scrollToBottom();
    }

    /**
     * Masquage de l'indicateur de frappe
     */
    hideTyping() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    /**
     * Gestion de l'état de chargement
     */
    setLoading(loading) {
        this.isLoading = loading;
        this.elements.send.disabled = loading;
        this.elements.input.disabled = loading;
        
        if (loading) {
            this.elements.send.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        } else {
            this.elements.send.innerHTML = '<i class="fas fa-paper-plane"></i>';
        }
    }

    /**
     * Scroll vers le bas
     */
    scrollToBottom() {
        setTimeout(() => {
            this.elements.messages.scrollTop = this.elements.messages.scrollHeight;
        }, 100);
    }
}

// Initialisation automatique quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    // Vérifier que nous sommes sur la bonne page
    if (window.location.pathname.includes('/cobit/home') || window.location.pathname.includes('/cobit/')) {
        window.cobitChatbot = new CobitChatbot();
        console.log('🤖 Chatbot COBIT 2019 initialisé');
    }
});

// Export pour utilisation globale
window.CobitChatbot = CobitChatbot;
