<?php
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

// Controllo se i dati del form di registrazione sono stati inviati
if (!empty($_POST['nome']) && !empty($_POST['cognome']) && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empt y($_POST['confirm_password']) && !empty($_POST[''])) {

    $name            = trim($_POST['nome']);
    $cogome          = trim($_POST['cognome']);
    $usernameInput   = trim($_POST['username']);
    $email           = trim($_POST['email']);
    $passwordInput   = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $data_assunzione = DateTime();
    $stipendio       = trim($_POST['stipendio']);
    $IBAN            = trim($_POST['IBAN']);
    $tipo            = trim($_POST['tipo']);
    $is_admin        = trim($_POST['is_admin']);

    // Validazione lato server

    // Lunghezza username
    if (strlen($usernameInput) < 3 || !preg_match('/^[a-zA-Z0-9]+$/', $usernameInput)) {
        die("Username non valido: deve essere di almeno 3 caratteri e contenere solo lettere e numeri.");
    }

    // Validazione email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Indirizzo email non valido.");
    }

    // Corrispondenza password
    if ($passwordInput !== $confirmPassword) {
        die("Le password non coincidono.");
    }

    // Lunghezza minima password
    if (strlen($passwordInput) < 8) {
        die("La password deve essere di almeno 8 caratteri.");
    }

    // Hash della password
    $passwordHash = hash('sha256', $passwordInput);

    // Verifica se username o email già esistono
    $checkSql  = "SELECT id_dipendente FROM dipendenti WHERE username = :username OR email = :email";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([
        'username' => $usernameInput,
        'email'    => $email,
    ]);

    if ($checkStmt->fetch()) {
        die("Username o email già in uso. Scegline altri.");
    }

    // Inserimento del nuovo utente
    $insertSql  = "INSERT INTO dipendenti (nome_completo, username, email, pswd) VALUES (:nome_completo, :username, :email, :pswd)";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        'nome'          => $name,
        'cognome'       => $cognome,
        'Data_Assunzione' => $now
        'username'      => $usernameInput,
        'email'         => $email,
        'pswd'          => $passwordHash,
        'stipendio'     => $stipendio,

    ]);

    echo "Registrazione completata con successo! Benvenuto, " . htmlspecialchars($name) . ".";
    // Puoi reindirizzare al login: header('Location: login.php');

} else {
    echo "Compila tutti i campi richiesti.";
}
?>
