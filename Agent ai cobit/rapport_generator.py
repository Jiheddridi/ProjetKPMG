# from langchain.llms import Ollama  # Commenté pour éviter les erreurs d'import
try:
    import fitz  # PyMuPDF
    FITZ_AVAILABLE = True
except ImportError:
    FITZ_AVAILABLE = False
    print("⚠️ PyMuPDF non disponible - fonctionnalité PDF limitée")

import pandas as pd
import re
import json
from datetime import datetime
from typing import Dict, List, Tuple
import nltk
from collections import Counter
from cobit_config import OBJECTIFS_COBIT_2019, NIVEAUX_MATURITE, DOMAINES_FOCUS, INDICATEURS_PERFORMANCE

# Télécharger les ressources NLTK nécessaires (à faire une seule fois)
try:
    nltk.data.find('tokenizers/punkt')
except LookupError:
    nltk.download('punkt')

try:
    nltk.data.find('corpora/stopwords')
except LookupError:
    nltk.download('stopwords')

from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize, sent_tokenize

llm = Ollama(model="cobit-auditeur")  # Commenté temporairement

class MockLLM:
    """Mock LLM pour les tests sans Ollama/Langchain."""
    def __call__(self, prompt):
        return self.generer_rapport_mock(prompt)

    def generer_rapport_mock(self, prompt):
        """Génère un rapport mock pour les tests."""
        return """
🏢 **RAPPORT D'AUDIT GOUVERNANCE IT - COBIT 2019**
📅 Date du rapport: Généré automatiquement

## 1. 📋 SYNTHÈSE EXÉCUTIVE
- Score de maturité global estimé: 2.5/5 (Niveau "Répétable mais intuitif")
- Principaux enjeux: Amélioration des processus de gouvernance IT nécessaire
- Priorités d'action: Mise en place de processus structurés

## 2. 🎯 ANALYSE DES BESOINS CLIENT
- Besoins métier identifiés: Amélioration de la gouvernance IT
- Défis technologiques: Standardisation des processus
- Objectifs stratégiques: Alignement IT/Métier

## 3. 🏗️ OBJECTIFS COBIT 2019 RECOMMANDÉS
### Domaine EDM (Évaluer, Diriger, Surveiller)
- EDM01: Assurer la définition du cadre de gouvernance (Priorité: Élevée)
- EDM02: Assurer la réalisation des bénéfices (Priorité: Moyenne)

### Domaine APO (Aligner, Planifier, Organiser)
- APO01: Gérer le cadre de gestion IT (Priorité: Élevée)
- APO02: Gérer la stratégie (Priorité: Critique)

## 4. 📊 ÉVALUATION DE MATURITÉ
- Niveau actuel moyen: 2.5/5
- Niveau cible recommandé: 4/5
- Écart à combler: 1.5 points

## 5. 💡 RECOMMANDATIONS STRATÉGIQUES
### Court terme (0-6 mois)
- Définir les rôles et responsabilités IT
- Mettre en place des politiques de base

### Moyen terme (6-18 mois)
- Implémenter les processus COBIT prioritaires
- Former les équipes

### Long terme (18+ mois)
- Optimiser et automatiser les processus
- Amélioration continue

## 6. 📈 ANALYSE ROI
- Investissement estimé: 150,000€
- Bénéfices annuels attendus: 225,000€
- ROI estimé: 150%
- Temps de retour: 0.7 ans

## 7. 🛣️ FEUILLE DE ROUTE
### Phase 1: Fondations (0-6 mois)
- Audit des processus existants
- Définition du cadre de gouvernance

### Phase 2: Implémentation (6-18 mois)
- Déploiement des processus COBIT
- Formation et accompagnement

### Phase 3: Optimisation (18+ mois)
- Mesure de performance
- Amélioration continue

## 8. 📋 CONCLUSION
Ce rapport identifie les axes d'amélioration prioritaires pour renforcer la gouvernance IT selon le référentiel COBIT 2019. La mise en œuvre des recommandations permettra d'améliorer significativement la maturité des processus IT.
        """

llm = MockLLM()

