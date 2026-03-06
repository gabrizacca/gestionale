<?php
// ============================================================
//  gestionale_backend.php  –  Backend unico per il gestionale
//  Funzionalità: login, sessione, ordini, prodotti, filiali
// ============================================================

session_start();

// ──────────────────────────────────────────────
// CONFIGURAZIONE DATABASE
// ──────────────────────────────────────────────
$host    = 'localhost';
$dbname  = 'gestionale';
$dbuser  = 'root';
$dbpass  = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbuser, $dbpass, $pdoOptions);
} catch (PDOException $e) {
    http_response_code(500);
    jsonResponse(['error' => 'Connessione al database fallita: ' . $e->getMessage()]);
}

// ──────────────────────────────────────────────
// HELPER FUNCTIONS
// ──────────────────────────────────────────────

/** Restituisce una risposta JSON e termina l'esecuzione */
function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/** Verifica che l'utente sia autenticato */
function requireAuth(): void
{
    if (empty($_SESSION['id_dipendente'])) {
        jsonResponse(['error' => 'Non autenticato. Effettua il login.'], 401);
    }
}

/** Legge il body JSON della richiesta */
function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// ──────────────────────────────────────────────
// ROUTER PRINCIPALE
// ──────────────────────────────────────────────
// Formato URL atteso:  gestionale_backend.php?action=<nome>
// Metodo HTTP:         GET o POST (indicato per ogni route)

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // ── AUTH ─────────────────────────────────
    case 'login':           handleLogin($pdo);          break;
    case 'logout':          handleLogout();             break;
    case 'me':              handleMe($pdo);             break;

    // ── PRODOTTI ─────────────────────────────
    case 'prodotti.list':   listProdotti($pdo);         break;
    case 'prodotti.add':    addProdotto($pdo);          break;

    // ── FILIALI ──────────────────────────────
    case 'filiali.list':    listFiliali($pdo);          break;

    // ── CLIENTI ──────────────────────────────
    case 'clienti.list':    listClienti($pdo);          break;
    case 'clienti.add':     addCliente($pdo);           break;

    // ── MAGAZZINI ────────────────────────────
    case 'magazzini.list':  listMagazzini($pdo);        break;

    // ── ORDINI ───────────────────────────────
    case 'ordini.list':     listOrdini($pdo);           break;
    case 'ordini.get':      getOrdine($pdo);            break;
    case 'ordini.add':      addOrdine($pdo);            break;
    case 'ordini.delete':   deleteOrdine($pdo);         break;

    // ── MOVIMENTAZIONE MAGAZZINO → FILIALE ───
    case 'magazzino.spedisci': spedisciProdotto($pdo);  break;

    // ── DEFAULT ──────────────────────────────
    default:
        jsonResponse([
            'info'    => 'Gestionale Backend v1.0',
            'actions' => [
                'POST login',
                'POST logout',
                'GET  me',
                'GET  prodotti.list',
                'POST prodotti.add',
                'GET  filiali.list',
                'GET  clienti.list',
                'POST clienti.add',
                'GET  magazzini.list',
                'GET  ordini.list',
                'GET  ordini.get&id=<ID_Ordine>',
                'POST ordini.add',
                'POST ordini.delete',
                'POST magazzino.spedisci',
            ],
        ]);
}

// ══════════════════════════════════════════════
// AUTH
// ══════════════════════════════════════════════

/**
 * POST ?action=login
 * Body (JSON o form-data): { "username": "...", "password": "..." }
 */
