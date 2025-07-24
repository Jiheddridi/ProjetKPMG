<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'COBIT 2019 Design Toolkit')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/cobit.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary: #1E40AF;
            --secondary: #3B82F6;
            --accent: #10B981;
            --dark: #1F2937;
            --light: #F9FAFB;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e6f0fa 100%);
            color: #1F2937;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .slider-container {
            margin: 10px 0;
        }
        
        .slider {
            width: 100%;
            height: 8px;
            border-radius: 5px;
            background: #d1d5db;
            outline: none;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .slider:hover {
            opacity: 1;
        }
        
        .slider::-webkit-slider-thumb {
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #1D4ED8;
        }
        
        .btn-secondary {
            background: #6B7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4B5563;
        }
        
        .btn-success {
            background: var(--accent);
            color: white;
        }
        
        .btn-success:hover {
            background: #059669;
        }
        
        .btn-danger {
            background: #DC2626;
            color: white;
        }
        
        .btn-danger:hover {
            background: #B91C1C;
        }
        
        .progress-step {
            transition: all 0.3s ease;
        }
        
        .progress-step.active {
            background: var(--primary);
            color: white;
        }
        
        .progress-step.completed {
            background: var(--accent);
            color: white;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background: var(--accent);
        }
        
        .notification.error {
            background: #DC2626;
        }
        
        .notification.warning {
            background: #F59E0B;
        }
        
        .notification.info {
            background: var(--secondary);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">COBIT 2019 Design Toolkit</h1>
                        <p class="text-sm text-gray-600">Évaluation de la Gouvernance IT</p>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <a href="{{ route('cobit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    <a href="{{ route('cobit.evaluation') }}" class="btn btn-primary">
                        <i class="fas fa-clipboard-check mr-2"></i>Évaluation
                    </a>
                    <a href="{{ route('cobit.results') }}" class="btn btn-success">
                        <i class="fas fa-chart-bar mr-2"></i>Résultats
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen p-6">
        <div class="max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    <!-- Scripts -->
    <script>
        // Configuration globale
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}'
        };

        // Fonction pour afficher les notifications
        function showNotification(message, type = 'info', duration = 3000) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
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
        }

        // Configuration AJAX globale
        fetch.defaults = {
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };
    </script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('js/cobit-utils.js') }}"></script>
    <script src="{{ asset('js/cobit.js') }}"></script>

    @stack('scripts')
</body>
</html>
