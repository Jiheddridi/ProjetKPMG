<?php
/**
 * Fichier de vérification du déploiement COBIT sur InfinityFree
 * Placez ce fichier dans le dossier public/ et accédez-y via:
 * https://cobite2019platforme47.infinityfreeapp.com/deploy-check.php
 */

// Désactiver l'affichage des erreurs pour la production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Informations sur le serveur
$server_info = [
    'PHP Version' => phpversion(),
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'Server Name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
    'HTTP Host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
    'Remote Addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    'Server Protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
    'Request Time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()),
    'Max Execution Time' => ini_get('max_execution_time') . ' seconds',
    'Upload Max Filesize' => ini_get('upload_max_filesize'),
    'Post Max Size' => ini_get('post_max_size'),
    'Memory Limit' => ini_get('memory_limit'),
];

// Extensions PHP requises pour Laravel
$required_extensions = [
    'BCMath', 'Ctype', 'Fileinfo', 'JSON', 'Mbstring', 
    'OpenSSL', 'PDO', 'Tokenizer', 'XML', 'cURL',
    'GD', 'MySQL', 'Zip'
];

// Vérification des extensions
$extensions_status = [];
foreach ($required_extensions as $ext) {
    $extensions_status[$ext] = extension_loaded(strtolower($ext)) ? 'Installed' : 'Missing';
}

// Vérification des dossiers et permissions
$root_path = realpath(__DIR__ . '/..');
$directories_to_check = [
    'storage/app' => '0755',
    'storage/framework' => '0755',
    'storage/framework/cache' => '0755',
    'storage/framework/sessions' => '0755',
    'storage/framework/views' => '0755',
    'storage/logs' => '0755',
    'bootstrap/cache' => '0755',
    '.env' => '0644',
];

$directory_status = [];
foreach ($directories_to_check as $dir => $required_perms) {
    $full_path = $root_path . '/' . $dir;
    $exists = file_exists($full_path);
    $perms = $exists ? substr(sprintf('%o', fileperms($full_path)), -4) : 'N/A';
    $writable = $exists ? is_writable($full_path) : false;
    
    $directory_status[$dir] = [
        'exists' => $exists ? 'Yes' : 'No',
        'permissions' => $perms,
        'writable' => $writable ? 'Yes' : 'No',
        'status' => $exists && $writable ? 'OK' : 'Problem',
    ];
}

// Vérification de la base de données
$db_status = 'Not Checked';
$db_message = '';

// Essayer de lire le fichier .env pour les informations de base de données
$env_file = $root_path . '/.env';
$db_config = [];

if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    preg_match('/DB_CONNECTION=(.*)/', $env_content, $matches);
    $db_config['connection'] = $matches[1] ?? 'Unknown';
    
    preg_match('/DB_HOST=(.*)/', $env_content, $matches);
    $db_config['host'] = $matches[1] ?? 'Unknown';
    
    preg_match('/DB_PORT=(.*)/', $env_content, $matches);
    $db_config['port'] = $matches[1] ?? 'Unknown';
    
    preg_match('/DB_DATABASE=(.*)/', $env_content, $matches);
    $db_config['database'] = $matches[1] ?? 'Unknown';
    
    preg_match('/DB_USERNAME=(.*)/', $env_content, $matches);
    $db_config['username'] = $matches[1] ?? 'Unknown';
    
    // Ne pas afficher le mot de passe complet pour des raisons de sécurité
    preg_match('/DB_PASSWORD=(.*)/', $env_content, $matches);
    $password = $matches[1] ?? '';
    $db_config['password'] = !empty($password) ? '******' . substr($password, -2) : 'Not Set';
    
    // Tester la connexion à la base de données
    try {
        $dsn = "{$db_config['connection']}:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']}";
        $pdo = new PDO($dsn, $db_config['username'], $matches[1] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db_status = 'Connected';
        $db_message = 'Successfully connected to database';
    } catch (PDOException $e) {
        $db_status = 'Failed';
        $db_message = 'Connection failed: ' . $e->getMessage();
    }
} else {
    $db_status = 'Failed';
    $db_message = '.env file not found';
}

// Vérification des routes Laravel
$routes_status = 'Not Checked';
$routes_message = '';

