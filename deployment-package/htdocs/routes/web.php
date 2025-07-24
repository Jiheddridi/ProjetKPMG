<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobitController;
use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Routes d'authentification
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Route principale - Redirection vers la page d'accueil COBIT
Route::get('/', function () {
    return redirect('/cobit/home');
});

// Routes pour l'évaluation COBIT (protégées par authentification)
Route::prefix('cobit')->name('cobit.')->middleware(App\Http\Middleware\AuthMiddleware::class)->group(function () {
    // Page d'accueil KPMG
    Route::get('/home', [CobitController::class, 'home'])->name('home');
    Route::get('/', [CobitController::class, 'home'])->name('index');

    // Pages des Design Factors
    Route::get('/df/{number}', [CobitController::class, 'dfDetail'])->name('df.detail');
    // CRUD Évaluations
    Route::post('/evaluation/create', [CobitController::class, 'createEvaluation'])->name('evaluation.create');
    Route::get('/evaluation/{id}/df/{df}', [CobitController::class, 'showEvaluationDF'])->name('evaluation.df');
    Route::post('/evaluation/save-df', [CobitController::class, 'saveDFData'])->name('evaluation.save-df');
    Route::get('/evaluation/{id}/canvas', [CobitController::class, 'showCanvas'])->name('evaluation.canvas');
    Route::delete('/evaluation/{id}', [CobitController::class, 'deleteEvaluation'])->name('evaluation.delete');

    // Route pour l'analyse IA des documents
    Route::post('/ai-analyze', [CobitController::class, 'aiAnalyzeDocuments'])->name('ai.analyze');

    // Nouvelle évaluation (ancienne méthode - à supprimer plus tard)
    Route::post('/nouvelle-evaluation', [CobitController::class, 'nouvelleEvaluation'])->name('nouvelle.evaluation');



    // Historique des évaluations
    Route::get('/historique', [CobitController::class, 'historique'])->name('historique');
    Route::get('/historique/canvas/{id}', [CobitController::class, 'viewCanvasFromHistory'])->name('historique.canvas');

    // Routes pour la comparaison d'évaluations
    Route::get('/comparison', [CobitController::class, 'comparisonPage'])->name('comparison');
    Route::post('/comparison/analyze', [CobitController::class, 'analyzeComparison'])->name('comparison.analyze');

    // Anciennes routes (pour compatibilité)
    Route::get('/evaluation', [CobitController::class, 'evaluationTest'])->name('evaluation');
    Route::post('/save-evaluation', [CobitController::class, 'saveEvaluation'])->name('save-evaluation');
    Route::get('/results', [CobitController::class, 'results'])->name('results');
    Route::get('/export-pdf', [CobitController::class, 'exportPdf'])->name('export-pdf');
    Route::post('/import', [CobitController::class, 'import'])->name('import');
    Route::delete('/reset', [CobitController::class, 'reset'])->name('reset');

    // API Routes pour les données en temps réel
    Route::get('/api/calculate/{dfNumber}', [CobitController::class, 'calculateResults'])->name('api.calculate');
    Route::post('/api/update-inputs', [CobitController::class, 'updateInputs'])->name('api.update-inputs');
    Route::post('/api/save-df', [CobitController::class, 'saveDFData'])->name('api.save-df');

    // Routes d'export
    Route::post('/export/pdf', [App\Http\Controllers\ExportController::class, 'exportPDF'])->name('export.pdf');
    Route::post('/export/excel', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('export.excel');

    // Routes pour le chatbot COBIT (avec authentification)
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/health', [ChatbotController::class, 'health'])->name('health');
        Route::post('/query', [ChatbotController::class, 'query'])->name('query');
        Route::get('/suggestions', [ChatbotController::class, 'suggestions'])->name('suggestions');
        Route::get('/history', [ChatbotController::class, 'history'])->name('history');
        Route::get('/stats', [ChatbotController::class, 'stats'])->name('stats');
    });
});

// Routes publiques pour le chatbot (tests et démonstration)
Route::prefix('api/chatbot')->name('api.chatbot.')->group(function () {
    Route::get('/health', [ChatbotController::class, 'health'])->name('health');
    Route::post('/query', [ChatbotController::class, 'query'])->name('query')->withoutMiddleware(['csrf']);
    Route::get('/suggestions', [ChatbotController::class, 'suggestions'])->name('suggestions');
    Route::get('/stats', [ChatbotController::class, 'stats'])->name('stats');
});
