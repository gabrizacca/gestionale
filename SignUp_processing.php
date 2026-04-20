<?php
session_start();
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
    $_SESSION['message'] = "Errore di connessione al database.";
    $_SESSION['message_type'] = 'error';
    header('Location: SignUp.php');
    exit;
}

// Controllo se l'utente è loggato e è amministratore
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['message'] = "Devi essere loggato per creare un nuovo utente.";
    $_SESSION['message_type'] = 'error';
    header('Location: index.html');
    exit;
}

$usernameSession = $_SESSION['username'];
$sql = "SELECT is_admin FROM dipendenti WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $usernameSession]);
$userData = $stmt->fetch();

if (!$userData || $userData['is_admin'] != 1) {
    $_SESSION['message'] = "Solo gli amministratori possono creare nuovi utenti.";
    $_SESSION['message_type'] = 'error';
    header('Location: dashboard.php');
    exit;
}

// Controllo se i dati del form di registrazione sono stati inviati
if (!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password']) && !empty($_POST['iban']) && !empty($_POST['stipendio']) && !empty($_POST['tipo']) && !empty($_POST['filiale_indirizzo']) && isset($_POST['is_admin'])) 
    {

    $name            = trim($_POST['firstname']);
    $cognome         = trim($_POST['lastname']);
    $usernameInput   = trim($_POST['username']);
    $email           = trim($_POST['email']);
    $passwordInput   = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $data_assunzione = new DateTime();
    $stipendio       = trim($_POST['stipendio']);
    $IBAN            = trim($_POST['iban']);
    $tipo            = trim($_POST['tipo']);
    $filialeIndirizzo = trim($_POST['filiale_indirizzo']);
    $is_admin        = trim($_POST['is_admin']);

    // Validazione lato server

    // Lunghezza username
    if (strlen($usernameInput) < 3 || !preg_match('/^[a-zA-Z0-9]+$/', $usernameInput)) {
        $_SESSION['message'] = "Username non valido: deve essere di almeno 3 caratteri e contenere solo lettere e numeri.";
        $_SESSION['message_type'] = 'error';
        header('Location: SignUp.php');
        exit;
    }

    // Validazione email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Indirizzo email non valido.";
        $_SESSION['message_type'] = 'error';
        header('Location: SignUp.php');
        exit;
    }

    // Corrispondenza password
    if ($passwordInput !== $confirmPassword) {
        $_SESSION['message'] = "Le password non coincidono.";
        $_SESSION['message_type'] = 'error';
        header('Location: SignUp.php');
        exit;
    }

    // Lunghezza minima password
    if (strlen($passwordInput) < 8) {
        $_SESSION['message'] = "La password deve essere di almeno 8 caratteri.";
        $_SESSION['message_type'] = 'error';
        header('Location: SignUp.php');
        exit;
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
        $_SESSION['message'] = "Username o email già in uso. Scegline altri.";
        $_SESSION['message_type'] = 'error';
        header('Location: SignUp.php');
        exit;
    }

    // Risolvo l'ID della filiale dalla via inserita
    $filialeSql = "SELECT id_filiale FROM filiali WHERE Indirizzo LIKE :indirizzo LIMIT 1";
    $filialeStmt = $pdo->prepare($filialeSql);
    $filialeStmt->execute(['indirizzo' => '%' . $filialeIndirizzo . '%']);
    $filialeData = $filialeStmt->fetch();

    if (!$filialeData) {
        $_SESSION['message'] = "Filiale non trovata con l'indirizzo inserito. Verifica la via e riprova.";
        $_SESSION['message_type'] = 'error';
        header('Location: SignUp.php');
        exit;
    }

    $idFiliale = $filialeData['id_filiale'];

    // Inserimento del nuovo utente
    $insertSql  = "INSERT INTO dipendenti (ID_Filiale, Nome, Cognome, Username, Email, Pswd, Data_Assunzione, Stipendio, IBAN, Tipo, Is_admin) VALUES (:id_filiale, :nome, :cognome, :username, :email, :pswd, :data_assunzione, :stipendio, :iban, :tipo, :is_admin)";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        'id_filiale'      => $idFiliale,
        'nome'            => $name,
        'cognome'         => $cognome,
        'username'        => $usernameInput,
        'email'           => $email,
        'pswd'            => $passwordHash,
        'data_assunzione' => $data_assunzione->format('Y-m-d'),
        'stipendio'       => $stipendio,
        'iban'            => $IBAN,
        'tipo'            => $tipo,
        'is_admin'        => $is_admin,
    ]);

    $_SESSION['message'] = "Registrazione completata con successo! Nuovo utente creato: " . htmlspecialchars($name) . ".";
    $_SESSION['message_type'] = 'success';
    header('Location: dashboard.php');
    exit;

} 
else {
    $_SESSION['message'] = "Compila tutti i campi richiesti.";
    $_SESSION['message_type'] = 'error';
    header('Location: SignUp.php');
    exit;
}
?>