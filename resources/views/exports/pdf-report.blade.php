<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport COBIT 2019 - {{ $user['name'] ?? 'Utilisateur' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #00338D;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .kpmg-logo {
            background-color: #00338D;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            display: inline-block;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #00338D;
            margin: 10px 0;
        }
        
        .report-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #00338D;
            border-bottom: 2px solid #E0E7FF;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metric-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 1px solid #E0E7FF;
            background-color: #F8FAFC;
        }
        
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #00338D;
        }
        
        .metric-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        .df-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .df-table th,
        .df-table td {
            border: 1px solid #E0E7FF;
            padding: 8px;
            text-align: left;
        }
        
        .df-table th {
            background-color: #00338D;
            color: white;
            font-weight: bold;
        }
        
        .df-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        
        .status-completed {
            color: #16A34A;
            font-weight: bold;
        }
        
        .status-pending {
            color: #EA580C;
            font-weight: bold;
        }
        
        .ai-section {
            background-color: #F0F4FF;
            border: 2px solid #C7D2FE;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .ai-title {
            font-size: 14px;
            font-weight: bold;
            color: #4338CA;
            margin-bottom: 10px;
        }
        
        .recommendation-item {
            margin-bottom: 8px;
            padding-left: 15px;
        }
        
        .risk-high {
            color: #DC2626;
            font-weight: bold;
        }
        
        .risk-medium {
            color: #EA580C;
            font-weight: bold;
        }
        
        .risk-low {
            color: #16A34A;
            font-weight: bold;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #E0E7FF;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .action-plan {
            margin-bottom: 15px;
        }
        
        .action-plan h4 {
            color: #00338D;
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .action-plan ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .action-plan li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="kpmg-logo">KPMG</div>
        <div class="report-title">Rapport d'Évaluation COBIT 2019</div>
        <div class="report-subtitle">Analyse des Design Factors et Recommandations IA</div>
        <div class="report-subtitle">Généré le {{ $generatedAt }}</div>
        <div class="report-subtitle">Utilisateur: {{ $user['name'] ?? 'N/A' }} ({{ $user['role'] ?? 'N/A' }})</div>
    </div>

    <!-- Résumé Exécutif -->
    <div class="section">
        <div class="section-title">📊 Résumé Exécutif</div>
        
        <div class="metrics-grid">
            <div class="metric-item">
                <div class="metric-value">{{ number_format($globalMetrics['globalScore'], 1) }}</div>
                <div class="metric-label">Score Global</div>
            </div>
            <div class="metric-item">
                <div class="metric-value">{{ $globalMetrics['maturityLevel'] }}</div>
                <div class="metric-label">Niveau de Maturité</div>
            </div>
            <div class="metric-item">
                <div class="metric-value">{{ $globalMetrics['completedDFs'] }}/10</div>
                <div class="metric-label">DF Complétés</div>
            </div>
            <div class="metric-item">
                <div class="metric-value">{{ number_format($globalMetrics['averageCompletion'], 0) }}%</div>
                <div class="metric-label">Complétude Moyenne</div>
            </div>
        </div>

        <div class="ai-section">
            <div class="ai-title">🤖 Analyse IA - Statut Global</div>
            <p><strong>Niveau de Risque:</strong> 
                <span class="risk-{{ strtolower($aiAnalysis['overallRisk']) }}">
                    {{ $aiAnalysis['overallRisk'] }}
                </span>
            </p>
            <p><strong>Statut de Conformité:</strong> {{ $aiAnalysis['complianceStatus'] }}</p>
        </div>
    </div>

    <!-- Détail des Design Factors -->
    <div class="section">
        <div class="section-title">🎯 Détail des Design Factors</div>
        
        <table class="df-table">
            <thead>
                <tr>
                    <th>Design Factor</th>
                    <th>Score Moyen</th>
                    <th>Complétude</th>
                    <th>Statut</th>
                    <th>Dernière MAJ</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 10; $i++)
                    @php
                        $dfKey = "DF{$i}";
                        $dfData = $evaluationData[$dfKey] ?? [];
                        $score = $dfData['avgScore'] ?? 0;
                        $completion = $dfData['completion'] ?? 0;
                        $completed = $dfData['completed'] ?? false;
                        $updated = $dfData['updated_at'] ?? 'N/A';
                    @endphp
                    <tr>
                        <td><strong>{{ $dfKey }}</strong></td>
                        <td>{{ number_format($score, 2) }}</td>
                        <td>{{ number_format($completion, 0) }}%</td>
                        <td class="{{ $completed ? 'status-completed' : 'status-pending' }}">
                            {{ $completed ? 'Complété' : 'En cours' }}
                        </td>
                        <td>{{ $updated !== 'N/A' ? \Carbon\Carbon::parse($updated)->format('d/m/Y H:i') : 'N/A' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Recommandations IA -->
    <div class="section">
        <div class="section-title">🧠 Recommandations IA</div>
        
        <div class="ai-section">
            <div class="ai-title">🎯 Recommandations Clés</div>
            @foreach($aiAnalysis['keyRecommendations'] as $recommendation)
                <div class="recommendation-item">{{ $recommendation }}</div>
            @endforeach
        </div>
    </div>

    <!-- Plan d'Action -->
    <div class="section">
        <div class="section-title">📋 Plan d'Action Recommandé</div>
        
        <div class="action-plan">
            <h4>🚀 Court Terme (0-3 mois)</h4>
            <ul>
                @foreach($aiAnalysis['actionPlan']['short_term'] ?? [] as $action)
                    <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>

        <div class="action-plan">
            <h4>📈 Moyen Terme (3-12 mois)</h4>
            <ul>
                @foreach($aiAnalysis['actionPlan']['medium_term'] ?? [] as $action)
                    <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>

        <div class="action-plan">
            <h4>🎯 Long Terme (12+ mois)</h4>
            <ul>
                @foreach($aiAnalysis['actionPlan']['long_term'] ?? [] as $action)
                    <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Méthodologie -->
    <div class="section">
        <div class="section-title">📚 Méthodologie COBIT 2019</div>
        <p><strong>Framework:</strong> COBIT 2019 (Control Objectives for Information and Related Technologies)</p>
        <p><strong>Calculs:</strong> Formules officielles ISACA avec matrices de mapping</p>
        <p><strong>Formules utilisées:</strong></p>
        <ul>
            <li>Score: =PRODUITMAT(DFmap!B2:E41;'DF'!D7:D10)</li>
            <li>Baseline: =PRODUITMAT(DFmap!B2:E41;'DF'!E7:E10)</li>
            <li>Relative Importance: =SIERREUR(ARRONDI.AU.MULTIPLE($E$14*100*B22/C22;5)-100;0)</li>
        </ul>
        <p><strong>IA:</strong> Analyse automatique basée sur les écarts et patterns de performance</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 KPMG Advisory Services - Rapport COBIT 2019 généré automatiquement</p>
        <p>Ce rapport est confidentiel et destiné exclusivement à l'usage interne de l'organisation</p>
    </div>
</body>
</html>
