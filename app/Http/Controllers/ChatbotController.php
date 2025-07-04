<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Contrôleur pour l'intégration du chatbot COBIT 2019
 * Fait le pont entre le frontend Laravel et l'API FastAPI du chatbot
 */
class ChatbotController extends Controller
{
    /**
     * URL de base de l'API FastAPI du chatbot
     */
    private const CHATBOT_API_URL = 'http://localhost:8001';
    
    /**
     * Timeout pour les requêtes vers l'API chatbot (en secondes)
     */
    private const REQUEST_TIMEOUT = 120;

    /**
     * Vérifier l'état de santé du chatbot
     * 
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        try {
            $response = Http::timeout(10)->get(self::CHATBOT_API_URL . '/health');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'success',
                    'chatbot_available' => true,
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'chatbot_available' => false,
                    'message' => 'Chatbot API non accessible'
                ], 503);
            }
        } catch (Exception $e) {
            Log::error('Erreur lors de la vérification du chatbot: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'chatbot_available' => false,
                'message' => 'Chatbot non disponible'
            ], 503);
        }
    }

    /**
     * Envoyer une question au chatbot et retourner la réponse
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function query(Request $request): JsonResponse
    {
        // Validation de la requête
        $request->validate([
            'question' => 'required|string|min:3|max:1000'
        ]);

        $question = trim($request->input('question'));
        
        try {
            Log::info('Question envoyée au chatbot: ' . $question);
            
            // Envoyer la requête à l'API FastAPI
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->post(self::CHATBOT_API_URL . '/query', [
                    'question' => $question
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Réponse reçue du chatbot');
                
                return response()->json([
                    'status' => 'success',
                    'question' => $question,
                    'answer' => $data['response'] ?? 'Réponse vide',
                    'timestamp' => now()->toISOString()
                ]);
            } else {
                Log::error('Erreur API chatbot: ' . $response->status() . ' - ' . $response->body());
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erreur lors de la communication avec le chatbot',
                    'details' => 'Code HTTP: ' . $response->status()
                ], 500);
            }
            
        } catch (Exception $e) {
            Log::error('Exception lors de la requête chatbot: ' . $e->getMessage());
            
            // Déterminer le type d'erreur
            $errorMessage = 'Erreur interne du serveur';
            $statusCode = 500;
            
            if (str_contains($e->getMessage(), 'timeout')) {
                $errorMessage = 'Le chatbot met trop de temps à répondre. Veuillez réessayer.';
                $statusCode = 408;
            } elseif (str_contains($e->getMessage(), 'Connection refused')) {
                $errorMessage = 'Le chatbot n\'est pas disponible. Veuillez vérifier qu\'il est démarré.';
                $statusCode = 503;
            }
            
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage,
                'details' => config('app.debug') ? $e->getMessage() : null
            ], $statusCode);
        }
    }

    /**
     * Obtenir l'historique des conversations (pour une future implémentation)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function history(Request $request): JsonResponse
    {
        // Pour l'instant, retourner un tableau vide
        // Dans une future version, on pourrait stocker l'historique en base
        return response()->json([
            'status' => 'success',
            'history' => []
        ]);
    }

    /**
     * Obtenir des suggestions de questions prédéfinies
     * 
     * @return JsonResponse
     */
    public function suggestions(): JsonResponse
    {
        $suggestions = [
            [
                'category' => 'Introduction',
                'questions' => [
                    'Qu\'est-ce que COBIT 2019 ?',
                    'Quels sont les principes de COBIT ?',
                    'Quelle est la différence entre gouvernance et gestion ?'
                ]
            ],
            [
                'category' => 'Objectifs de Gouvernance',
                'questions' => [
                    'Expliquez l\'objectif EDM01',
                    'Quels sont les 5 objectifs de gouvernance ?',
                    'Comment EDM02 assure-t-il la livraison des bénéfices ?'
                ]
            ],
            [
                'category' => 'Objectifs de Gestion',
                'questions' => [
                    'Décrivez le domaine APO',
                    'Qu\'est-ce que l\'objectif BAI01 ?',
                    'Comment fonctionne le domaine DSS ?'
                ]
            ],
            [
                'category' => 'Enablers',
                'questions' => [
                    'Quels sont les 7 enablers de COBIT ?',
                    'Expliquez le rôle des processus',
                    'Comment les enablers interagissent-ils ?'
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Obtenir des statistiques d'utilisation du chatbot
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        // Pour l'instant, retourner des statistiques fictives
        // Dans une future version, on pourrait les calculer depuis la base de données
        return response()->json([
            'status' => 'success',
            'stats' => [
                'total_questions' => 0,
                'avg_response_time' => '0s',
                'most_asked_topics' => [
                    'Introduction COBIT' => 0,
                    'Objectifs EDM' => 0,
                    'Domaines de gestion' => 0
                ]
            ]
        ]);
    }
}