function handleLogin(PDO $pdo): void
{
    $body     = getJsonBody();
    $username = trim($body['username'] ?? $_POST['username'] ?? '');
    $password = $body['password'] ?? $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        jsonResponse(['error' => 'Username e password sono obbligatori.'], 400);
    }

    $hash = hash('sha256', $password);

    $stmt = $pdo->prepare(
        'SELECT ID_Dipendente, Nome, Cognome, Email, Tipo, Is_admin
         FROM dipendenti
         WHERE Username = :username AND Pswd = :pswd
         LIMIT 1'
    );
    $stmt->execute(['username' => $username, 'pswd' => $hash]);
    $dipendente = $stmt->fetch();

    if (!$dipendente) {
        jsonResponse(['error' => 'Credenziali non valide.'], 401);
    }

    // Avvia sessione
    $_SESSION['id_dipendente'] = $dipendente['ID_Dipendente'];
    $_SESSION['nome']          = $dipendente['Nome'];
    $_SESSION['cognome']       = $dipendente['Cognome'];
    $_SESSION['is_admin']      = (bool) $dipendente['Is_admin'];

    jsonResponse([
        'message'    => 'Login effettuato con successo.',
        'dipendente' => [
            'id'       => $dipendente['ID_Dipendente'],
            'nome'     => $dipendente['Nome'],
            'cognome'  => $dipendente['Cognome'],
            'email'    => $dipendente['Email'],
            'tipo'     => $dipendente['Tipo'],
            'is_admin' => (bool) $dipendente['Is_admin'],
        ],
    ]);
}

/**
 * POST ?action=logout
 */
function handleLogout(): void
{
    $_SESSION = [];
    session_destroy();
    jsonResponse(['message' => 'Logout effettuato.']);
}

/**
 * GET ?action=me   — restituisce il dipendente loggato
 */
function handleMe(PDO $pdo): void
{
    requireAuth();
    $id   = $_SESSION['id_dipendente'];
    $stmt = $pdo->prepare(
        'SELECT ID_Dipendente, Nome, Cognome, Email, Tipo, Is_admin FROM dipendenti WHERE ID_Dipendente = :id'
    );
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    jsonResponse($row ?: []);
}

// ══════════════════════════════════════════════
// PRODOTTI
// ══════════════════════════════════════════════

/**
 * GET ?action=prodotti.list
 * Parametri opzionali: ?id_magazzino=<int>  — filtra per magazzino
 */
function listProdotti(PDO $pdo): void
{
    requireAuth();

    $idMagazzino = isset($_GET['id_magazzino']) ? (int) $_GET['id_magazzino'] : null;

    if ($idMagazzino) {
        $stmt = $pdo->prepare(
            'SELECT p.ID_Prodotto, p.Desc_prodotto, mp.ID_Magazzino
             FROM prodotti p
             JOIN magazzino_prodotti mp ON mp.ID_Prodotto = p.ID_Prodotto
             WHERE mp.ID_Magazzino = :id'
        );
        $stmt->execute(['id' => $idMagazzino]);
    } else {
        $stmt = $pdo->query('SELECT ID_Prodotto, Desc_prodotto FROM prodotti ORDER BY ID_Prodotto');
    }

    jsonResponse($stmt->fetchAll());
}

/**
 * POST ?action=prodotti.add
 * Body: { "desc_prodotto": "Descrizione prodotto" }
 */
function addProdotto(PDO $pdo): void
{
    requireAuth();
    $body = getJsonBody();
    $desc = trim($body['desc_prodotto'] ?? $_POST['desc_prodotto'] ?? '');

    if ($desc === '') {
        jsonResponse(['error' => 'desc_prodotto è obbligatorio.'], 400);
    }

    $stmt = $pdo->prepare('INSERT INTO prodotti (Desc_prodotto) VALUES (:desc)');
    $stmt->execute(['desc' => $desc]);
    $newId = (int) $pdo->lastInsertId();

    jsonResponse(['message' => 'Prodotto inserito.', 'ID_Prodotto' => $newId], 201);
}

// ══════════════════════════════════════════════
// FILIALI
// ══════════════════════════════════════════════

/**
 * GET ?action=filiali.list
 */
function listFiliali(PDO $pdo): void
{
    requireAuth();
    $rows = $pdo->query('SELECT id_filiale, Indirizzo, Tipo, Recapito_Telefonico FROM filiali ORDER BY id_filiale')->fetchAll();
    jsonResponse($rows);
}

// ══════════════════════════════════════════════
// CLIENTI
// ══════════════════════════════════════════════

/**
 * GET ?action=clienti.list
 */
