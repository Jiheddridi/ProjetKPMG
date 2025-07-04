{{-- 
    Composant Chatbot COBIT 2019
    Widget de chat flottant pour l'assistance COBIT
--}}

<!-- Styles CSS pour le chatbot -->
<link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">

<!-- Meta CSRF pour les requêtes AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Le widget sera créé dynamiquement par JavaScript -->
<!-- Aucun HTML statique nécessaire ici -->

<!-- JavaScript du chatbot -->
<script src="{{ asset('js/chatbot.js') }}"></script>

<script>
    // Configuration spécifique pour cette instance
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier que le chatbot est bien initialisé
        if (window.cobitChatbot) {
            console.log('✅ Chatbot COBIT 2019 prêt sur la page {{ request()->path() }}');
            
            // Optionnel : personnalisation selon la page
            @if(request()->is('cobit/home'))
                // Configuration spéciale pour la page d'accueil
                console.log('🏠 Chatbot configuré pour la page d\'accueil');
            @endif
        } else {
            console.warn('⚠️ Chatbot COBIT non initialisé');
        }
    });
</script>

<style>
    /* Styles spécifiques pour l'intégration dans le thème KPMG */
    .chatbot-container {
        /* S'assurer que le chatbot ne gêne pas les autres éléments */
        z-index: 9999;
    }
    
    /* Adaptation pour les écrans mobiles */
    @media (max-width: 768px) {
        .chatbot-container {
            bottom: 15px;
            right: 15px;
        }
    }
    
    /* Intégration harmonieuse avec le design KPMG */
    .chatbot-toggle {
        background: linear-gradient(135deg, #00338D 0%, #0066CC 100%);
        box-shadow: 0 8px 32px rgba(0, 51, 141, 0.3);
    }
    
    .chatbot-toggle:hover {
        box-shadow: 0 12px 40px rgba(0, 51, 141, 0.4);
    }
    
    /* Animation d'entrée douce */
    .chatbot-container {
        animation: chatbotFadeIn 0.5s ease-out;
    }
    
    @keyframes chatbotFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