# Dictionnaires de mots-clés spécifiques à la gouvernance IT
MOTS_CLES_GOUVERNANCE = {
    "processus_cobit": [
        "APO", "BAI", "DSS", "MEA", "EDM",
        "gouvernance", "gestion", "stratégie", "architecture",
        "innovation", "portefeuille", "budget", "ressources humaines",
        "relations", "accords de service", "fournisseurs", "qualité",
        "risques", "sécurité", "données", "actifs", "configuration",
        "changement", "acceptation", "déploiement", "disponibilité",
        "capacité", "continuité", "sécurité informatique", "contrôles",
        "incidents", "problèmes", "demandes", "performance", "conformité",
        "assurance", "audit interne", "gouvernance d'entreprise"
    ],
    "domaines_focus": [
        "alignement stratégique", "création de valeur", "gestion des risques",
        "gestion des ressources", "mesure de performance", "optimisation",
        "transformation digitale", "innovation", "agilité", "résilience"
    ],
    "objectifs_gouvernance": [
        "EDM01", "EDM02", "EDM03", "EDM04", "EDM05",
        "APO01", "APO02", "APO03", "APO04", "APO05", "APO06", "APO07",
        "APO08", "APO09", "APO10", "APO11", "APO12", "APO13", "APO14",
        "BAI01", "BAI02", "BAI03", "BAI04", "BAI05", "BAI06", "BAI07",
        "BAI08", "BAI09", "BAI10", "BAI11", "DSS01", "DSS02", "DSS03",
        "DSS04", "DSS05", "DSS06", "MEA01", "MEA02", "MEA03", "MEA04"
    ],
    "niveaux_maturite": [
        "inexistant", "initial", "répétable", "défini", "géré", "optimisé",
        "niveau 0", "niveau 1", "niveau 2", "niveau 3", "niveau 4", "niveau 5"
    ],
    "indicateurs_performance": [
        "KPI", "KGI", "métriques", "tableau de bord", "reporting",
        "indicateurs clés", "mesures", "benchmarking", "ROI", "TCO"
    ]
}

def extraire_mots_cles_gouvernance(texte: str) -> Dict[str, List[str]]:
    """Extrait les mots-clés spécifiques à la gouvernance IT du texte."""
    texte_lower = texte.lower()
    mots_cles_trouves = {}

    for categorie, mots_cles in MOTS_CLES_GOUVERNANCE.items():
        mots_trouves = []
        for mot_cle in mots_cles:
            if mot_cle.lower() in texte_lower:
                # Compter les occurrences
                occurrences = len(re.findall(r'\b' + re.escape(mot_cle.lower()) + r'\b', texte_lower))
                if occurrences > 0:
                    mots_trouves.append(f"{mot_cle} ({occurrences}x)")
        mots_cles_trouves[categorie] = mots_trouves

    return mots_cles_trouves

def analyser_besoins_client(texte: str) -> Dict[str, any]:
    """Analyse les besoins spécifiques du client dans le document."""
    # Tokenisation et nettoyage
    try:
        stop_words = set(stopwords.words('french') + stopwords.words('english'))
    except:
        stop_words = set()

    phrases = sent_tokenize(texte)
    mots = word_tokenize(texte.lower())
    mots_filtres = [mot for mot in mots if mot.isalnum() and mot not in stop_words and len(mot) > 3]

    # Analyse de fréquence
    freq_mots = Counter(mots_filtres)
    mots_frequents = freq_mots.most_common(20)

    # Recherche de phrases contenant des besoins
    phrases_besoins = []
    mots_cles_besoins = ['besoin', 'exigence', 'requis', 'nécessaire', 'objectif', 'but', 'améliorer', 'optimiser','amélioration','risque','incident','iso27001']

    for phrase in phrases:
        if any(mot in phrase.lower() for mot in mots_cles_besoins):
            phrases_besoins.append(phrase.strip())

    return {
        'mots_frequents': mots_frequents,
        'phrases_besoins': phrases_besoins[:10],  # Top 10 phrases
        'nombre_phrases': len(phrases),
        'nombre_mots': len(mots_filtres)
    }

def lire_pdf(path: str) -> Tuple[str, Dict]:
    """Lit un PDF et extrait le texte avec métadonnées."""
    if not FITZ_AVAILABLE:
        return "Erreur: PyMuPDF non disponible pour lire les fichiers PDF", {
            'erreur': 'PyMuPDF non installé',
            'type_fichier': 'PDF'
        }

    try:
        text = ""
        metadata = {}

        with fitz.open(path) as doc:
            metadata = {
                'nombre_pages': doc.page_count,
                'titre': doc.metadata.get('title', 'Non spécifié'),
                'auteur': doc.metadata.get('author', 'Non spécifié'),
                'creation_date': doc.metadata.get('creationDate', 'Non spécifié'),
                'type_fichier': 'PDF'
            }

            for page_num, page in enumerate(doc):
                text += f"\n--- Page {page_num + 1} ---\n"
                text += page.get_text()

        return text, metadata
    except Exception as e:
        return f"Erreur lors de la lecture du PDF: {str(e)}", {
            'erreur': str(e),
            'type_fichier': 'PDF'
        }

