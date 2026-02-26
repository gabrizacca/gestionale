<?php
require 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // 1. Cerchiamo il dipendente con quel token
    $stmt = $pdo->prepare("SELECT ID_Dipendente FROM Dipendenti WHERE Token_Verifica = ? AND Email_Verificata = 0");
    $stmt->execute([$token]);
    $utente = $stmt->fetch();

    if ($utente) {
        // 2. Trovato! Attiviamo l'account e svuotiamo il token
        $updateStmt = $pdo->prepare("UPDATE Dipendenti SET Email_Verificata = 1, Token_Verifica = NULL WHERE ID_Dipendente = ?");
        
        if ($updateStmt->execute([$utente['ID_Dipendente']])) {
            echo "<h2>Ottimo lavoro!</h2><p>La tua email è stata confermata. Ora puoi effettuare il <a href='login.php'>login</a>.</p>";
        } else {
            echo "Ops! Qualcosa è andato storto durante l'attivazione.";
        }
    } else {
        echo "<h2>Errore</h2><p>Il token non è valido o l'account è già stato verificato.</p>";
    }
} else {
    echo "Accesso negato. Nessun token fornito.";
}
?>