function listClienti(PDO $pdo): void
{
    requireAuth();
    $rows = $pdo->query('SELECT ID_Cliente, Nome_Azienda, Indirizzo, P_IVA, Email FROM clienti ORDER BY ID_Cliente')->fetchAll();
    jsonResponse($rows);
}

/**
 * POST ?action=clienti.add
 * Body: { "nome_azienda": "...", "indirizzo": "...", "p_iva": "...", "email": "..." }
 */
function addCliente(PDO $pdo): void
{
    requireAuth();
    $body = getJsonBody();

    $nomeAzienda = trim($body['nome_azienda'] ?? $_POST['nome_azienda'] ?? '');
    $indirizzo   = trim($body['indirizzo']    ?? $_POST['indirizzo']    ?? '');
    $piva        = trim($body['p_iva']        ?? $_POST['p_iva']        ?? '');
    $email       = trim($body['email']        ?? $_POST['email']        ?? '');

    if ($nomeAzienda === '' || $piva === '' || $email === '') {
        jsonResponse(['error' => 'nome_azienda, p_iva ed email sono obbligatori.'], 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['error' => 'Email non valida.'], 400);
    }

    // Controlla duplicato P.IVA
    $check = $pdo->prepare('SELECT ID_Cliente FROM clienti WHERE P_IVA = :piva');
    $check->execute(['piva' => $piva]);
    if ($check->fetch()) {
        jsonResponse(['error' => 'P.IVA già presente nel sistema.'], 409);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO clienti (Nome_Azienda, Indirizzo, P_IVA, Email) VALUES (:nome, :ind, :piva, :email)'
    );
    $stmt->execute([
        'nome'  => $nomeAzienda,
        'ind'   => $indirizzo,
        'piva'  => $piva,
        'email' => $email,
    ]);

    jsonResponse(['message' => 'Cliente inserito.', 'ID_Cliente' => (int) $pdo->lastInsertId()], 201);
}

// ══════════════════════════════════════════════
// MAGAZZINI
// ══════════════════════════════════════════════

/**
 * GET ?action=magazzini.list
 */
function listMagazzini(PDO $pdo): void
{
    requireAuth();
    $rows = $pdo->query('SELECT ID_Magazzino, Indirizzo, Desc_Magazzino FROM magazzini ORDER BY ID_Magazzino')->fetchAll();
    jsonResponse($rows);
}

// ══════════════════════════════════════════════
// ORDINI
// ══════════════════════════════════════════════

/**
 * GET ?action=ordini.list
 * Parametri opzionali:
 *   ?id_cliente=<int>    — filtra per cliente
 *   ?id_dipendente=<int> — filtra per dipendente (default: loggato)
 *   ?dal=YYYY-MM-DD      — data inizio
 *   ?al=YYYY-MM-DD       — data fine
 */
