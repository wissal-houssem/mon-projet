<?php
// save_order.php - VERSION CORRIGÉE
header('Content-Type: application/json; charset=utf-8');

// Activer CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Gérer les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Connection à la base de données
require_once 'config.php';

// Créer un fichier log pour le débogage
$log_file = 'debug_log.txt';
$log_data = "=== NOUVELLE REQUÊTE ===\n";
$log_data .= "Méthode: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log_data .= "Heure: " . date('Y-m-d H:i:s') . "\n";
$log_data .= "Headers:\n";
foreach (getallheaders() as $name => $value) {
    $log_data .= "  $name: $value\n";
}

// 2. Récupérer les données
$input = file_get_contents('php://input');
$log_data .= "Données brutes (php://input):\n$input\n\n";
$log_data .= "POST data: " . print_r($_POST, true) . "\n";

// 3. Vérifier si on a des données
if (empty($input)) {
    $log_data .= "ERREUR: Aucune donnée reçue\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo json_encode([
        'success' => false, 
        'error' => 'No data received',
        'debug' => 'Input vide',
        'method' => $_SERVER['REQUEST_METHOD']
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 4. Essayer de décoder les données
$data = json_decode($input, true);
$log_data .= "Données décodées:\n" . print_r($data, true) . "\n";

// Vérifier si le JSON est valide
if (json_last_error() !== JSON_ERROR_NONE) {
    $log_data .= "ERREUR JSON: " . json_last_error_msg() . "\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid JSON: ' . json_last_error_msg(),
        'raw_input' => $input
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$data) {
    $log_data .= "ERREUR: Données JSON vides\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo json_encode([
        'success' => false, 
        'error' => 'Données JSON vides',
        'raw_input' => $input
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Extraire les données
$nom_client = isset($data['nom_client']) ? trim($data['nom_client']) : '';
$telephone = isset($data['telephone']) ? trim($data['telephone']) : '';
$adresse = isset($data['adresse']) ? trim($data['adresse']) : '';
$total = isset($data['total']) ? floatval($data['total']) : 0;
$produits = isset($data['produits']) ? $data['produits'] : [];

// Vérification des données obligatoires
if (empty($nom_client) || empty($telephone) || empty($adresse) || $total <= 0) {
    $log_data .= "ERREUR: Données incomplètes\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo json_encode([
        'success' => false, 
        'error' => 'Données incomplètes: ' . 
                   'nom=' . (empty($nom_client) ? 'vide' : 'ok') . ', ' .
                   'tel=' . (empty($telephone) ? 'vide' : 'ok') . ', ' .
                   'adresse=' . (empty($adresse) ? 'vide' : 'ok') . ', ' .
                   'total=' . $total
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 6. Convertir les produits en JSON
$produits_json = json_encode($produits, JSON_UNESCAPED_UNICODE);

// 7. Préparer la requête SQL
$sql = "INSERT INTO commandes (nom_client, telephone, adresse, produits, total) 
        VALUES (?, ?, ?, ?, ?)";

$log_data .= "SQL: $sql\n";

if (!$stmt = $conn->prepare($sql)) {
    $log_data .= "ERREUR Préparation: " . $conn->error . "\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur préparation: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 8. Binder les paramètres
$stmt->bind_param("ssssd", $nom_client, $telephone, $adresse, $produits_json, $total);

// 9. Exécuter
if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    
    $log_data .= "SUCCÈS: Commande #$order_id enregistrée\n";
    $log_data .= "Nom: $nom_client, Tél: $telephone, Total: $total DZD\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => '✅ Commande enregistrée avec succès!',
        'details' => [
            'client' => $nom_client,
            'telephone' => $telephone,
            'total' => $total,
            'produits' => count($produits)
        ]
    ], JSON_UNESCAPED_UNICODE);
} else {
    $log_data .= "ERREUR Exécution: " . $stmt->error . "\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur base de données: ' . $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

// 10. Fermer les connexions
$stmt->close();
$conn->close();
?>