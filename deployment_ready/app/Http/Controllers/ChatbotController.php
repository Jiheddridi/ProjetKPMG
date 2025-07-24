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
     * Vérifier l'état de santé du chatbot (version intégrée)
     *
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        // Chatbot intégré toujours disponible
        return response()->json([
            'status' => 'success',
            'chatbot_available' => true,
            'data' => [
                'version' => '1.0.0',
                'type' => 'integrated',
                'capabilities' => ['cobit_2019', 'governance', 'design_factors']
            ]
        ]);
    }

    /**
     * Envoyer une question au chatbot et retourner la réponse (version intégrée)
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

        Log::info('Question reçue par le chatbot intégré: ' . $question);

        // Générer une réponse basée sur COBIT 2019
        $answer = $this->generateCobitResponse($question);

        return response()->json([
            'status' => 'success',
            'question' => $question,
            'answer' => $answer,
            'timestamp' => now()->toISOString(),
            'source' => 'integrated_chatbot'
        ]);
    }

    /**
     * Générer une réponse COBIT 2019 basée sur la question
     */
    private function generateCobitResponse(string $question): string
    {
        $questionLower = strtolower($question);

        // Réponses sur les Design Factors
        if (preg_match('/design factor|df(\d+)|facteur/i', $questionLower)) {
            return $this->getDesignFactorResponse($questionLower);
        }

        // Réponses sur les objectifs de gouvernance EDM
        if (preg_match('/edm|gouvernance|objectif.*gouvernance/i', $questionLower)) {
            return $this->getGovernanceResponse($questionLower);
        }

        // Réponses sur les domaines de gestion
        if (preg_match('/apo|bai|dss|mea|gestion|management/i', $questionLower)) {
            return $this->getManagementResponse($questionLower);
        }

        // Réponses sur les enablers
        if (preg_match('/enabler|facilitateur|processus|structure|culture|éthique|information|service|personne|compétence/i', $questionLower)) {
            return $this->getEnablersResponse($questionLower);
        }

        // Réponses sur les principes COBIT
        if (preg_match('/principe|foundation|fondement/i', $questionLower)) {
            return $this->getPrinciplesResponse($questionLower);
        }

        // Réponses sur la maturité et les niveaux
        if (preg_match('/maturité|niveau|capability|capacité|performance/i', $questionLower)) {
            return $this->getMaturityResponse($questionLower);
        }

        // Réponses sur l'implémentation
        if (preg_match('/implémentation|mise en place|déploiement|implementation/i', $questionLower)) {
            return $this->getImplementationResponse($questionLower);
        }

        // Réponses sur les objectifs spécifiques
        if (preg_match('/apo\d+|bai\d+|dss\d+|mea\d+|edm\d+/i', $questionLower)) {
            return $this->getSpecificObjectiveResponse($questionLower);
        }

        // Réponses sur l'évaluation
        if (preg_match('/évaluation|comment.*évaluer|assessment/i', $questionLower)) {
            return $this->getEvaluationResponse($questionLower);
        }

        // Réponses générales sur COBIT
        if (preg_match('/cobit|qu\'est-ce|définition|framework/i', $questionLower)) {
            return $this->getGeneralCobitResponse($questionLower);
        }

        // Réponses sur les bonnes pratiques
        if (preg_match('/bonne.*pratique|best.*practice|recommandation/i', $questionLower)) {
            return $this->getBestPracticesResponse($questionLower);
        }

        // Réponse par défaut avec menu complet
        return $this->getDefaultResponse();
    }

    /**
     * Réponses sur les Design Factors
     */
    private function getDesignFactorResponse(string $question): string
    {
        if (preg_match('/df1|design factor 1|stratégie/i', $question)) {
            return "**DF1 - Enterprise Strategy** 🎯\n\n" .
                   "**Définition** : Stratégie d'entreprise et son alignement avec l'IT\n\n" .
                   "**Éléments clés** :\n" .
                   "• **Vision et mission** clairement définies\n" .
                   "• **Objectifs stratégiques** mesurables\n" .
                   "• **Planification** à court et long terme\n" .
                   "• **Alignement IT-Business** optimal\n\n" .
                   "**Impact sur COBIT** : Influence la sélection des objectifs de gouvernance et de gestion\n\n" .
                   "💡 **Conseil** : Une stratégie claire guide toutes les décisions IT !";
        }

        if (preg_match('/df2|design factor 2|objectifs/i', $question)) {
            return "**DF2 - Enterprise Goals** 📊\n\n" .
                   "**Définition** : Objectifs et métriques d'entreprise\n\n" .
                   "**Éléments clés** :\n" .
                   "• **KPI financiers** (ROI, coûts, revenus)\n" .
                   "• **KPI clients** (satisfaction, rétention)\n" .
                   "• **KPI internes** (efficacité, qualité)\n" .
                   "• **KPI apprentissage** (innovation, compétences)\n\n" .
                   "**Cascade COBIT** : Objectifs entreprise → Objectifs IT → Objectifs processus\n\n" .
                   "💡 **Conseil** : Utilisez des métriques SMART et équilibrées !";
        }

        if (preg_match('/df3|design factor 3|risque/i', $question)) {
            return "**DF3 - Risk Profile** ⚠️\n\n" .
                   "**Définition** : Profil et appétence au risque de l'organisation\n\n" .
                   "**Types de risques** :\n" .
                   "• **Risques stratégiques** (marché, concurrence)\n" .
                   "• **Risques opérationnels** (processus, systèmes)\n" .
                   "• **Risques de conformité** (réglementaire, légal)\n" .
                   "• **Risques financiers** (budget, investissement)\n\n" .
                   "**Appétence au risque** : Conservateur ↔ Agressif\n\n" .
                   "💡 **Impact** : Détermine l'intensité des contrôles et pratiques de gestion !";
        }

        if (preg_match('/df4|design factor 4|enjeux/i', $question)) {
            return "**DF4 - I&T-Related Issues** 🔧\n\n" .
                   "**Définition** : Enjeux et défis IT spécifiques à l'organisation\n\n" .
                   "**Catégories d'enjeux** :\n" .
                   "• **Technologiques** (legacy, modernisation)\n" .
                   "• **Organisationnels** (compétences, culture)\n" .
                   "• **Réglementaires** (conformité, audit)\n" .
                   "• **Budgétaires** (contraintes, optimisation)\n\n" .
                   "**Exemples** : Transformation digitale, cybersécurité, cloud, RGPD\n\n" .
                   "💡 **Usage** : Priorise les objectifs COBIT selon vos défis actuels !";
        }

        if (preg_match('/df5|design factor 5|menace/i', $question)) {
            return "**DF5 - Threat Landscape** 🛡️\n\n" .
                   "**Définition** : Paysage des menaces et environnement de sécurité\n\n" .
                   "**Types de menaces** :\n" .
                   "• **Cyberattaques** (malware, phishing, ransomware)\n" .
                   "• **Menaces internes** (erreurs, malveillance)\n" .
                   "• **Risques physiques** (catastrophes, pannes)\n" .
                   "• **Menaces émergentes** (IA, IoT, cloud)\n\n" .
                   "**Niveau de menace** : Faible → Modéré → Élevé → Critique\n\n" .
                   "💡 **Impact** : Influence les pratiques de sécurité et de continuité !";
        }

        return "**Les 10 Design Factors COBIT 2019** 🎯\n\n" .
               "🎯 **DF1** - Enterprise Strategy (Stratégie)\n" .
               "📊 **DF2** - Enterprise Goals (Objectifs)\n" .
               "⚠️ **DF3** - Risk Profile (Profil de risque)\n" .
               "🔧 **DF4** - I&T-Related Issues (Enjeux IT)\n" .
               "🛡️ **DF5** - Threat Landscape (Menaces)\n" .
               "📋 **DF6** - Compliance Requirements (Conformité)\n" .
               "🏛️ **DF7** - Role of IT (Rôle de l'IT)\n" .
               "🤝 **DF8** - Sourcing Model (Approvisionnement)\n" .
               "⚙️ **DF9** - IT Implementation Methods (Méthodes)\n" .
               "🏢 **DF10** - Enterprise Size (Taille)\n\n" .
               "💡 **Usage** : Personnalisent l'implémentation COBIT selon votre contexte !\n\n" .
               "Demandez-moi des détails sur un Design Factor spécifique !";
    }

    /**
     * Réponses sur la gouvernance
     */
    private function getGovernanceResponse(string $question): string
    {
        return "**Gouvernance IT selon COBIT 2019** 🏛️\n\n" .
               "La gouvernance comprend **5 objectifs principaux** :\n\n" .
               "📋 **EDM01** - Assurer la définition et le maintien du cadre de gouvernance\n" .
               "💰 **EDM02** - Assurer la livraison des bénéfices\n" .
               "⚠️ **EDM03** - Assurer l'optimisation des risques\n" .
               "💎 **EDM04** - Assurer l'optimisation des ressources\n" .
               "👥 **EDM05** - Assurer la transparence envers les parties prenantes\n\n" .
               "💡 **Principe clé** : La gouvernance **dirige** et **supervise**, elle ne gère pas directement !";
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
     * Réponses sur les domaines de gestion
     */
    private function getManagementResponse(string $question): string
    {
        return "**Domaines de Gestion COBIT 2019** ⚙️\n\n" .
               "Les **4 domaines de gestion** sont :\n\n" .
               "📋 **APO** (Align, Plan, Organize) - 14 objectifs\n" .
               "🔨 **BAI** (Build, Acquire, Implement) - 11 objectifs\n" .
               "🚀 **DSS** (Deliver, Service, Support) - 6 objectifs\n" .
               "📊 **MEA** (Monitor, Evaluate, Assess) - 4 objectifs\n\n" .
               "💡 **Total** : 35 objectifs de gestion pour couvrir tout le cycle de vie IT !";
    }

    /**
     * Réponses générales sur COBIT
     */
    private function getGeneralCobitResponse(string $question): string
    {
        return "**COBIT 2019 - Référentiel de Gouvernance IT** 🏛️\n\n" .
               "COBIT (Control Objectives for Information and Related Technologies) est :\n\n" .
               "🎯 **Un framework** de gouvernance et gestion IT\n" .
               "📊 **40 objectifs** (5 gouvernance + 35 gestion)\n" .
               "🔧 **10 Design Factors** pour personnaliser l'implémentation\n" .
               "📈 **6 niveaux de maturité** (0 à 5)\n\n" .
               "💡 **Objectif** : Créer de la valeur optimale à partir de l'IT tout en gérant les risques !";
    }

    /**
     * Réponses sur l'évaluation
     */
    private function getEvaluationResponse(string $question): string
    {
        return "**Évaluation COBIT 2019** 📊\n\n" .
               "Le processus d'évaluation comprend :\n\n" .
               "1️⃣ **Analyse des 10 Design Factors**\n" .
               "2️⃣ **Évaluation des 40 objectifs** (échelle 1-5)\n" .
               "3️⃣ **Calcul du niveau de maturité**\n" .
               "4️⃣ **Génération de recommandations**\n\n" .
               "🤖 **Astuce** : Utilisez l'Agent IA pour analyser vos documents et pré-remplir l'évaluation !\n\n" .
               "📈 **Niveaux de maturité** :\n" .
               "• **0** - Inexistant\n" .
               "• **1** - Initial\n" .
               "• **2** - Géré\n" .
               "• **3** - Défini\n" .
               "• **4** - Quantitativement géré\n" .
               "• **5** - Optimisé";
    }

    /**
     * Réponses sur les enablers COBIT
     */
    private function getEnablersResponse(string $question): string
    {
        if (preg_match('/processus/i', $question)) {
            return "**Enabler Processus** ⚙️\n\n" .
                   "Les processus COBIT 2019 comprennent :\n\n" .
                   "🏛️ **5 processus de gouvernance** (EDM01-EDM05)\n" .
                   "📋 **14 processus APO** (Align, Plan, Organize)\n" .
                   "🔨 **11 processus BAI** (Build, Acquire, Implement)\n" .
                   "🚀 **6 processus DSS** (Deliver, Service, Support)\n" .
                   "📊 **4 processus MEA** (Monitor, Evaluate, Assess)\n\n" .
                   "💡 Chaque processus a des **objectifs**, **pratiques** et **métriques** spécifiques.";
        }

        return "**Les 7 Enablers COBIT 2019** 🔧\n\n" .
               "1️⃣ **Processus** - Activités structurées\n" .
               "2️⃣ **Structures organisationnelles** - Rôles et responsabilités\n" .
               "3️⃣ **Culture, éthique et comportement** - Valeurs et attitudes\n" .
               "4️⃣ **Information** - Données et connaissances\n" .
               "5️⃣ **Services, infrastructure et applications** - Technologies\n" .
               "6️⃣ **Personnes, compétences et aptitudes** - Ressources humaines\n" .
               "7️⃣ **Principes, politiques et procédures** - Orientations\n\n" .
               "💡 **Interaction** : Tous les enablers travaillent ensemble pour créer de la valeur !";
    }

    /**
     * Réponses sur les principes COBIT
     */
    private function getPrinciplesResponse(string $question): string
    {
        return "**Les 6 Principes COBIT 2019** 🎯\n\n" .
               "1️⃣ **Fournir de la valeur aux parties prenantes**\n" .
               "   • Créer de la valeur optimale\n" .
               "   • Équilibrer bénéfices, risques et ressources\n\n" .
               "2️⃣ **Approche holistique**\n" .
               "   • Vision globale de l'entreprise\n" .
               "   • Intégration de tous les enablers\n\n" .
               "3️⃣ **Système de gouvernance dynamique**\n" .
               "   • Adaptation au contexte\n" .
               "   • Évolution continue\n\n" .
               "4️⃣ **Gouvernance distincte de la gestion**\n" .
               "   • Gouvernance : dirige et supervise\n" .
               "   • Gestion : planifie et exécute\n\n" .
               "5️⃣ **Adapté aux besoins de l'entreprise**\n" .
               "   • Personnalisation via Design Factors\n" .
               "   • Contexte spécifique\n\n" .
               "6️⃣ **Système de gouvernance de bout en bout**\n" .
               "   • Couverture complète\n" .
               "   • Intégration avec autres frameworks";
    }

    /**
     * Réponses sur la maturité
     */
    private function getMaturityResponse(string $question): string
    {
        return "**Modèle de Maturité COBIT 2019** 📈\n\n" .
               "**6 Niveaux de Maturité** :\n\n" .
               "**0️⃣ Inexistant** - Aucun processus identifiable\n" .
               "**1️⃣ Initial** - Processus ad hoc et désorganisé\n" .
               "**2️⃣ Géré** - Processus suivi et mesuré\n" .
               "**3️⃣ Défini** - Processus documenté et standardisé\n" .
               "**4️⃣ Quantitativement géré** - Processus mesuré et contrôlé\n" .
               "**5️⃣ Optimisé** - Processus en amélioration continue\n\n" .
               "**Évaluation** :\n" .
               "• **Capability** : Maturité d'un processus spécifique\n" .
               "• **Maturity** : Maturité globale d'un domaine\n\n" .
               "💡 **Objectif** : Progression graduelle vers l'excellence !";
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
                'category' => 'COBIT Fondamentaux',
                'questions' => [
                    'Qu\'est-ce que COBIT 2019 ?',
                    'Quels sont les 6 principes COBIT ?',
                    'Expliquez les 7 enablers',
                    'Différence gouvernance vs gestion ?'
                ]
            ],
            [
                'category' => 'Design Factors',
                'questions' => [
                    'Quels sont les 10 Design Factors ?',
                    'Expliquez le Design Factor 1',
                    'Comment utiliser les Design Factors ?',
                    'DF3 - Profil de risque'
                ]
            ],
            [
                'category' => 'Objectifs de Gouvernance',
                'questions' => [
                    'Quels sont les 5 objectifs EDM ?',
                    'Expliquez EDM01',
                    'EDM02 - Livraison des bénéfices',
                    'EDM03 - Optimisation des risques'
                ]
            ],
            [
                'category' => 'Domaines de Gestion',
                'questions' => [
                    'Expliquez le domaine APO',
                    'Qu\'est-ce que BAI ?',
                    'Domaine DSS en détail',
                    'MEA - Monitor Evaluate Assess'
                ]
            ],
            [
                'category' => 'Évaluation et Maturité',
                'questions' => [
                    'Comment évaluer ma maturité COBIT ?',
                    'Quels sont les 6 niveaux de maturité ?',
                    'Différence capability vs maturity ?',
                    'Comment utiliser l\'Agent IA ?'
                ]
            ],
            [
                'category' => 'Implémentation',
                'questions' => [
                    'Comment implémenter COBIT 2019 ?',
                    'Quelles sont les bonnes pratiques ?',
                    'Par où commencer ?',
                    'Comment mesurer le succès ?'
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Réponses sur l'implémentation COBIT
     */
    private function getImplementationResponse(string $question): string
    {
        return "**Implémentation COBIT 2019** 🚀\n\n" .
               "**7 Étapes d'implémentation** :\n\n" .
               "1️⃣ **Comprendre l'entreprise et le contexte**\n" .
               "2️⃣ **Déterminer les objectifs de gouvernance**\n" .
               "3️⃣ **Déterminer la portée du système de gouvernance**\n" .
               "4️⃣ **Planifier le programme**\n" .
               "5️⃣ **Concevoir le système de gouvernance**\n" .
               "6️⃣ **Implémenter le système de gouvernance**\n" .
               "7️⃣ **Opérer et améliorer le système**\n\n" .
               "💡 **Facteurs clés de succès** :\n" .
               "• Support de la direction\n" .
               "• Communication claire\n" .
               "• Formation des équipes\n" .
               "• Approche progressive\n" .
               "• Mesure des résultats";
    }

    /**
     * Réponses sur les objectifs spécifiques
     */
    private function getSpecificObjectiveResponse(string $question): string
    {
        // EDM - Objectifs de gouvernance
        if (preg_match('/edm01/i', $question)) {
            return "**EDM01 - Assurer la définition et le maintien du cadre de gouvernance** 🏛️\n\n" .
                   "**Objectif** : Fournir un cadre de gouvernance cohérent et intégré\n\n" .
                   "**Pratiques clés** :\n" .
                   "• Évaluer le système de gouvernance\n" .
                   "• Orienter la définition du cadre\n" .
                   "• Surveiller l'efficacité du système\n\n" .
                   "**Résultats attendus** :\n" .
                   "• Cadre de gouvernance défini\n" .
                   "• Rôles et responsabilités clairs\n" .
                   "• Politiques et procédures établies";
        }

        if (preg_match('/edm02/i', $question)) {
            return "**EDM02 - Assurer la livraison des bénéfices** 💰\n\n" .
                   "**Objectif** : Optimiser la contribution de l'IT à la création de valeur\n\n" .
                   "**Pratiques clés** :\n" .
                   "• Évaluer l'optimisation de la valeur\n" .
                   "• Orienter l'optimisation de la valeur\n" .
                   "• Surveiller l'optimisation de la valeur\n\n" .
                   "**Métriques** :\n" .
                   "• ROI des investissements IT\n" .
                   "• Réalisation des bénéfices\n" .
                   "• Satisfaction des parties prenantes";
        }

        // APO - Exemples
        if (preg_match('/apo01/i', $question)) {
            return "**APO01 - Gérer le cadre de gestion IT** 📋\n\n" .
                   "**Objectif** : Maintenir et améliorer le cadre de gestion IT\n\n" .
                   "**Pratiques** :\n" .
                   "• Définir le cadre de gestion IT\n" .
                   "• Définir les structures organisationnelles\n" .
                   "• Établir les rôles et responsabilités\n" .
                   "• Maintenir les enablers\n" .
                   "• Améliorer continuellement";
        }

        return "**Objectifs COBIT 2019** 🎯\n\n" .
               "**Gouvernance (EDM)** : 5 objectifs\n" .
               "• EDM01 à EDM05\n\n" .
               "**Gestion** : 35 objectifs\n" .
               "• **APO** : APO01 à APO14 (14 objectifs)\n" .
               "• **BAI** : BAI01 à BAI11 (11 objectifs)\n" .
               "• **DSS** : DSS01 à DSS06 (6 objectifs)\n" .
               "• **MEA** : MEA01 à MEA04 (4 objectifs)\n\n" .
               "Demandez-moi des détails sur un objectif spécifique !";
    }

    /**
     * Réponses sur les bonnes pratiques
     */
    private function getBestPracticesResponse(string $question): string
    {
        return "**Bonnes Pratiques COBIT 2019** ✨\n\n" .
               "**Gouvernance** :\n" .
               "• Séparer gouvernance et gestion\n" .
               "• Impliquer le conseil d'administration\n" .
               "• Définir l'appétence au risque\n" .
               "• Mesurer la création de valeur\n\n" .
               "**Gestion** :\n" .
               "• Aligner IT et business\n" .
               "• Gérer les risques de manière proactive\n" .
               "• Optimiser les ressources\n" .
               "• Améliorer continuellement\n\n" .
               "**Implémentation** :\n" .
               "• Commencer petit et grandir\n" .
               "• Former les équipes\n" .
               "• Communiquer régulièrement\n" .
               "• Mesurer et ajuster\n\n" .
               "💡 **Clé du succès** : Adaptation au contexte de votre organisation !";
    }

    /**
     * Réponse par défaut avec menu complet
     */
    private function getDefaultResponse(): string
    {
        return "🤖 **Assistant Expert COBIT 2019** - Je peux vous aider avec :\n\n" .
               "🎯 **Design Factors** (DF1-DF10)\n" .
               "   • Stratégie, objectifs, risques, conformité...\n\n" .
               "🏛️ **Gouvernance** (EDM01-EDM05)\n" .
               "   • Cadre, bénéfices, risques, ressources, transparence\n\n" .
               "⚙️ **Gestion** (APO, BAI, DSS, MEA)\n" .
               "   • 35 objectifs de gestion IT\n\n" .
               "🔧 **Enablers** (7 facilitateurs)\n" .
               "   • Processus, structures, culture, information...\n\n" .
               "📈 **Maturité** (Niveaux 0-5)\n" .
               "   • Évaluation et amélioration continue\n\n" .
               "🚀 **Implémentation**\n" .
               "   • Bonnes pratiques et méthodologie\n\n" .
               "💡 **Exemples de questions** :\n" .
               "• \"Expliquez le Design Factor 1\"\n" .
               "• \"Quels sont les objectifs EDM ?\"\n" .
               "• \"Comment évaluer ma maturité ?\"\n" .
               "• \"Qu'est-ce que l'objectif APO01 ?\"\n\n" .
               "Posez-moi votre question sur COBIT 2019 ! 🎯";
    }

    /**
     * Obtenir des statistiques d'utilisation du chatbot
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'stats' => [
                'total_questions' => 0,
                'avg_response_time' => '< 1s',
                'chatbot_type' => 'Expert COBIT 2019 Intégré',
                'knowledge_base' => [
                    'Design Factors' => 10,
                    'Objectifs EDM' => 5,
                    'Objectifs APO' => 14,
                    'Objectifs BAI' => 11,
                    'Objectifs DSS' => 6,
                    'Objectifs MEA' => 4,
                    'Enablers' => 7,
                    'Principes' => 6
                ]
            ]
        ]);
    }
}
