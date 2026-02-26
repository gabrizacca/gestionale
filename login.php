<?php
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
    $sql = "SELECT id_dipendente FROM dipendenti WHERE username = :username AND pswd = :pswd";
    $stmt = $pdo->prepare($sql);

    // Esecuzione sicura (Prepared Statement)
    $stmt->execute([
        'username' => $usernameInput,
        'pswd'     => $passwordHash // Nota: qui la virgola è necessaria tra gli elementi dell'array
    ]);

    // Recupero il risultato
    $user = $stmt->fetch();

    if ($user) {
        echo "Login effettuato! ID Dipendente: " . $user['id_dipendente'];
        // Qui puoi avviare la sessione: session_start(); $_SESSION['id'] = $user['id_dipendente'];
    } else {
        echo "Credenziali errate.";
    }
}
?>