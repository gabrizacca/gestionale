<?php
session_start();

$host = "localhost";
$db   = "Gestionale";
$user = "root";
$pass = "";
$charset = 'utf8mb4';

// Configurazione DSN (Data Source Name) per PDO
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

// Controllo se i dati sono stati inviati
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    
    $usernameInput = $_POST['username'];
    $passwordInput = $_POST['password'];
    $passwordHash  = hash('sha256', $passwordInput);

    // SQL corretta con segnaposti e i due punti mancanti nel tuo codice
    $sql = "SELECT id_dipendente FROM dipendenti WHERE username = ? AND pswd = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->BindParam( 1 , $usernameInput, PDO::PARAM_STR);
    $stmt->BindParam( 2 , $passwordHash, PDO::PARAM_STR);


    // Esecuzione sicura (Prepared Statement)
    $stmt->execute();

    // Recupero il risultato
    $user = $stmt->fetch();

    if ($user) {
        header( "Location .\Dashboard.php" );
        exit;
    } else {
        echo "Credenziali errate.";
    }
}
?>