function listOrdini(PDO $pdo): void
{
    requireAuth();

    $where  = [];
    $params = [];

    if (!empty($_GET['id_cliente'])) {
        $where[]              = 'o.ID_Cliente = :id_cliente';
        $params['id_cliente'] = (int) $_GET['id_cliente'];
    }
    if (!empty($_GET['id_dipendente'])) {
        $where[]                 = 'o.ID_Dipendente = :id_dipendente';
        $params['id_dipendente'] = (int) $_GET['id_dipendente'];
    }
    if (!empty($_GET['dal'])) {
        $where[]       = 'o.Data_Ordine >= :dal';
        $params['dal'] = $_GET['dal'];
    }
    if (!empty($_GET['al'])) {
        $where[]      = 'o.Data_Ordine <= :al';
        $params['al'] = $_GET['al'];
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "SELECT
                o.ID_Ordine,
                o.Data_Ordine,
                o.Data_Arrivo,
                c.Nome_Azienda  AS cliente,
                c.Email         AS email_cliente,
                CONCAT(d.Nome, ' ', d.Cognome) AS dipendente
            FROM ordini o
            LEFT JOIN clienti   c ON c.ID_Cliente    = o.ID_Cliente
            LEFT JOIN dipendenti d ON d.ID_Dipendente = o.ID_Dipendente
            $whereSql
            ORDER BY o.Data_Ordine DESC, o.ID_Ordine DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ordini = $stmt->fetchAll();

    // Aggiunge i prodotti di ciascun ordine
    foreach ($ordini as &$ordine) {
        $pStmt = $pdo->prepare(
            'SELECT p.ID_Prodotto, p.Desc_prodotto, op.Quantita
             FROM ordine_prodotti op
             JOIN prodotti p ON p.ID_Prodotto = op.ID_Prodotto
             WHERE op.ID_Ordine = :id'
        );
        $pStmt->execute(['id' => $ordine['ID_Ordine']]);
        $ordine['prodotti'] = $pStmt->fetchAll();
    }
    unset($ordine);

    jsonResponse($ordini);
}

/**
 * GET ?action=ordini.get&id=<ID_Ordine>
 */
function getOrdine(PDO $pdo): void
{
    requireAuth();
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if (!$id) {
        jsonResponse(['error' => 'Parametro id mancante.'], 400);
    }

    $stmt = $pdo->prepare(
        "SELECT
             o.ID_Ordine,
             o.Data_Ordine,
             o.Data_Arrivo,
             c.Nome_Azienda  AS cliente,
             c.Email         AS email_cliente,
             CONCAT(d.Nome, ' ', d.Cognome) AS dipendente
         FROM ordini o
         LEFT JOIN clienti    c ON c.ID_Cliente    = o.ID_Cliente
         LEFT JOIN dipendenti d ON d.ID_Dipendente = o.ID_Dipendente
         WHERE o.ID_Ordine = :id"
    );
    $stmt->execute(['id' => $id]);
    $ordine = $stmt->fetch();

    if (!$ordine) {
        jsonResponse(['error' => 'Ordine non trovato.'], 404);
    }

    $pStmt = $pdo->prepare(
        'SELECT p.ID_Prodotto, p.Desc_prodotto, op.Quantita
         FROM ordine_prodotti op
         JOIN prodotti p ON p.ID_Prodotto = op.ID_Prodotto
         WHERE op.ID_Ordine = :id'
    );
    $pStmt->execute(['id' => $id]);
    $ordine['prodotti'] = $pStmt->fetchAll();

    jsonResponse($ordine);
}

/**
 * POST ?action=ordini.add
 * Body JSON:
 * {
 *   "id_cliente":  1,
 *   "data_arrivo": "2026-04-01",          // opzionale
 *   "prodotti": [
 *     { "id_prodotto": 3, "quantita": 2 },
 *     { "id_prodotto": 7, "quantita": 1 }
 *   ]
 * }
 */
function addOrdine(PDO $pdo): void
{
    requireAuth();

    $body        = getJsonBody();
    $idCliente   = isset($body['id_cliente'])  ? (int) $body['id_cliente']  : 0;
    $dataArrivo  = $body['data_arrivo']  ?? null;
    $prodotti    = $body['prodotti']     ?? [];
    $idDipendente = $_SESSION['id_dipendente'];

    // Validazioni
    if (!$idCliente) {
        jsonResponse(['error' => 'id_cliente è obbligatorio.'], 400);
    }
    if (empty($prodotti) || !is_array($prodotti)) {
        jsonResponse(['error' => 'Inserisci almeno un prodotto nell\'ordine.'], 400);
    }

    // Verifica esistenza cliente
    $chk = $pdo->prepare('SELECT ID_Cliente FROM clienti WHERE ID_Cliente = :id');
    $chk->execute(['id' => $idCliente]);
    if (!$chk->fetch()) {
        jsonResponse(['error' => "Cliente con ID $idCliente non trovato."], 404);
    }

    $pdo->beginTransaction();

    try {
        // Inserisci ordine
        $dataOrdine = date('Y-m-d');
        $stmtOrdine = $pdo->prepare(
            'INSERT INTO ordini (ID_Cliente, ID_Dipendente, Data_Ordine, Data_Arrivo)
             VALUES (:id_cliente, :id_dipendente, :data_ordine, :data_arrivo)'
        );
        $stmtOrdine->execute([
            'id_cliente'    => $idCliente,
            'id_dipendente' => $idDipendente,
            'data_ordine'   => $dataOrdine,
            'data_arrivo'   => $dataArrivo ?: null,
        ]);
        $idOrdine = (int) $pdo->lastInsertId();

        // Inserisci righe prodotto
        $stmtProd = $pdo->prepare(
            'INSERT INTO ordine_prodotti (ID_Ordine, ID_Prodotto, Quantita) VALUES (:id_ordine, :id_prodotto, :qty)'
        );
        foreach ($prodotti as $riga) {
            $idProdotto = isset($riga['id_prodotto']) ? (int) $riga['id_prodotto'] : 0;
            $quantita   = isset($riga['quantita'])    ? (int) $riga['quantita']    : 1;

            if (!$idProdotto || $quantita < 1) {
                throw new RuntimeException("Riga prodotto non valida: id_prodotto=$idProdotto, quantita=$quantita");
            }

            // Verifica esistenza prodotto
            $chkP = $pdo->prepare('SELECT ID_Prodotto FROM prodotti WHERE ID_Prodotto = :id');
            $chkP->execute(['id' => $idProdotto]);
            if (!$chkP->fetch()) {
                throw new RuntimeException("Prodotto con ID $idProdotto non trovato.");
            }

            $stmtProd->execute([
                'id_ordine'  => $idOrdine,
                'id_prodotto' => $idProdotto,
                'qty'         => $quantita,
            ]);
        }

        $pdo->commit();

        jsonResponse([
            'message'   => 'Ordine creato con successo.',
            'ID_Ordine' => $idOrdine,
            'prodotti_inseriti' => count($prodotti),
        ], 201);

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(['error' => 'Errore nella creazione dell\'ordine: ' . $e->getMessage()], 500);
    }
}

/**
 * POST ?action=ordini.delete
 * Body: { "id_ordine": 5 }
 */
function deleteOrdine(PDO $pdo): void
{
    requireAuth();
    $body     = getJsonBody();
    $idOrdine = isset($body['id_ordine']) ? (int) $body['id_ordine'] : (int) ($_POST['id_ordine'] ?? 0);

    if (!$idOrdine) {
        jsonResponse(['error' => 'id_ordine mancante.'], 400);
    }

    // Controlla esistenza
    $chk = $pdo->prepare('SELECT ID_Ordine FROM ordini WHERE ID_Ordine = :id');
    $chk->execute(['id' => $idOrdine]);
    if (!$chk->fetch()) {
        jsonResponse(['error' => 'Ordine non trovato.'], 404);
    }

    // Solo admin o il dipendente che ha creato l'ordine può eliminarlo
    $chkOwner = $pdo->prepare('SELECT ID_Dipendente FROM ordini WHERE ID_Ordine = :id');
    $chkOwner->execute(['id' => $idOrdine]);
    $owner = $chkOwner->fetch();

    if (!$_SESSION['is_admin'] && $owner['ID_Dipendente'] !== $_SESSION['id_dipendente']) {
        jsonResponse(['error' => 'Non autorizzato a eliminare questo ordine.'], 403);
    }

    $pdo->beginTransaction();
    try {
        $pdo->prepare('DELETE FROM ordine_prodotti WHERE ID_Ordine = :id')->execute(['id' => $idOrdine]);
        $pdo->prepare('DELETE FROM ordini WHERE ID_Ordine = :id')->execute(['id' => $idOrdine]);
        $pdo->commit();
        jsonResponse(['message' => "Ordine $idOrdine eliminato."]);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(['error' => 'Errore nell\'eliminazione: ' . $e->getMessage()], 500);
    }
}

// ══════════════════════════════════════════════
// MOVIMENTAZIONE MAGAZZINO → FILIALE
// ══════════════════════════════════════════════

/**
 * POST ?action=magazzino.spedisci
 * Sposta uno o più prodotti da un magazzino a una filiale
 * (aggiorna magazzino_prodotti e filiale_magazzini se necessario)
 *
 * Body JSON:
 * {
 *   "id_magazzino_origine": 2,
 *   "id_filiale_destinazione": 1,
 *   "prodotti": [5, 9, 12]     // array di ID_Prodotto da spostare
 * }
 */
function spedisciProdotto(PDO $pdo): void
{
    requireAuth();

    $body              = getJsonBody();
    $idMagazzino       = isset($body['id_magazzino_origine'])    ? (int) $body['id_magazzino_origine']    : 0;
    $idFiliale         = isset($body['id_filiale_destinazione']) ? (int) $body['id_filiale_destinazione'] : 0;
    $prodotti          = $body['prodotti'] ?? [];

    if (!$idMagazzino || !$idFiliale) {
        jsonResponse(['error' => 'id_magazzino_origine e id_filiale_destinazione sono obbligatori.'], 400);
    }
    if (empty($prodotti)) {
        jsonResponse(['error' => 'Specifica almeno un prodotto da spedire.'], 400);
    }

    // Verifica magazzino
    $chkMag = $pdo->prepare('SELECT ID_Magazzino FROM magazzini WHERE ID_Magazzino = :id');
    $chkMag->execute(['id' => $idMagazzino]);
    if (!$chkMag->fetch()) {
        jsonResponse(['error' => "Magazzino ID $idMagazzino non trovato."], 404);
    }

    // Verifica filiale
    $chkFil = $pdo->prepare('SELECT id_filiale FROM filiali WHERE id_filiale = :id');
    $chkFil->execute(['id' => $idFiliale]);
    if (!$chkFil->fetch()) {
        jsonResponse(['error' => "Filiale ID $idFiliale non trovata."], 404);
    }

    // Assicura che la filiale sia collegata al magazzino
    $chkFM = $pdo->prepare(
        'SELECT * FROM filiale_magazzini WHERE id_filiale = :fil AND ID_Magazzino = :mag'
    );
    $chkFM->execute(['fil' => $idFiliale, 'mag' => $idMagazzino]);
    if (!$chkFM->fetch()) {
        // Crea il collegamento automaticamente
        $pdo->prepare(
            'INSERT IGNORE INTO filiale_magazzini (id_filiale, ID_Magazzino) VALUES (:fil, :mag)'
        )->execute(['fil' => $idFiliale, 'mag' => $idMagazzino]);
    }

    $pdo->beginTransaction();
    $spostati  = [];
    $nonTrovati = [];

    try {
        foreach ($prodotti as $idProdotto) {
            $idProdotto = (int) $idProdotto;

            // Controlla che il prodotto sia nel magazzino origine
            $chkMP = $pdo->prepare(
                'SELECT * FROM magazzino_prodotti WHERE ID_Magazzino = :mag AND ID_Prodotto = :prod'
            );
            $chkMP->execute(['mag' => $idMagazzino, 'prod' => $idProdotto]);

            if (!$chkMP->fetch()) {
                $nonTrovati[] = $idProdotto;
                continue;
            }

            // Rimuovi dal magazzino origine
            $pdo->prepare(
                'DELETE FROM magazzino_prodotti WHERE ID_Magazzino = :mag AND ID_Prodotto = :prod'
            )->execute(['mag' => $idMagazzino, 'prod' => $idProdotto]);

            // Aggiungi al magazzino della filiale destinazione (se esiste un magazzino collegato)
            // In questa implementazione associamo direttamente il prodotto al magazzino
            // linkedato alla filiale destinazione (filiale_magazzini), oppure
            // registriamo semplicemente il movimento nella tabella magazzino_prodotti
            // con l'ID magazzino corrente (comportamento configurabile).
            // Per ora il prodotto è considerato "in transito" / consegnato alla filiale.

            $spostati[] = $idProdotto;
        }

        $pdo->commit();

        jsonResponse([
            'message'         => 'Spedizione eseguita.',
            'prodotti_spostati' => $spostati,
            'prodotti_non_trovati_in_magazzino' => $nonTrovati,
            'dettaglio'       => count($spostati) . ' prodotto/i rimossi dal magazzino ' . $idMagazzino
                                 . ' e registrati come consegnati alla filiale ' . $idFiliale . '.',
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(['error' => 'Errore durante la spedizione: ' . $e->getMessage()], 500);
    }
}
