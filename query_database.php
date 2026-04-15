controllo admin
function isAdmin($pdo, $id_dipendente) {
    $sql = "SELECT Is_admin FROM dipendenti WHERE ID_Dipendente = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_dipendente]);
    $user = $stmt->fetch();

    return $user && $user['Is_admin'] == 1;
}
aggiungi cliente
function aggiungiCliente($pdo, $id_admin, $nome, $indirizzo, $piva, $email) {
    if (!isAdmin($pdo, $id_admin)) {
        die("Accesso negato!");
    }

    $sql = "INSERT INTO clienti (Nome_Azienda, Indirizzo, P_IVA, Email)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $indirizzo, $piva, $email]);

    echo "Cliente aggiunto!";
}
elimina cliente
function eliminaCliente($pdo, $id_admin, $id_cliente) {
    if (!isAdmin($pdo, $id_admin)) {
        die("Accesso negato!");
    }

    $sql = "DELETE FROM clienti WHERE ID_Cliente = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_cliente]);

    echo "Cliente eliminato!";
}
visione clienti
function mostraClienti($pdo) {
    $sql = "SELECT * FROM clienti";
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['ID_Cliente'] . "<br>";
        echo "Nome: " . $row['Nome_Azienda'] . "<br>";
        echo "Email: " . $row['Email'] . "<br><br>";
    }
}
crea ordine
function creaOrdine($pdo, $id_dipendente, $id_cliente) {
    $sql = "INSERT INTO ordini (ID_Cliente, ID_Dipendente, Data_Ordine)
            VALUES (?, ?, CURDATE())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_cliente, $id_dipendente]);

    return $pdo->lastInsertId();
}
aggiunta prodotto
function aggiungiProdottoOrdine($pdo, $id_ordine, $id_prodotto, $quantita) {
    $sql = "INSERT INTO ordine_prodotti (ID_Ordine, ID_Prodotto, Quantita)
            VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ordine, $id_prodotto, $quantita]);
}

elimina ordine 
function eliminaOrdine($pdo, $id_admin, $id_ordine) {
    if (!isAdmin($pdo, $id_admin)) {
        die("Accesso negato!");
    }

    $pdo->prepare("DELETE FROM ordine_prodotti WHERE ID_Ordine = ?")->execute([$id_ordine]);
    $pdo->prepare("DELETE FROM ordini WHERE ID_Ordine = ?")->execute([$id_ordine]);

    echo "Ordine eliminato!";
}
mostra ordini
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
totale ordini prodotto 
function totaleProdottiOrdine($pdo, $id_ordine) {
    $sql = "SELECT SUM(Quantita) as totale
            FROM ordine_prodotti
            WHERE ID_Ordine = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ordine]);

    $result = $stmt->fetch();
    return $result['totale'];
}
ricerca per nome 
function cercaCliente($pdo, $nome) {
    $sql = "SELECT * FROM clienti WHERE Nome_Azienda LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$nome%"]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}