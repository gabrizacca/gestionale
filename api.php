<?php
// Imposta il cookie sul percorso radice per renderlo leggibile da tutti i file
session_set_cookie_params(0, "/");
session_start();

require_once 'login.php';

// ... resto dei tuoi header e configurazioni

error_reporting(E_ALL);
ini_set('display_errors', '0');

header('Content-Type: application/json');

try {
    require_once 'login.php';
} catch (Exception $e) {
    die(json_encode(['success' => false, 'message' => 'Errore di connessione al database: ' . $e->getMessage()]));
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getOrdiniPersonali':
        getOrdiniPersonali();
        break;
    case 'getOrdini':
        getOrdini();
        break;
    case 'addOrdini':
        addOrdine();
        break;
    case 'updateOrdini':
        updateOrdine();
        break;
    case 'deleteOrdini':
        deleteOrdine();
        break;
    case 'getClienti':
        getClienti();
        break;
    case 'addClienti':
        addCliente();
        break;
    case 'updateClienti':
        updateCliente();
        break;
    case 'deleteClienti':
        deleteCliente();
        break;
    case 'getDipendenti':
        getDipendenti();
        break;
    case 'addDipendenti':
        addDipendente();
        break;
    case 'updateDipendenti':
        updateDipendente();
        break;
    case 'deleteDipendenti':
        deleteDipendente();
        break;
    case 'getProdotti':
        getProdotti();
        break;
    case 'addProdotti':
        addProdotto();
        break;
    case 'updateProdotti':
        updateProdotto();
        break;
    case 'deleteProdotti':
        deleteProdotto();
        break;
    case 'getMagazzini':
        getMagazzini();
        break;
    case 'addMagazzini':
        addMagazzino();
        break;
    case 'updateMagazzini':
        updateMagazzino();
        break;
    case 'deleteMagazzini':
        deleteMagazzino();
        break;
    case 'getFiliali':
        getFiliali();
        break;
    case 'addFiliali':
        addFiliale();
        break;
    case 'updateFiliali':
        updateFiliale();
        break;
    case 'deleteFiliali':
        deleteFiliale();
        break;
    case 'getDashboardStats':
        getDashboardStats();
        break;
    case 'getProdottiMagazzini':
        getProdottiMagazzini();
        break;
    case 'addProdottiMagazzini':
        addProdottoMagazzino();
        break;
    case 'updateProdottiMagazzini':
        updateProdottoMagazzino();
        break;
    case 'deleteProdottiMagazzini':
        deleteProdottoMagazzino();
        break;
    case 'getWarehousesForProduct':
        getWarehousesForProduct();
        break;
    case 'getOrderProducts':
        getOrderProducts();
        break;
    case 'addOrderProduct':
        addOrderProduct();
        break;
    case 'deleteOrderProduct':
        deleteOrderProduct();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Azione non valida']);
        break;
}

