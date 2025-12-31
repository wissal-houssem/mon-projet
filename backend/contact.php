<?php
// contact.php - Backend pour le formulaire de contact

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $sujet = htmlspecialchars($_POST['sujet']);
    $message = htmlspecialchars($_POST['message']);

    // Validation simple
    if (empty($nom) || empty($email) ||  empty($message)) {
        echo "error:Veuillez remplir tous les champs obligatoires.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "error:Adresse email invalide.";
        exit;
    }

    // Préparer les données pour l'enregistrement
    $data = [
        'date' => date('Y-m-d H:i:s'),
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'sujet' => $sujet,
        'message' => $message
    ];

    // Enregistrer dans un fichier (simule une base de données)
    $file = 'contacts.txt';
    $current = file_exists($file) ? file_get_contents($file) : '';
    $current .= "=== Nouveau Contact ===\n";
    $current .= print_r($data, true) . "\n\n";
    file_put_contents($file, $current);

    // Envoyer un email (simulé)
    $to = "Boutiquetech21@gmail.com";
    $subject = "Nouveau message de $nom - $sujet";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Dans un projet réel, on utiliserait mail() ou PHPMailer
    // mail($to, $subject, $message, $headers);

    // Réponse de succès
    echo "success:Votre message a été envoyé avec succès! Nous vous répondrons dans les plus brefs délais.";
} else {
    echo "error:Méthode non autorisée.";
}