def lire_excel(path: str) -> Tuple[str, Dict]:
    """Lit un fichier Excel et extrait les données avec métadonnées."""
    try:
        # Lire toutes les feuilles
        excel_file = pd.ExcelFile(path)
        sheets_data = {}
        text_complet = ""

        for sheet_name in excel_file.sheet_names:
            df = pd.read_excel(path, sheet_name=sheet_name)
            sheets_data[sheet_name] = {
                'lignes': len(df),
                'colonnes': len(df.columns),
                'colonnes_noms': list(df.columns)
            }

            text_complet += f"\n--- Feuille: {sheet_name} ---\n"
            text_complet += df.to_string(index=False)
            text_complet += "\n"

        metadata = {
            'nombre_feuilles': len(excel_file.sheet_names),
            'feuilles': sheets_data,
            'noms_feuilles': excel_file.sheet_names
        }

        return text_complet, metadata

    except Exception as e:
        return f"Erreur lors de la lecture du fichier Excel: {str(e)}", {}

def analyser_maturite_cobit(mots_cles: Dict, contenu: str) -> Dict:
    """Analyse le niveau de maturité basé sur les mots-clés trouvés."""
    analyse_maturite = {}

    # Analyse par domaine COBIT
    for domaine_code, domaine_info in OBJECTIFS_COBIT_2019.items():
        score_domaine = 0
        objectifs_applicables = []

        for obj_code, obj_info in domaine_info["objectifs"].items():
            # Vérifier si l'objectif est mentionné ou pertinent
            score_objectif = 0

            # Recherche de mots-clés spécifiques à l'objectif
            nom_lower = obj_info["nom"].lower()
            desc_lower = obj_info["description"].lower()

            for mot_cle in nom_lower.split() + desc_lower.split():
                if len(mot_cle) > 4 and mot_cle in contenu.lower():
                    score_objectif += 1

            # Évaluation basée sur les mots-clés de gouvernance trouvés
            for categorie, mots_trouves in mots_cles.items():
                if mots_trouves:
                    score_objectif += len(mots_trouves) * 0.1

            if score_objectif > 0:
                niveau_estime = min(5, max(1, int(score_objectif / 2)))
                objectifs_applicables.append({
                    "code": obj_code,
                    "nom": obj_info["nom"],
                    "niveau_actuel": niveau_estime,
                    "niveau_cible": min(5, niveau_estime + 2),
                    "priorite": "Élevée" if niveau_estime < 3 else "Moyenne"
                })
                score_domaine += niveau_estime

        if objectifs_applicables:
            analyse_maturite[domaine_code] = {
                "nom": domaine_info["nom"],
                "score_moyen": round(score_domaine / len(objectifs_applicables), 1),
                "objectifs": objectifs_applicables
            }

    return analyse_maturite

def generer_recommandations_strategiques(analyse_maturite: Dict, mots_cles: Dict) -> Dict:
    """Génère des recommandations stratégiques basées sur l'analyse."""
    recommandations = {
        "court_terme": [],
        "moyen_terme": [],
        "long_terme": []
    }

    # Analyser les domaines de focus
    for focus_code, focus_info in DOMAINES_FOCUS.items():
        score_focus = 0
        objectifs_lies = focus_info["objectifs_lies"]

        for domaine_code, domaine_data in analyse_maturite.items():
            for objectif in domaine_data["objectifs"]:
                if objectif["code"] in objectifs_lies:
                    score_focus += objectif["niveau_actuel"]

        if score_focus > 0:
            score_moyen = score_focus / len(objectifs_lies)

            if score_moyen < 2:
                recommandations["court_terme"].append({
                    "domaine": focus_info["nom"],
                    "action": f"Mise en place urgente des processus de base pour {focus_info['description'].lower()}",
                    "priorite": "Critique",
                    "duree": "0-6 mois"
                })
            elif score_moyen < 3:
                recommandations["moyen_terme"].append({
                    "domaine": focus_info["nom"],
                    "action": f"Amélioration et standardisation des processus de {focus_info['description'].lower()}",
                    "priorite": "Élevée",
                    "duree": "6-18 mois"
                })
            else:
                recommandations["long_terme"].append({
                    "domaine": focus_info["nom"],
                    "action": f"Optimisation et automatisation des processus de {focus_info['description'].lower()}",
                    "priorite": "Moyenne",
                    "duree": "18+ mois"
                })

    return recommandations

