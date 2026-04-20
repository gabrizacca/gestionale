<?php
$host = "localhost";
$db   = "Gestionale";
$user = "root";
$pass = "";
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Connessione al database fallita: " . $e->getMessage());
}

$message = '';

// Esegui la logica di login solo se questo file è richiamato direttamente.
// Quando viene incluso da api.php o dashboard.php, serve solo la connessione PDO.
if (basename($_SERVER['SCRIPT_NAME']) === 'login.php') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usernameInput = trim($_POST['username'] ?? '');
        $passwordInput = $_POST['password'] ?? '';

        if ($usernameInput === '' || $passwordInput === '') {
            $message = 'Inserisci username e password.';
        } else {
            $passwordHash = hash('sha256', $passwordInput);

            $sql = "SELECT id_dipendente, Nome, Cognome, is_admin FROM dipendenti WHERE username = ? AND pswd = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usernameInput, $passwordHash]);

            $user = $stmt->fetch();

            if ($user !== false) {
                session_set_cookie_params(0, '/');
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $usernameInput;
                $_SESSION['nome'] = $user['Nome'];
                $_SESSION['cognome'] = $user['Cognome'];
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['user_id'] = $user['id_dipendente'];
                header("Location: ./dashboard.php");
                exit;
            }

            $message = 'Credenziali errate.';
        }
    }

    if ($message !== '') {
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }
}
?>