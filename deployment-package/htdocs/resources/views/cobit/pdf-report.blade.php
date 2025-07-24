<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport COBIT 2019 - {{ $evaluation->name ?? 'Évaluation' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1E40AF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1E40AF;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #1E40AF;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
        }
        
        .summary-number {
            font-size: 24px;
            font-weight: bold;
            color: #1E40AF;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .priority-high { color: #DC2626; }
        .priority-medium { color: #F59E0B; }
        .priority-low { color: #10B981; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }
        
        table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .priority-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .priority-badge.high {
            background: #FEE2E2;
            color: #DC2626;
        }
        
        .priority-badge.medium {
            background: #FEF3C7;
            color: #F59E0B;
        }
        
        .priority-badge.low {
            background: #D1FAE5;
            color: #10B981;
        }
        
        .domain-scores {
            display: table;
            width: 100%;
        }
        
        .domain-item {
            display: table-cell;
            width: 20%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .domain-name {
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 5px;
        }
        
        .domain-score {
            font-size: 18px;
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
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .recommendations {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #1E40AF;
            margin-top: 20px;
        }
        
        .recommendations h4 {
            margin-top: 0;
            color: #1E40AF;
        }
        
        .recommendations ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .recommendations li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Rapport d'Évaluation COBIT 2019</h1>
        <div class="subtitle">
            {{ $evaluation->name ?? 'Évaluation COBIT' }}<br>
            Généré le {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>

    <!-- Résumé Exécutif -->
    <div class="section">
        <div class="section-title">Résumé Exécutif</div>
        
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $finalResults['summary']['totalObjectives'] }}</div>
                <div class="summary-label">Objectifs Évalués</div>
            </div>
            <div class="summary-item">
                <div class="summary-number priority-high">{{ $finalResults['summary']['highPriority'] }}</div>
                <div class="summary-label">Priorité Haute</div>
            </div>
            <div class="summary-item">
                <div class="summary-number priority-medium">{{ $finalResults['summary']['mediumPriority'] }}</div>
                <div class="summary-label">Priorité Moyenne</div>
            </div>
            <div class="summary-item">
                <div class="summary-number priority-low">{{ $finalResults['summary']['lowPriority'] }}</div>
                <div class="summary-label">Priorité Faible</div>
            </div>
        </div>

        <p>
            Cette évaluation COBIT 2019 a analysé {{ $finalResults['summary']['totalObjectives'] }} objectifs 
            de gouvernance et de gestion IT. Les résultats montrent que 
            {{ $finalResults['summary']['highPriority'] }} objectifs nécessitent une attention prioritaire,
            {{ $finalResults['summary']['mediumPriority'] }} objectifs présentent des améliorations modérées à apporter,
            et {{ $finalResults['summary']['lowPriority'] }} objectifs sont dans des niveaux acceptables.
        </p>
    </div>

    <!-- Performance par Domaine -->
    <div class="section">
        <div class="section-title">Performance par Domaine</div>
        
        <div class="domain-scores">
            @foreach($finalResults['domainAverages']['labels'] as $index => $domain)
            <div class="domain-item">
                <div class="domain-name">{{ $domain }}</div>
                <div class="domain-score">{{ number_format($finalResults['domainAverages']['avgData'][$index], 1) }}/5</div>
            </div>
            @endforeach
        </div>
        
        <p style="margin-top: 20px;">
            <strong>Analyse des domaines :</strong><br>
            @foreach($finalResults['domainAverages']['labels'] as $index => $domain)
                @php
                    $score = $finalResults['domainAverages']['avgData'][$index];
                    $baseline = $finalResults['domainAverages']['baselineData'][$index];
                    $gap = $score - $baseline;
                @endphp
                <strong>{{ $domain }}</strong>: Score {{ number_format($score, 1) }}/5 
                ({{ $gap >= 0 ? '+' : '' }}{{ number_format($gap, 1) }} vs baseline){{ !$loop->last ? ', ' : '.' }}
            @endforeach
        </p>
    </div>

    <!-- Objectifs Prioritaires -->
    <div class="section page-break">
        <div class="section-title">Objectifs Prioritaires</div>
        
        <h4>Priorité Haute (Action Immédiate Requise)</h4>
        <table>
            <thead>
                <tr>
                    <th>Objectif</th>
                    <th>Score Actuel</th>
                    <th>Baseline</th>
                    <th>Écart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($finalResults['objectives'] as $obj)
                    @if($obj['priority'] === 'H')
                    <tr>
                        <td><strong>{{ $obj['objective'] }}</strong></td>
                        <td>{{ $obj['score'] }}</td>
                        <td>{{ $obj['baseline'] }}</td>
                        <td class="priority-high">{{ $obj['gap'] > 0 ? '+' : '' }}{{ $obj['gap'] }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <h4>Priorité Moyenne (Amélioration Recommandée)</h4>
        <table>
            <thead>
                <tr>
                    <th>Objectif</th>
                    <th>Score Actuel</th>
                    <th>Baseline</th>
                    <th>Écart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($finalResults['objectives'] as $obj)
                    @if($obj['priority'] === 'M')
                    <tr>
                        <td><strong>{{ $obj['objective'] }}</strong></td>
                        <td>{{ $obj['score'] }}</td>
                        <td>{{ $obj['baseline'] }}</td>
                        <td class="priority-medium">{{ $obj['gap'] > 0 ? '+' : '' }}{{ $obj['gap'] }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Résultats Détaillés -->
    <div class="section page-break">
        <div class="section-title">Résultats Détaillés - Tous les Objectifs</div>
        
        <table>
            <thead>
                <tr>
                    <th>Objectif</th>
                    <th>Score</th>
                    <th>Baseline</th>
                    <th>Écart</th>
                    <th>Priorité</th>
                </tr>
            </thead>
            <tbody>
                @foreach($finalResults['objectives'] as $obj)
                <tr>
                    <td><strong>{{ $obj['objective'] }}</strong></td>
                    <td>{{ $obj['score'] }}</td>
                    <td>{{ $obj['baseline'] }}</td>
                    <td class="{{ $obj['gap'] >= 0 ? 'priority-low' : 'priority-high' }}">
                        {{ $obj['gap'] > 0 ? '+' : '' }}{{ $obj['gap'] }}
                    </td>
                    <td>
                        <span class="priority-badge {{ $obj['priority'] === 'H' ? 'high' : ($obj['priority'] === 'M' ? 'medium' : 'low') }}">
                            {{ $obj['priority'] === 'H' ? 'Haute' : ($obj['priority'] === 'M' ? 'Moyenne' : 'Faible') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recommandations -->
    <div class="section">
        <div class="section-title">Recommandations</div>
        
        <div class="recommendations">
            <h4>Actions Prioritaires</h4>
            <ul>
                @if($finalResults['summary']['highPriority'] > 0)
                <li>Concentrer les efforts sur les {{ $finalResults['summary']['highPriority'] }} objectifs à priorité haute</li>
                <li>Établir un plan d'action détaillé avec des échéances précises</li>
                <li>Allouer les ressources nécessaires pour combler les écarts critiques</li>
                @endif
                
                @if($finalResults['summary']['mediumPriority'] > 0)
                <li>Planifier l'amélioration des {{ $finalResults['summary']['mediumPriority'] }} objectifs à priorité moyenne</li>
                <li>Intégrer ces améliorations dans la roadmap IT à moyen terme</li>
                @endif
                
                <li>Maintenir le niveau des objectifs performants</li>
                <li>Mettre en place un système de monitoring continu</li>
                <li>Réviser l'évaluation trimestriellement</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        COBIT 2019 Design Toolkit - Rapport généré automatiquement le {{ now()->format('d/m/Y à H:i') }}
        <br>Page <span class="pagenum"></span>
    </div>
</body>
</html>
