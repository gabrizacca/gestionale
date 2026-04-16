<?php
// ================== CONNESSIONE ==================
$host = "localhost";
$dbname = "gestionale";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Errore connessione: " . $e->getMessage());
}


// ================== CONTROLLO ADMIN ==================
function isAdmin($pdo, $id_dipendente) {
    $sql = "SELECT Is_admin FROM dipendenti WHERE ID_Dipendente = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_dipendente]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($user && $user['Is_admin'] == 1);
}


// ================== CLIENTI ==================

// AGGIUNTA CLIENTE
function aggiungiCliente($pdo, $id_admin, $nome, $indirizzo, $piva, $email) {
    if (!isAdmin($pdo, $id_admin)) {
        die("Accesso negato!");
    }

    $sql = "INSERT INTO clienti (Nome_Azienda, Indirizzo, P_IVA, Email)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $indirizzo, $piva, $email]);

    echo "Cliente aggiunto!<br>";
}


// ELIMINA CLIENTE
function eliminaCliente($pdo, $id_admin, $id_cliente) {
    if (!isAdmin($pdo, $id_admin)) {
        die("Accesso negato!");
    }

    $sql = "DELETE FROM clienti WHERE ID_Cliente = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_cliente]);

    echo "Cliente eliminato!<br>";
}


// MOSTRA CLIENTI
function mostraClienti($pdo) {
    $sql = "SELECT * FROM clienti";
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['ID_Cliente'] . "<br>";
        echo "Nome: " . $row['Nome_Azienda'] . "<br>";
        echo "Email: " . $row['Email'] . "<br><br>";
    }
}


// RICERCA CLIENTE
function cercaCliente($pdo, $nome) {
    $sql = "SELECT * FROM clienti WHERE Nome_Azienda LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$nome%"]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// ================== ORDINI ==================

// CREA ORDINE
function creaOrdine($pdo, $id_dipendente, $id_cliente) {
    $sql = "INSERT INTO ordini (ID_Cliente, ID_Dipendente, Data_Ordine)
            VALUES (?, ?, CURDATE())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_cliente, $id_dipendente]);

    return $pdo->lastInsertId();
}


// AGGIUNGI PRODOTTO A ORDINE
function aggiungiProdottoOrdine($pdo, $id_ordine, $id_prodotto, $quantita) {
    $sql = "INSERT INTO ordine_prodotti (ID_Ordine, ID_Prodotto, Quantita)
            VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ordine, $id_prodotto, $quantita]);
}


// ELIMINA ORDINE (solo admin)
function eliminaOrdine($pdo, $id_admin, $id_ordine) {
    if (!isAdmin($pdo, $id_admin)) {
        die("Accesso negato!");
    }

    $pdo->prepare("DELETE FROM ordine_prodotti WHERE ID_Ordine = ?")
        ->execute([$id_ordine]);

    $pdo->prepare("DELETE FROM ordini WHERE ID_Ordine = ?")
        ->execute([$id_ordine]);

    echo "Ordine eliminato!<br>";
}


// MOSTRA ORDINI
function mostraOrdini($pdo) {
    $sql = "SELECT o.ID_Ordine, c.Nome_Azienda, d.Nome, d.Cognome, o.Data_Ordine
            FROM ordini o
            JOIN clienti c ON o.ID_Cliente = c.ID_Cliente
            JOIN dipendenti d ON o.ID_Dipendente = d.ID_Dipendente";

    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Ordine #" . $row['ID_Ordine'] . "<br>";
        echo "Cliente: " . $row['Nome_Azienda'] . "<br>";
        echo "Dipendente: " . $row['Nome'] . " " . $row['Cognome'] . "<br>";
        echo "Data: " . $row['Data_Ordine'] . "<br><br>";
    }
}


// TOTALE PRODOTTI IN ORDINE
function totaleProdottiOrdine($pdo, $id_ordine) {
    $sql = "SELECT SUM(Quantita) as totale
            FROM ordine_prodotti
            WHERE ID_Ordine = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ordine]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['totale'] ?? 0;
}
?>