function getOrdiniPersonali(){
    global $pdo;
    try {
        $id_dipendente = $_SESSION['user_id'];

        $sql = "SELECT o.ID_Ordine as id, o.ID_Cliente as id_cliente, c.Nome_Azienda as cliente, o.Data_Ordine as data_ordine, o.Data_Arrivo as data_arrivo, 
                       CONCAT(d.Nome, ' ', d.Cognome) as dipendente
                FROM ordini o
                LEFT JOIN clienti c ON o.ID_Cliente = c.ID_Cliente
                LEFT JOIN dipendenti d ON o.ID_Dipendente = d.ID_Dipendente
                WHERE o.ID_Dipendente = ?
                ORDER BY o.Data_Ordine DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_dipendente]);
        $ordini = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $ordini]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getOrdini() {
    global $pdo;
    try {
        $id = $_GET['id'] ?? null;
        $where = $id ? "WHERE o.ID_Ordine = ?" : "";
        $sql = "SELECT o.ID_Ordine as id, o.ID_Cliente as id_cliente, o.ID_Dipendente as id_dipendente, c.Nome_Azienda as cliente, o.Data_Ordine as data_ordine, o.Data_Arrivo as data_arrivo, 
                       CONCAT(d.Nome, ' ', d.Cognome) as dipendente
                FROM ordini o
                LEFT JOIN clienti c ON o.ID_Cliente = c.ID_Cliente
                LEFT JOIN dipendenti d ON o.ID_Dipendente = d.ID_Dipendente
                $where
                ORDER BY o.Data_Ordine DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($id ? [$id] : []);
        $ordini = $stmt->fetchAll();
        $data = $id ? ($ordini[0] ?? null) : $ordini;
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addOrdine() {
    global $pdo;
    try {
        $id_cliente = $_POST['ID_Cliente'];
        $id_dipendente = $_SESSION['user_id'];
        $data_ordine = $_POST['Data_Ordine'];
        $data_arrivo = $_POST['Data_Arrivo'] ?: null;

        $sql = "INSERT INTO ordini (ID_Cliente, ID_Dipendente, Data_Ordine, Data_Arrivo) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cliente, $id_dipendente, $data_ordine, $data_arrivo]);

        $newId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'id' => $newId]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateOrdine() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $id_cliente = $_POST['ID_Cliente'];
        $data_ordine = $_POST['Data_Ordine'];
        $data_arrivo = $_POST['Data_Arrivo'] ?: null;

        $sql = "UPDATE ordini SET ID_Cliente = ?, Data_Ordine = ?, Data_Arrivo = ? WHERE ID_Ordine = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cliente, $data_ordine, $data_arrivo, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteOrdine() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $sql = "DELETE FROM ordini WHERE ID_Ordine = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getClienti() {
    global $pdo;
    try {
        $id = $_GET['id'] ?? null;
        $where = $id ? "WHERE ID_Cliente = ?" : "";
        $sql = "SELECT ID_Cliente as id, Nome_Azienda as nome_azienda, Indirizzo as indirizzo, P_IVA as p_iva, Email as email FROM clienti $where ORDER BY Nome_Azienda";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($id ? [$id] : []);
        $clienti = $stmt->fetchAll();
        $data = $id ? ($clienti[0] ?? null) : $clienti;
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addCliente() {
    global $pdo;
    try {
        $nome_azienda = $_POST['Nome_Azienda'];
        $indirizzo = $_POST['Indirizzo'];
        $p_iva = $_POST['P_IVA'];
        $email = $_POST['Email'];

        $sql = "INSERT INTO clienti (Nome_Azienda, Indirizzo, P_IVA, Email) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome_azienda, $indirizzo, $p_iva, $email]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateCliente() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $nome_azienda = $_POST['Nome_Azienda'];
        $indirizzo = $_POST['Indirizzo'];
        $p_iva = $_POST['P_IVA'];
        $email = $_POST['Email'];

        $sql = "UPDATE clienti SET Nome_Azienda = ?, Indirizzo = ?, P_IVA = ?, Email = ? WHERE ID_Cliente = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome_azienda, $indirizzo, $p_iva, $email, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteCliente() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $sql = "DELETE FROM clienti WHERE ID_Cliente = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getFiliali() {
    global $pdo;
    try {
        $id = $_GET['id'] ?? null;
        $where = $id ? "WHERE id_filiale = ?" : "";
        $sql = "SELECT id_filiale as id, Indirizzo as indirizzo, Tipo as tipo, Recapito_Telefonico as recapito FROM filiali $where ORDER BY Indirizzo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($id ? [$id] : []);
        $filiali = $stmt->fetchAll();
        $data = $id ? ($filiali[0] ?? null) : $filiali;
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addFiliale() {
    global $pdo;
    try {
        $indirizzo = $_POST['Indirizzo'];
        $tipo = $_POST['Tipo'];
        $recapito = $_POST['Recapito_Telefonico'];

        $sql = "INSERT INTO filiali (Indirizzo, Tipo, Recapito_Telefonico) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$indirizzo, $tipo, $recapito]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateFiliale() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $indirizzo = $_POST['Indirizzo'];
        $tipo = $_POST['Tipo'];
        $recapito = $_POST['Recapito_Telefonico'];

        $sql = "UPDATE filiali SET Indirizzo = ?, Tipo = ?, Recapito_Telefonico = ? WHERE id_filiale = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$indirizzo, $tipo, $recapito, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteFiliale() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $sql = "DELETE FROM filiali WHERE id_filiale = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getMagazzini() {
    global $pdo;
    try {
        $id = $_GET['id'] ?? null;
        $where = $id ? "WHERE ID_Magazzino = ?" : "";
        $sql = "SELECT ID_Magazzino as id, Indirizzo as indirizzo, Desc_Magazzino as descrizione FROM magazzini $where ORDER BY Indirizzo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($id ? [$id] : []);
        $magazzini = $stmt->fetchAll();
        $data = $id ? ($magazzini[0] ?? null) : $magazzini;
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addMagazzino() {
    global $pdo;
    try {
        $indirizzo = $_POST['Indirizzo'];
        $descrizione = $_POST['Desc_Magazzino'];

        $sql = "INSERT INTO magazzini (Indirizzo, Desc_Magazzino) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$indirizzo, $descrizione]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateMagazzino() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $indirizzo = $_POST['Indirizzo'];
        $descrizione = $_POST['Desc_Magazzino'];

        $sql = "UPDATE magazzini SET Indirizzo = ?, Desc_Magazzino = ? WHERE ID_Magazzino = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$indirizzo, $descrizione, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteMagazzino() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $sql = "DELETE FROM magazzini WHERE ID_Magazzino = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getProdotti() {
    global $pdo;
    try {
        $id = $_GET['id'] ?? null;
        $where = $id ? "WHERE ID_Prodotto = ?" : "";
        $sql = "SELECT ID_Prodotto as id, Nome as nome, Descrizione as descrizione, Prezzo as prezzo FROM prodotti $where ORDER BY Nome";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($id ? [$id] : []);
        $prodotti = $stmt->fetchAll();
        $data = $id ? ($prodotti[0] ?? null) : $prodotti;
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addProdotto() {
    global $pdo;
    try {
        $nome = $_POST['Nome'];
        $descrizione = $_POST['Descrizione'] ?? null;
        $prezzo = floatval($_POST['Prezzo'] ?? 0);

        $sql = "INSERT INTO prodotti (Nome, Descrizione, Prezzo) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $descrizione, $prezzo]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateProdotto() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $nome = $_POST['Nome'];
        $descrizione = $_POST['Descrizione'] ?? null;
        $prezzo = floatval($_POST['Prezzo'] ?? 0);

        $sql = "UPDATE prodotti SET Nome = ?, Descrizione = ?, Prezzo = ? WHERE ID_Prodotto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $descrizione, $prezzo, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteProdotto() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $sql = "DELETE FROM prodotti WHERE ID_Prodotto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getDipendenti() {
    global $pdo;
    try {
        $id = $_GET['id'] ?? null;
        $where = $id ? "WHERE d.ID_Dipendente = ?" : "";
        $sql = "SELECT d.ID_Dipendente as id_dipendente, d.ID_Dipendente as id, CONCAT(d.Nome, ' ', d.Cognome) as nome_completo, d.Nome as nome, d.Cognome as cognome, d.Tipo as posizione, f.Indirizzo as filiale, d.ID_Filiale as id_filiale, d.Username as username, d.Email as email, d.Data_Assunzione as data_assunzione, d.Stipendio as stipendio, d.IBAN as iban, d.Is_admin as is_admin FROM dipendenti d LEFT JOIN filiali f ON d.ID_Filiale = f.id_filiale $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($id ? [$id] : []);
        $dipendenti = $stmt->fetchAll();
        $data = $id ? ($dipendenti[0] ?? null) : $dipendenti;
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addDipendente() {
    global $pdo;
    try {
        $id_filiale = $_POST['ID_Filiale'];
        $username = $_POST['Username'];
        $pswd = hash('sha256', $_POST['Pswd']);
        $nome = $_POST['Nome'];
        $cognome = $_POST['Cognome'];
        $email = $_POST['Email'];
        $data_assunzione = $_POST['Data_Assunzione'];
        $stipendio = $_POST['Stipendio'];
        $iban = $_POST['IBAN'];
        $tipo = $_POST['Tipo'];
        $is_admin = $_POST['Is_admin'] ?? 0;

        $sql = "INSERT INTO dipendenti (ID_Filiale, Username, Pswd, Nome, Cognome, Email, Data_Assunzione, Stipendio, IBAN, Tipo, Is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_filiale, $username, $pswd, $nome, $cognome, $email, $data_assunzione, $stipendio, $iban, $tipo, $is_admin]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateDipendente() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $id_filiale = $_POST['ID_Filiale'];
        $username = $_POST['Username'];
        $pswd = isset($_POST['Pswd']) && !empty($_POST['Pswd']) ? hash('sha256', $_POST['Pswd']) : null;
        $nome = $_POST['Nome'];
        $cognome = $_POST['Cognome'];
        $email = $_POST['Email'];
        $data_assunzione = $_POST['Data_Assunzione'];
        $stipendio = $_POST['Stipendio'];
        $iban = $_POST['IBAN'];
        $tipo = $_POST['Tipo'];
        $is_admin = $_POST['Is_admin'] ?? 0;

        $sql = $pswd ? "UPDATE dipendenti SET ID_Filiale = ?, Username = ?, Pswd = ?, Nome = ?, Cognome = ?, Email = ?, Data_Assunzione = ?, Stipendio = ?, IBAN = ?, Tipo = ?, Is_admin = ? WHERE ID_Dipendente = ?" : "UPDATE dipendenti SET ID_Filiale = ?, Username = ?, Nome = ?, Cognome = ?, Email = ?, Data_Assunzione = ?, Stipendio = ?, IBAN = ?, Tipo = ?, Is_admin = ? WHERE ID_Dipendente = ?";
        $params = $pswd ? [$id_filiale, $username, $pswd, $nome, $cognome, $email, $data_assunzione, $stipendio, $iban, $tipo, $is_admin, $id] : [$id_filiale, $username, $nome, $cognome, $email, $data_assunzione, $stipendio, $iban, $tipo, $is_admin, $id];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteDipendente() {
    global $pdo;
    try {
        $id = $_POST['id'];
        $sql = "DELETE FROM dipendenti WHERE ID_Dipendente = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getDashboardStats() {
    global $pdo;
    try {
        $stats = [];

        $stats['totalOrdini'] = (int)$pdo->query("SELECT COUNT(DISTINCT ID_Ordine) FROM ordini")->fetchColumn();
        $stats['totalProdotti'] = (int)$pdo->query("SELECT COUNT(DISTINCT ID_Prodotto) FROM prodotti")->fetchColumn();
        $stats['totalClienti'] = (int)$pdo->query("SELECT COUNT(DISTINCT ID_Cliente) FROM clienti")->fetchColumn();
        $stats['totalMagazzini'] = (int)$pdo->query("SELECT COUNT(DISTINCT ID_Magazzino) FROM magazzini")->fetchColumn();
        $stats['totalFiliali'] = (int)$pdo->query("SELECT COUNT(DISTINCT id_filiale) FROM filiali")->fetchColumn();
        $stats['totalDipendenti'] = (int)$pdo->query("SELECT COUNT(DISTINCT ID_Dipendente) FROM dipendenti")->fetchColumn();

        echo json_encode(['success' => true, 'data' => $stats]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getProdottiMagazzini() {
    global $pdo;
    try {
        $id_magazzino = $_GET['idMagazzino'] ?? null;
        $id_prodotto = $_GET['idProdotto'] ?? null;
        
        $where = "";
        $params = [];
        
        if($id_magazzino) {
            $where .= "WHERE mp.id_magazzino = ?";
            $params[] = $id_magazzino;
        }
        if($id_prodotto) {
            $where .= ($where ? " AND " : "WHERE ") . "mp.id_prodotto = ?";
            $params[] = $id_prodotto;
        }
        
        // AGGIORNAMENTO: Seleziono p.nome e p.descrizione (nuovi nomi campi)
        // AGGIORNAMENTO: Modificato l'ORDER BY per usare p.nome
        $sql = "SELECT mp.id_magazzino, mp.id_prodotto, mp.quantita, 
                       p.nome as nome_prodotto, p.descrizione as descrizione_prodotto, 
                       m.Indirizzo as indirizzo_magazzino
                FROM magazzini_prodotti mp
                LEFT JOIN prodotti p ON mp.id_prodotto = p.ID_Prodotto
                LEFT JOIN magazzini m ON mp.id_magazzino = m.ID_Magazzino
                $where
                ORDER BY p.nome, m.Indirizzo";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addProdottoMagazzino() {
    global $pdo;
    try {
        $id_magazzino = $_POST['ID_Magazzino'];
        $id_prodotto = $_POST['ID_Prodotto'];
        $quantita = $_POST['Quantita'];

        $sql = "INSERT INTO magazzini_prodotti (id_magazzino, id_prodotto, quantita) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_magazzino, $id_prodotto, $quantita]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateProdottoMagazzino() {
    global $pdo;
    try {
        $id_magazzino = $_POST['id_magazzino'];
        $id_prodotto = $_POST['id_prodotto'];
        $quantita = $_POST['Quantita'];

        $sql = "UPDATE magazzini_prodotti SET quantita = ? WHERE id_magazzino = ? AND id_prodotto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantita, $id_magazzino, $id_prodotto]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteProdottoMagazzino() {
    global $pdo;
    try {
        $id_magazzino = $_POST['id_magazzino'];
        $id_prodotto = $_POST['id_prodotto'];

        $sql = "DELETE FROM magazzini_prodotti WHERE id_magazzino = ? AND id_prodotto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_magazzino, $id_prodotto]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getWarehousesForProduct() {
    global $pdo;
    try {
        $id_prodotto = $_GET['id_prodotto'] ?? null;
        
        if(!$id_prodotto) {
            echo json_encode(['success' => false, 'message' => 'ID prodotto non specificato']);
            return;
        }
        
        $sql = "SELECT mp.id_magazzino as id, m.Indirizzo as indirizzo, mp.quantita as quantita
                FROM magazzini_prodotti mp
                LEFT JOIN magazzini m ON mp.id_magazzino = m.ID_Magazzino
                WHERE mp.id_prodotto = ? AND mp.quantita > 0
                ORDER BY m.Indirizzo";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_prodotto]);
        $magazzini = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $magazzini]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getOrderProducts() {
    global $pdo;
    try {
        $id_ordine = $_GET['id_ordine'] ?? null;
        
        if(!$id_ordine) {
            echo json_encode(['success' => false, 'message' => 'ID ordine non specificato']);
            return;
        }
        
        $sql = "SELECT op.id_prodotto, op.id_magazzino, op.quantita,
                       p.Nome as nome_prodotto, p.Descrizione as descrizione_prodotto,
                       m.Indirizzo as indirizzo_magazzino
                FROM prodotti_ordine op
                LEFT JOIN prodotti p ON op.id_prodotto = p.ID_Prodotto
                LEFT JOIN magazzini m ON op.id_magazzino = m.ID_Magazzino
                WHERE op.id_ordine = ?
                ORDER BY p.Nome";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ordine]);
        $prodotti = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $prodotti]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addOrderProduct() {
    global $pdo;
    try {
        $id_ordine = $_POST['id_ordine'] ?? null;
        $id_prodotto = $_POST['id_prodotto'] ?? null;
        $id_magazzino = $_POST['id_magazzino'] ?? null;
        $quantita = intval($_POST['quantita'] ?? 0);
        
        if(!$id_ordine || !$id_prodotto || !$id_magazzino || $quantita <= 0) {
            echo json_encode(['success' => false, 'message' => 'Dati non validi']);
            return;
        }
        
        // Controlla disponibilità nel magazzino
        $sql = "SELECT quantita FROM magazzini_prodotti WHERE id_magazzino = ? AND id_prodotto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_magazzino, $id_prodotto]);
        $row = $stmt->fetch();
        
        if(!$row || $row['quantita'] < $quantita) {
            echo json_encode(['success' => false, 'message' => 'Prodotti insufficienti in magazzino']);
            return;
        }
        
        // Inserisci nella tabella prodotti_ordine
        $sql = "INSERT INTO prodotti_ordine (id_ordine, id_prodotto, id_magazzino, quantita) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ordine, $id_prodotto, $id_magazzino, $quantita]);
        
        // Aggiorna magazzini_prodotti decrementando la quantità
        $sql = "UPDATE magazzini_prodotti SET quantita = quantita - ? WHERE id_magazzino = ? AND id_prodotto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantita, $id_magazzino, $id_prodotto]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteOrderProduct() {
    global $pdo;
    try {
        $id_ordine = $_POST['id_ordine'] ?? null;
        $id_prodotto = $_POST['id_prodotto'] ?? null;
        $id_magazzino = $_POST['id_magazzino'] ?? null;
        
        if(!$id_ordine || !$id_prodotto || !$id_magazzino) {
            echo json_encode(['success' => false, 'message' => 'Dati non validi']);
            return;
        }
        
        // Ottieni la quantità dal prodotto dell'ordine
        $sql = "SELECT quantita FROM prodotti_ordine WHERE id_ordine = ? AND id_prodotto = ? AND id_magazzino = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ordine, $id_prodotto, $id_magazzino]);
        $row = $stmt->fetch();
        
        if(!$row) {
            echo json_encode(['success' => false, 'message' => 'Prodotto non trovato nell\'ordine']);
            return;
        }
        
        $quantita = $row['quantita'];
        
        // Rimuovi da prodotti_ordine
        $sql = "DELETE FROM prodotti_ordine WHERE id_ordine = ? AND id_prodotto = ? AND id_magazzino = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ordine, $id_prodotto, $id_magazzino]);
        
        // Aggiorna magazzini_prodotti incrementando la quantità
        $sql = "UPDATE magazzini_prodotti SET quantita = quantita + ? WHERE id_magazzino = ? AND id_prodotto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantita, $id_magazzino, $id_prodotto]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Gestore errori globale
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode(['success' => false, 'message' => "Errore PHP: $errstr (line $errline)"]);
    exit;
});

// Gestore eccezioni non catturate
set_exception_handler(function($exception) {
    echo json_encode(['success' => false, 'message' => 'Eccezione: ' . $exception->getMessage()]);
    exit;
});
?>
