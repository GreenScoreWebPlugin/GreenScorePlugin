<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Path to error log file
define("ERROR_LOG_FILE", __DIR__ . "/error_log.json");

// Debug log for input data
file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Input: " . file_get_contents("php://input") . "\n", FILE_APPEND);

function logError($message, $data = null) {
    $errorEntry = [
        "timestamp" => date("Y-m-d H:i:s"),
        "message" => $message,
        "data" => $data
    ];

    $existingErrors = [];
    if (file_exists(ERROR_LOG_FILE)) {
        $existingErrors = json_decode(file_get_contents(ERROR_LOG_FILE), true) ?: [];
    }
    $existingErrors[] = $errorEntry;

    file_put_contents(ERROR_LOG_FILE, json_encode($existingErrors, JSON_PRETTY_PRINT));
}

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("Le fichier .env.local est introuvable : $filePath");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse les lignes sous forme NOM_VARIABLE=valeur
        [$name, $value] = explode('=', $line, 2);

        // Supprime les espaces superflus
        $name = trim($name);
        $value = trim($value);

        // Définit la variable dans $_ENV et $_SERVER (et éventuellement getenv())
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

try {
    // Charger les variables d'environnement
    loadEnv(__DIR__ . '/.env.local');
    file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Env variables loaded\n", FILE_APPEND);

    $dbConfig = [
        'host' => getenv('DB_HOST'),
        'dbname' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS')
    ];

    $inputData = file_get_contents("php://input");
    file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Received data: " . $inputData . "\n", FILE_APPEND);

    if (empty($inputData)) {
        throw new Exception("No data received", 400);
    }

    $data = json_decode($inputData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON format: " . json_last_error_msg(), 400);
    }

    // Establish database connection
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8", 
        $dbConfig['username'], 
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Connected to database\n", FILE_APPEND);
    
    // Insert new monitored website data
    $insertData = [
        'user_id' => $data['userId'],
        'queries_quantity' => $data['totalRequests'],
        'loading_time' => $data['loadTime'],
        'data_transferred' => $data['totalTransferredSize'],
        'resources' => $data['totalResourceSize'],
        'carbon_footprint' => $data['totalEmissions'],
        'country' => $data['country'],
        'url_full' => $data['url'],
        'url_domain' => $data['domain'],
    ];

    // First query: Insert website data
    $sqlInsert = "
        INSERT INTO monitored_website 
        (url_domain, user_id, queries_quantity, data_transferred, resources, 
         loading_time, carbon_footprint, url_full, country)
        VALUES 
        (:url_domain, :user_id, :queries_quantity, :data_transferred, :resources, 
         :loading_time, :carbon_footprint, :url_full, :country)
    ";
    
    $stmt = $pdo->prepare($sqlInsert);
    $stmt->execute($insertData);
    
    file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Inserted website data\n", FILE_APPEND);

    // Second query: Update user's total carbon footprint
    $sqlUpdateUser = "
        UPDATE user 
        SET total_carbon_footprint = COALESCE(total_carbon_footprint, 0) + :new_emissions
        WHERE id = :user_id
    ";

    $updateStmt = $pdo->prepare($sqlUpdateUser);
    $updateStmt->execute([
        'new_emissions' => $data['totalEmissions'],
        'user_id' => $data['userId']
    ]);

    file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Updated user carbon footprint\n", FILE_APPEND);

    // Third query: Get the updated total
    $sqlGetTotal = "
        SELECT total_carbon_footprint 
        FROM user 
        WHERE id = :user_id
    ";

    $totalStmt = $pdo->prepare($sqlGetTotal);
    $totalStmt->execute(['user_id' => $data['userId']]);
    $updatedTotal = $totalStmt->fetchColumn();

    file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Retrieved new total: " . $updatedTotal . "\n", FILE_APPEND);

    // Success response
    http_response_code(200);
    echo json_encode([
        "success" => true, 
        "message" => "Data inserted successfully",
        "insertedId" => $pdo->lastInsertId(),
        "updatedTotalCarbonFootprint" => $updatedTotal
    ]);

} catch (Exception $e) {
    // Log detailed error information
    $errorDetails = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    file_put_contents(__DIR__ . "/debug_log.txt", date('Y-m-d H:i:s') . " - Error: " . json_encode($errorDetails) . "\n", FILE_APPEND);
    
    logError($e->getMessage(), [
        'input' => $inputData ?? null,
        'error_details' => $errorDetails
    ]);

    // Send error response
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        "error" => $e->getMessage(),
        "code" => $e->getCode() ?: 500,
        "details" => $errorDetails
    ]);
}
?>
