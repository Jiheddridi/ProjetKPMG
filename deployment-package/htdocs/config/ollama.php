<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ollama Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'intégration Ollama avec COBIT 2019
    |
    */

    'host' => env('OLLAMA_HOST', 'http://localhost:11434'),
    
    'models' => [
        'cobit' => env('OLLAMA_COBIT_MODEL', 'cobit-auditeur'),
        'general' => env('OLLAMA_GENERAL_MODEL', 'llama2'),
    ],
    
    'timeouts' => [
        'connection' => env('OLLAMA_CONNECTION_TIMEOUT', 3),
        'request' => env('OLLAMA_REQUEST_TIMEOUT', 15),
    ],
    
    'analysis' => [
        'max_content_length' => env('OLLAMA_MAX_CONTENT_LENGTH', 2000),
        'max_tokens' => env('OLLAMA_MAX_TOKENS', 800),
        'temperature' => env('OLLAMA_TEMPERATURE', 0.2),
        'top_p' => env('OLLAMA_TOP_P', 0.8),
    ],
    
    'fallback' => [
        'enabled' => env('OLLAMA_FALLBACK_ENABLED', true),
        'confidence_threshold' => env('OLLAMA_CONFIDENCE_THRESHOLD', 0.7),
    ],
    
    'cobit_specifics' => [
        'design_factors' => [
            'DF1' => 'Enterprise Strategy',
            'DF2' => 'Enterprise Goals', 
            'DF3' => 'Risk Profile',
            'DF4' => 'I&T-Related Issues',
            'DF5' => 'Threat Landscape',
            'DF6' => 'Compliance Requirements',
            'DF7' => 'Role of IT',
            'DF8' => 'Sourcing Model',
            'DF9' => 'IT Implementation Methods',
            'DF10' => 'Enterprise Size'
        ],
        
        'company_sizes' => [
            'petite' => 'Petite entreprise (< 100 employés)',
            'moyenne' => 'Moyenne entreprise (100-500 employés)',
            'grande' => 'Grande entreprise (500-5000 employés)',
            'tres_grande' => 'Très grande entreprise (> 5000 employés)'
        ],
        
        'sectors' => [
            'financier' => ['banque', 'finance', 'assurance'],
            'santé' => ['santé', 'médical', 'hôpital'],
            'éducation' => ['éducation', 'université', 'école'],
            'industriel' => ['industrie', 'manufacture', 'production'],
            'services' => ['service', 'conseil', 'consulting'],
            'public' => ['public', 'gouvernement', 'administration'],
            'retail' => ['commerce', 'retail', 'vente'],
            'technologie' => ['tech', 'software', 'it']
        ]
    ]
];
