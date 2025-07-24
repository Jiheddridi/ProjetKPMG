from flask import Flask, request, render_template, jsonify, send_from_directory
import os
import logging
from datetime import datetime
from rapport_generator import traiter_document
from email_utils import envoyer_email
from validation import (
    valider_fichier, nettoyer_nom_fichier, gerer_erreur_traitement,
    creer_rapport_erreur, ValidationError
)

# Configuration du logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)

# Configuration
UPLOAD_FOLDER = 'uploads'
REPORTS_FOLDER = 'reports'
EMAIL_DESTINATAIRE_DEFAUT = 'jihed.dridi@esprit.tn', 'mpa42272@gmail.com'

TAILLE_MAX_FICHIER = 16 * 1024 * 1024  # 16MB

# Créer les dossiers nécessaires
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(REPORTS_FOLDER, exist_ok=True)

# Configuration Flask
app.config['MAX_CONTENT_LENGTH'] = TAILLE_MAX_FICHIER

# Fonction fichier_autorise supprimée - remplacée par la validation complète

@app.route('/')
def formulaire():
    """Page d'accueil avec le formulaire d'upload."""
    return render_template('index.html')

@app.route('/upload', methods=['POST'])
def upload():
    """Traite l'upload du fichier et génère le rapport avec validation complète."""
    fichier_temp = None

    try:
        # Vérification de la présence du fichier
        if 'document' not in request.files:
            return jsonify({'error': 'Aucun fichier sélectionné'}), 400

        fichier = request.files['document']

        # Vérification du nom de fichier
        if fichier.filename == '':
            return jsonify({'error': 'Aucun fichier sélectionné'}), 400

        # Nettoyage du nom de fichier
        nom_fichier_nettoye = nettoyer_nom_fichier(fichier.filename)

        # Génération d'un nom de fichier unique et sécurisé
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        nom_fichier_securise = f"{timestamp}_{nom_fichier_nettoye}"
        chemin_fichier = os.path.join(UPLOAD_FOLDER, nom_fichier_securise)

        # Sauvegarde temporaire du fichier
        fichier.save(chemin_fichier)
        fichier_temp = chemin_fichier
        logger.info(f"Fichier sauvegardé temporairement: {chemin_fichier}")

        # Validation complète du fichier
        logger.info("Validation du fichier...")
        est_valide, message_erreur = valider_fichier(chemin_fichier)

        if not est_valide:
            logger.warning(f"Fichier invalide: {message_erreur}")
            return f"""
            <html>
            <head>
                <title>Fichier invalide</title>
                <style>
                    body {{
                        font-family: Arial, sans-serif;
                        max-width: 600px;
                        margin: 50px auto;
                        padding: 20px;
                        text-align: center;
                    }}
                    .error {{
                        background: #f8d7da;
                        border: 1px solid #f5c6cb;
                        color: #721c24;
                        padding: 20px;
                        border-radius: 10px;
                        margin: 20px 0;
                    }}
                    .btn {{
                        background: #007bff;
                        color: white;
                        padding: 10px 20px;
                        text-decoration: none;
                        border-radius: 5px;
                        display: inline-block;
                        margin: 10px;
                    }}
                </style>
            </head>
            <body>
                <h1>❌ Fichier invalide</h1>
                <div class="error">
                    <h3>🚫 Validation échouée</h3>
                    <p><strong>Erreur:</strong> {message_erreur}</p>
                    <p><strong>Fichier:</strong> {fichier.filename}</p>
                </div>
                <a href="/" class="btn">🔄 Essayer avec un autre fichier</a>
            </body>
            </html>
            """, 400

        logger.info("Fichier validé avec succès")

        # Traitement du document
        logger.info("Début du traitement du document...")
        rapport = traiter_document(chemin_fichier)

        # Vérification que le rapport a été généré
        if not rapport or len(rapport.strip()) < 100:
            raise ValidationError("Le rapport généré est trop court ou vide")

        # Sauvegarde du rapport
        nom_rapport = f"rapport_{timestamp}.txt"
        chemin_rapport = os.path.join(REPORTS_FOLDER, nom_rapport)
        with open(chemin_rapport, 'w', encoding='utf-8') as f:
            f.write(rapport)
        logger.info(f"Rapport sauvegardé: {chemin_rapport}")

        # Envoi automatique à l'email par défaut
        logger.info(f"Envoi du rapport à {EMAIL_DESTINATAIRE_DEFAUT}...")
        succes_email = envoyer_email(EMAIL_DESTINATAIRE_DEFAUT, rapport, fichier.filename)

        if not succes_email:
            logger.warning("Échec de l'envoi d'email, mais rapport généré")

        # Nettoyage du fichier uploadé
        if fichier_temp and os.path.exists(fichier_temp):
            os.remove(fichier_temp)
            fichier_temp = None
            logger.info("Fichier temporaire supprimé")

        # Message de succès avec détails
        message_email = "✅ Envoyé avec succès" if succes_email else "⚠️ Rapport généré mais email non envoyé"

        return f"""
        <html>
        <head>
            <title>Analyse terminée</title>
            <meta charset="UTF-8">
            <style>
                body {{
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    max-width: 700px;
                    margin: 30px auto;
                    padding: 20px;
                    background: #f8f9fa;
                }}
                .container {{
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }}
                .success {{
                    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                    border: 1px solid #c3e6cb;
                    color: #155724;
                    padding: 25px;
                    border-radius: 12px;
                    margin: 20px 0;
                    text-align: center;
                }}
                .info {{
                    background: #e8f4fd;
                    border: 1px solid #b3d9f2;
                    color: #2c5aa0;
                    padding: 20px;
                    border-radius: 10px;
                    margin: 20px 0;
                }}
                .warning {{
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    color: #856404;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 15px 0;
                }}
                .btn {{
                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                    color: white;
                    padding: 12px 24px;
                    text-decoration: none;
                    border-radius: 8px;
                    display: inline-block;
                    margin: 10px 5px;
                    font-weight: 600;
                    transition: transform 0.2s;
                }}
                .btn:hover {{
                    transform: translateY(-2px);
                }}
                .btn-secondary {{
                    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
                }}
                .stats {{
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 15px;
                    margin: 20px 0;
                }}
                .stat-item {{
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 8px;
                    text-align: center;
                    border-left: 4px solid #007bff;
                }}
                h1 {{ color: #2c3e50; text-align: center; margin-bottom: 30px; }}
                h3 {{ color: #34495e; margin-bottom: 15px; }}
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🎉 Analyse terminée avec succès !</h1>

                <div class="success">
                    <h3>📊 Rapport d'audit généré</h3>
                    <p style="font-size: 1.1em; margin: 10px 0;">
                        <strong>Fichier analysé:</strong> {fichier.filename}<br>
                        <strong>Date d'analyse:</strong> {datetime.now().strftime("%d/%m/%Y à %H:%M")}<br>
                        <strong>Référentiel:</strong> COBIT 2019
                    </p>
                </div>

                <div class="stats">
                    <div class="stat-item">
                        <strong>📧 Email</strong><br>
                        {message_email}
                    </div>
                    <div class="stat-item">
                        <strong>📍 Destinataire</strong><br>
                        {EMAIL_DESTINATAIRE_DEFAUT}
                    </div>
                    <div class="stat-item">
                        <strong>📄 Rapport</strong><br>
                        Disponible en téléchargement
                    </div>
                </div>

                <div class="info">
                    <h3>🎯 Contenu du rapport</h3>
                    <ul style="text-align: left; margin: 15px 0;">
                        <li>✅ Analyse des besoins client identifiés</li>
                        <li>✅ Évaluation de maturité COBIT 2019</li>
                        <li>✅ Recommandations stratégiques par horizon</li>
                        <li>✅ Analyse ROI et bénéfices attendus</li>
                        <li>✅ Feuille de route d'implémentation</li>
                        <li>✅ Plan d'action opérationnel détaillé</li>
                    </ul>
                </div>

                {"<div class='warning'><strong>⚠️ Attention:</strong> L'email n'a pas pu être envoyé. Le rapport est disponible en téléchargement ci-dessous.</div>" if not succes_email else ""}

                <div style="text-align: center; margin-top: 30px;">
                    <a href="/" class="btn">📄 Analyser un autre document</a>
                    <a href="/rapport/{nom_rapport}" class="btn btn-secondary">📋 Télécharger le rapport</a>
                </div>
            </div>
        </body>
        </html>
        """

    except ValidationError as e:
        # Erreur de validation spécifique
        logger.error(f"Erreur de validation: {str(e)}")
        erreur_info = gerer_erreur_traitement(e, "validation du fichier")
        rapport_erreur = creer_rapport_erreur(erreur_info)

        return f"""
        <html>
        <head>
            <title>Erreur de validation</title>
            <meta charset="UTF-8">
            <style>
                body {{
                    font-family: Arial, sans-serif;
                    max-width: 600px;
                    margin: 50px auto;
                    padding: 20px;
                    background: #f8f9fa;
                }}
                .container {{
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }}
                .error {{
                    background: #f8d7da;
                    border: 1px solid #f5c6cb;
                    color: #721c24;
                    padding: 20px;
                    border-radius: 10px;
                    margin: 20px 0;
                }}
                .btn {{
                    background: #007bff;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    display: inline-block;
                    margin: 10px;
                }}
                pre {{
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    overflow-x: auto;
                    white-space: pre-wrap;
                }}
            </style>
        </head>
        <body>
            <div class="container">
                <h1>❌ Erreur de validation</h1>
                <div class="error">
                    <pre>{rapport_erreur}</pre>
                </div>
                <div style="text-align: center;">
                    <a href="/" class="btn">🔄 Réessayer avec un autre fichier</a>
                </div>
            </div>
        </body>
        </html>
        """, 400

    except Exception as e:
        # Erreur générale
        logger.error(f"Erreur lors du traitement: {str(e)}")
        erreur_info = gerer_erreur_traitement(e, "traitement du document")
        rapport_erreur = creer_rapport_erreur(erreur_info)

        return f"""
        <html>
        <head>
            <title>Erreur de traitement</title>
            <meta charset="UTF-8">
            <style>
                body {{
                    font-family: Arial, sans-serif;
                    max-width: 600px;
                    margin: 50px auto;
                    padding: 20px;
                    background: #f8f9fa;
                }}
                .container {{
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }}
                .error {{
                    background: #f8d7da;
                    border: 1px solid #f5c6cb;
                    color: #721c24;
                    padding: 20px;
                    border-radius: 10px;
                    margin: 20px 0;
                }}
                .btn {{
                    background: #007bff;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    display: inline-block;
                    margin: 10px;
                }}
                pre {{
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    overflow-x: auto;
                    white-space: pre-wrap;
                }}
            </style>
        </head>
        <body>
            <div class="container">
                <h1>❌ Erreur lors du traitement</h1>
                <div class="error">
                    <pre>{rapport_erreur}</pre>
                </div>
                <div style="text-align: center;">
                    <a href="/" class="btn">🔄 Réessayer</a>
                </div>
            </div>
        </body>
        </html>
        """, 500

    finally:
        # Nettoyage final en cas d'erreur
        if fichier_temp and os.path.exists(fichier_temp):
            try:
                os.remove(fichier_temp)
                logger.info("Fichier temporaire nettoyé après erreur")
            except Exception as cleanup_error:
                logger.error(f"Erreur lors du nettoyage: {cleanup_error}")

@app.route('/rapport/<filename>')
def telecharger_rapport(filename):
    """Permet de télécharger un rapport généré."""
    return send_from_directory(REPORTS_FOLDER, filename)

@app.errorhandler(413)
def fichier_trop_volumineux(e):
    return jsonify({'error': 'Fichier trop volumineux. Taille maximum: 16MB'}), 413

if __name__ == '__main__':
    logger.info("Démarrage de l'agent d'audit gouvernance IT...")
    logger.info(f"Email de destination par défaut: {EMAIL_DESTINATAIRE_DEFAUT}")
    app.run(debug=True, port=5000)
