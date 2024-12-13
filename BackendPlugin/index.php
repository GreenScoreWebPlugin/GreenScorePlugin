<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Path to error log file
define("ERROR_LOG_FILE", __DIR__ . "/error_log.json");

file_put_contents(__DIR__ . "/debug_log.txt", file_get_contents("php://input"), FILE_APPEND);


/**
 * Log errors to a JSON file
 * 
 * @param string $message Error message
 * @param mixed $data Optional additional data
 */
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

// Validate and parse input data
try {
    $inputData = file_get_contents("php://input");
    if (!$inputData) {
        throw new Exception("No data received", 400);
    }

    $data = json_decode($inputData, true);
    if (!$data) {
        throw new Exception("Invalid JSON format", 400);
    }

    // Validate required fields
    $requiredFields = ['totalRequests', 'loadTime', 'totalTransferredSize', 'totalResourceSize', 'url', 'domain'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field", 400);
        }
    }

    // Database connection parameters
    $dbConfig = [
        'host' => 'localhost',
        'dbname' => 'test',
        'username' => 'root',
        'password' => ''
    ];

    // Establish database connection with port
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8", 
        $dbConfig['username'], 
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare data for insertion
    $insertData = [
        'id_utilisateur' => 54321, // Static user ID
        'nombre_requetes' => $data['totalRequests'],
        'temps_chargement' => $data['loadTime'],
        'donnees_transferes' => $data['totalTransferredSize'],
        'ressources' => $data['totalResourceSize'],
        'empreinte_carbone' => $data['totalResourceSize'] * 0.000001, // Simplified carbon footprint calculation
        'pays' => 'France',
        'url_complet' => $data['url'],
        'url_domaine' => $data['domain']
    ];

    // Prepare and execute insert statement
    $sqlInsert = "
        INSERT INTO SitesWebSurveille 
        (url_domaine, id_utilisateur, nombre_requetes, transfer_donnees, ressources, 
         temps_chargement, empreinte_carbone, url_complet, pays)
        VALUES 
        (:url_domaine, :id_utilisateur, :nombre_requetes, :donnees_transferes, :ressources, 
         :temps_chargement, :empreinte_carbone, :url_complet, :pays)
    ";
    
    $stmt = $pdo->prepare($sqlInsert);
    $stmt->execute($insertData);

    // Success response
    http_response_code(200);
    echo json_encode([
        "success" => true, 
        "message" => "Data inserted successfully",
        "insertedId" => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    // Comprehensive error handling
    $errorCode = $e->getCode() ?: 500;
    $errorMessage = $e->getMessage();

    // Log the error
    logError($errorMessage, $inputData ?? null);

    // Send error response
    http_response_code($errorCode);
    echo json_encode([
        "error" => $errorMessage,
        "code" => $errorCode
    ]);
}
?>
