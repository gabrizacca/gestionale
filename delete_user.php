<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Controllo se l'utente è loggato e è amministratore
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['message'] = "Accesso negato. Solo gli amministratori possono creare nuovi utenti.";
    $_SESSION['message_type'] = 'error';
    header('Location: dashboard.php');
    exit;
}
$host    = "localhost";
$db      = "Gestionale";
$user    = "root";
$pass    = "";
$charset = 'utf8mb4';

// Configurazione DSN per PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Gestione eliminazione utente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_username'])) {
    $username = trim($_POST['username']);

    if (empty($username)) {
        die("Username richiesto.");
    }

    // Verifica se l'utente esiste
    $checkSql = "SELECT ID_Dipendente, Nome, Cognome, Email FROM dipendenti WHERE Username = :username";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute(['username' => $username]);
    $userToDelete = $checkStmt->fetch();

    if (!$userToDelete) {
        die("Utente non trovato.");
    }

    // Non permettere di eliminare se stesso
    if ($userToDelete['ID_Dipendente'] == $_SESSION['user_id']) {
        die("Non puoi eliminare te stesso.");
    }

    // Elimina l'utente
    $deleteSql = "DELETE FROM dipendenti WHERE Username = :username";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute(['username' => $username]);

    // Invia email all'amministratore (l'utente corrente)
    $adminEmail = $_SESSION['email'];
    $subject = "Eliminazione Utente";
    $message = "L'utente {$userToDelete['Nome']} {$userToDelete['Cognome']} (Username: {$username}, Email: {$userToDelete['Email']}) è stato eliminato dal sistema.";
    $headers = "From: noreply@gestionale.com\r\n";

    mail($adminEmail, $subject, $message, $headers);

    echo "Utente eliminato con successo. Email inviata all'amministratore.";
    // header("Location: manage_users.php"); // Commentato poiché non necessario
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elimina Utente</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>
    <h1>Elimina Utente</h1>
    <form method="post">
        <label for="username">Username dell'utente da eliminare:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit" name="delete_username" onclick="return confirm('Sei sicuro di voler eliminare questo utente?');">Elimina</button>
    </form>
    <a href="dashboard.php">Torna alla Dashboard</a>
</body>
</html>