// Vérifier si le fichier routes/web.php existe
$routes_file = $root_path . '/routes/web.php';
if (file_exists($routes_file)) {
    $routes_content = file_get_contents($routes_file);
    $routes_status = 'Found';
    
    // Compter le nombre de routes définies (approximatif)
    $route_count = substr_count($routes_content, 'Route::');
    $routes_message = "Found approximately {$route_count} routes defined";
} else {
    $routes_status = 'Failed';
    $routes_message = 'routes/web.php file not found';
}

// Fonction pour générer une classe CSS basée sur le statut
function getStatusClass($status) {
    if ($status === 'OK' || $status === 'Installed' || $status === 'Connected' || $status === 'Found' || $status === 'Yes') {
        return 'success';
    } elseif ($status === 'Problem' || $status === 'Missing' || $status === 'Failed' || $status === 'No') {
        return 'danger';
    } else {
        return 'warning';
    }
}

// Générer le rapport HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBIT Deployment Check</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        h1 {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: #3498db;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            font-size: 18px;
        }
        .card-body {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        .warning {
            color: #f39c12;
            font-weight: bold;
        }
        .danger {
            color: #e74c3c;
            font-weight: bold;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .summary-item {
            flex: 1;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            margin: 0 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .summary-item h3 {
            margin-top: 0;
        }
        .timestamp {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 14px;
        }
        .delete-warning {
            background-color: #ffecb3;
            border-left: 4px solid #ffa000;
            padding: 15px;
            margin-top: 30px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>COBIT Deployment Check</h1>
    
    <div class="summary">
        <div class="summary-item">
            <h3>PHP</h3>
            <p class="<?php echo getStatusClass(version_compare(phpversion(), '8.0.0', '>=') ? 'OK' : 'Problem'); ?>">
                <?php echo phpversion(); ?>
            </p>
        </div>
        <div class="summary-item">
            <h3>Database</h3>
            <p class="<?php echo getStatusClass($db_status); ?>">
                <?php echo $db_status; ?>
            </p>
        </div>
        <div class="summary-item">
            <h3>Routes</h3>
            <p class="<?php echo getStatusClass($routes_status); ?>">
                <?php echo $routes_status; ?>
            </p>
        </div>
        <div class="summary-item">
            <h3>Storage</h3>
            <p class="<?php echo getStatusClass($directory_status['storage/logs']['status']); ?>">
                <?php echo $directory_status['storage/logs']['status']; ?>
            </p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Server Information</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                </tr>
                <?php foreach ($server_info as $key => $value): ?>
                <tr>
                    <td><?php echo htmlspecialchars($key); ?></td>
                    <td><?php echo htmlspecialchars($value); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">PHP Extensions</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Extension</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($extensions_status as $ext => $status): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ext); ?></td>
                    <td class="<?php echo getStatusClass($status); ?>"><?php echo htmlspecialchars($status); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Directory Permissions</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Directory</th>
                    <th>Exists</th>
                    <th>Permissions</th>
                    <th>Writable</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($directory_status as $dir => $status): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dir); ?></td>
                    <td class="<?php echo getStatusClass($status['exists']); ?>"><?php echo htmlspecialchars($status['exists']); ?></td>
                    <td><?php echo htmlspecialchars($status['permissions']); ?></td>
                    <td class="<?php echo getStatusClass($status['writable']); ?>"><?php echo htmlspecialchars($status['writable']); ?></td>
                    <td class="<?php echo getStatusClass($status['status']); ?>"><?php echo htmlspecialchars($status['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Database Configuration</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                </tr>
                <?php foreach ($db_config as $key => $value): ?>
                <tr>
                    <td><?php echo htmlspecialchars($key); ?></td>
                    <td><?php echo htmlspecialchars($value); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Connection Status</td>
                    <td class="<?php echo getStatusClass($db_status); ?>"><?php echo htmlspecialchars($db_status); ?></td>
                </tr>
                <tr>
                    <td>Message</td>
                    <td><?php echo htmlspecialchars($db_message); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Routes Configuration</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Routes Status</td>
                    <td class="<?php echo getStatusClass($routes_status); ?>"><?php echo htmlspecialchars($routes_status); ?></td>
                </tr>
                <tr>
                    <td>Details</td>
                    <td><?php echo htmlspecialchars($routes_message); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="delete-warning">
        <strong>⚠️ Security Warning:</strong> For security reasons, delete this file (deploy-check.php) after verifying your deployment.
    </div>
    
    <div class="timestamp">
        Report generated on <?php echo date('Y-m-d H:i:s'); ?>
    </div>
</body>
</html>