def calculer_roi_estime(analyse_maturite: Dict) -> Dict:
    """Calcule le ROI estimé des améliorations."""
    roi_data = {
        "investissement_estime": 0,
        "benefices_annuels": 0,
        "roi_pourcentage": 0,
        "temps_retour": 0
    }

    # Calcul basé sur le nombre d'objectifs à améliorer
    total_objectifs = sum(len(domaine["objectifs"]) for domaine in analyse_maturite.values())

    if total_objectifs > 0:
        # Estimation basée sur des moyennes du marché
        roi_data["investissement_estime"] = total_objectifs * 50000  # 50k€ par objectif
        roi_data["benefices_annuels"] = total_objectifs * 75000     # 75k€ de bénéfices par an

        if roi_data["investissement_estime"] > 0:
            roi_data["roi_pourcentage"] = round(
                (roi_data["benefices_annuels"] / roi_data["investissement_estime"]) * 100, 1
            )
            roi_data["temps_retour"] = round(
                roi_data["investissement_estime"] / roi_data["benefices_annuels"], 1
            )

    return roi_data

def generer_rapport_detaille(contenu: str, metadata: Dict, mots_cles: Dict, analyse_besoins: Dict) -> str:
    """Génère un rapport détaillé d'audit de gouvernance IT."""

    date_rapport = datetime.now().strftime("%d/%m/%Y à %H:%M")

    # Analyses avancées
    analyse_maturite = analyser_maturite_cobit(mots_cles, contenu)
    recommandations = generer_recommandations_strategiques(analyse_maturite, mots_cles)
    roi_data = calculer_roi_estime(analyse_maturite)

    # Construction du prompt enrichi avec analyses
    prompt = f"""
Tu es un expert en gouvernance IT et audit COBIT 2019. Utilise les analyses suivantes pour produire un rapport d'audit professionnel et détaillé.

=== MÉTADONNÉES DU DOCUMENT ===
{json.dumps(metadata, indent=2, ensure_ascii=False)}

=== ANALYSE DE MATURITÉ COBIT 2019 ===
{json.dumps(analyse_maturite, indent=2, ensure_ascii=False)}

=== RECOMMANDATIONS STRATÉGIQUES ===
{json.dumps(recommandations, indent=2, ensure_ascii=False)}

=== ANALYSE ROI ===
{json.dumps(roi_data, indent=2, ensure_ascii=False)}

=== MOTS-CLÉS GOUVERNANCE IT IDENTIFIÉS ===
{json.dumps(mots_cles, indent=2, ensure_ascii=False)}

=== ANALYSE DES BESOINS CLIENT ===
Phrases clés identifiées: {analyse_besoins.get('phrases_besoins', [])}
Mots les plus fréquents: {[mot for mot, _ in analyse_besoins.get('mots_frequents', [])[:10]]}

=== EXTRAIT DU CONTENU ANALYSÉ ===
{contenu[:3000]}...

=== INSTRUCTIONS POUR LE RAPPORT ===
Utilise TOUTES les analyses fournies ci-dessus pour produire un rapport d'audit de gouvernance IT professionnel et détaillé selon cette structure :

🏢 **RAPPORT D'AUDIT GOUVERNANCE IT - COBIT 2019**
📅 Date du rapport: {date_rapport}
🤖 Généré par: Agent IA d'Audit Gouvernance IT

## 1. 📋 SYNTHÈSE EXÉCUTIVE
- Résumé des principaux enjeux identifiés dans le document
- Score de maturité global calculé (utilise les données d'analyse de maturité)
- Top 3 des priorités d'action basées sur les recommandations
- Impact business estimé

## 2. 🎯 ANALYSE DES BESOINS CLIENT
- Besoins métier identifiés (utilise les phrases clés extraites)
- Défis technologiques mentionnés
- Objectifs stratégiques exprimés
- Mots-clés les plus fréquents et leur signification

## 3. 🏗️ OBJECTIFS COBIT 2019 APPLICABLES
Pour chaque domaine identifié dans l'analyse de maturité:
- Liste des objectifs COBIT spécifiques avec codes (EDM01, APO02, etc.)
- Niveau de priorité basé sur l'analyse (Critique/Élevé/Moyen/Faible)
- Justification détaillée pour chaque objectif
- Lien avec les besoins client identifiés

## 4. 📊 ÉVALUATION DE MATURITÉ DÉTAILLÉE
Pour chaque processus dans l'analyse de maturité:
- Niveau actuel estimé (0-5) avec justification
- Niveau cible recommandé
- Écart à combler et effort requis
- Description du niveau de maturité selon COBIT

## 5. 🔍 ANALYSE DES RISQUES ET OPPORTUNITÉS
- Risques IT identifiés dans le document
- Risques liés aux niveaux de maturité faibles
- Impact potentiel (Critique/Élevé/Moyen/Faible)
- Opportunités d'amélioration
- Mesures de mitigation recommandées

## 6. 💡 RECOMMANDATIONS STRATÉGIQUES PAR HORIZON
### Court terme (0-6 mois) - Actions Critiques
{[rec for rec in recommandations.get('court_terme', [])]}

### Moyen terme (6-18 mois) - Améliorations Structurelles
{[rec for rec in recommandations.get('moyen_terme', [])]}

### Long terme (18+ mois) - Optimisation Continue
{[rec for rec in recommandations.get('long_terme', [])]}

## 7. 📈 ANALYSE ROI ET BÉNÉFICES ATTENDUS
- Investissement estimé: {roi_data.get('investissement_estime', 0):,}€
- Bénéfices annuels attendus: {roi_data.get('benefices_annuels', 0):,}€
- ROI estimé: {roi_data.get('roi_pourcentage', 0)}%
- Temps de retour sur investissement: {roi_data.get('temps_retour', 0)} ans
- Gains en efficacité opérationnelle
- Réduction des risques quantifiée

## 8. 🛣️ FEUILLE DE ROUTE D'IMPLÉMENTATION
### Phase 1 (0-6 mois): Fondations
- Actions prioritaires identifiées
- Ressources nécessaires
- Livrables attendus

### Phase 2 (6-18 mois): Développement
- Initiatives de moyen terme
- Jalons importants
- Indicateurs de progression

### Phase 3 (18+ mois): Optimisation
- Vision long terme
- Amélioration continue
- Innovation et transformation

## 9. 📊 INDICATEURS DE PERFORMANCE (KPI/KGI)
- KGI (Indicateurs d'Objectifs) recommandés
- KPI (Indicateurs de Performance) par processus
- Tableaux de bord suggérés
- Fréquence de mesure

## 10. 📋 PLAN D'ACTION OPÉRATIONNEL
Pour chaque recommandation critique:
- Action concrète détaillée
- Responsable suggéré (rôle)
- Échéance précise
- Budget estimé
- Prérequis et dépendances
- Critères de succès

## 11. 🎯 CONCLUSION ET PROCHAINES ÉTAPES
- Synthèse des bénéfices attendus
- Facteurs clés de succès
- Risques d'implémentation
- Recommandations pour le démarrage

IMPORTANT:
- Utilise EXCLUSIVEMENT les données d'analyse fournies
- Sois précis avec les codes COBIT (EDM01, APO02, etc.)
- Justifie chaque recommandation par les éléments du document
- Fournis des chiffres concrets quand disponibles
- Adapte le niveau de détail aux enjeux identifiés
"""

    return llm(prompt)

def traiter_document(path: str) -> str:
    """Traite un document et génère un rapport d'audit complet."""
    try:
        # Déterminer le type de fichier et extraire le contenu
        if path.lower().endswith('.pdf'):
            contenu, metadata = lire_pdf(path)
            type_fichier = "PDF"
        elif path.lower().endswith(('.xlsx', '.xls')):
            contenu, metadata = lire_excel(path)
            type_fichier = "Excel"
        else:
            return "❌ Format de fichier non supporté. Formats acceptés: PDF, Excel (.xlsx, .xls)"

        # Ajouter des informations sur le type de fichier
        metadata['type_fichier'] = type_fichier
        metadata['taille_contenu'] = len(contenu)
        metadata['date_traitement'] = datetime.now().isoformat()

        # Extraire les mots-clés spécifiques à la gouvernance
        mots_cles = extraire_mots_cles_gouvernance(contenu)

        # Analyser les besoins du client
        analyse_besoins = analyser_besoins_client(contenu)

        # Générer le rapport détaillé
        rapport = generer_rapport_detaille(contenu, metadata, mots_cles, analyse_besoins)

        return rapport

    except Exception as e:
        return f"❌ Erreur lors du traitement du document: {str(e)}\n\nVeuillez vérifier que le fichier n'est pas corrompu et réessayer."
