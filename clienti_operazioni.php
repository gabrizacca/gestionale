<?php
require_once 'database_config.php';

// Inizializza variabili
$operazione = isset($_GET['op']) ? $_GET['op'] : 'lista';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$messaggio = '';
$errore = '';

// Variabili per il form
$nome_azienda = '';
$indirizzo = '';
$p_iva = '';
$email = '';

// ==================== ELIMINA CLIENTE ====================
if ($operazione == 'elimina' && $id > 0) {
    try {
        // Verifica se ci sono ordini associati
        $check = $pdo->prepare("SELECT COUNT(*) FROM ordini WHERE ID_Cliente = ?");
        $check->execute([$id]);
        $num_ordini = $check->fetchColumn();
        
        if ($num_ordini > 0) {
            $errore = "Impossibile eliminare: questo cliente ha $num_ordini ordini associati. Elimina prima gli ordini.";
            $operazione = 'lista';
        } else {
            // Elimina cliente
            $sql = "DELETE FROM clienti WHERE ID_Cliente = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $messaggio = "Cliente eliminato con successo!";
            $operazione = 'lista';
        }
    } catch(PDOException $e) {
        $errore = "Errore durante l'eliminazione: " . $e->getMessage();
        $operazione = 'lista';
    }
}

// ==================== SALVA CLIENTE (CREATE/UPDATE) ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_azienda = trim($_POST['nome_azienda']);
    $indirizzo = trim($_POST['indirizzo']);
    $p_iva = trim($_POST['p_iva']);
    $email = trim($_POST['email']);
    $op = $_POST['operazione'];
    $id_cliente = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    // Validazione
    if (empty($nome_azienda) || empty($p_iva) || empty($email)) {
        $errore = "Nome Azienda, P.IVA ed Email sono campi obbligatori";
    } else {
        try {
            if ($op == 'crea') {
                // Verifica se P.IVA esiste già
                $check = $pdo->prepare("SELECT ID_Cliente FROM clienti WHERE P_IVA = ?");
                $check->execute([$p_iva]);
                if ($check->rowCount() > 0) {
                    $errore = "Questa Partita IVA è già registrata";
                } else {
                    // Inserimento
                    $sql = "INSERT INTO clienti (Nome_Azienda, Indirizzo, P_IVA, Email) 
                            VALUES (?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nome_azienda, $indirizzo, $p_iva, $email]);
                    $messaggio = "Cliente creato con successo!";
                    $operazione = 'lista';
                }
            } 
            elseif ($op == 'modifica') {
                // Verifica se P.IVA esiste già per un altro cliente
                $check = $pdo->prepare("SELECT ID_Cliente FROM clienti WHERE P_IVA = ? AND ID_Cliente != ?");
                $check->execute([$p_iva, $id_cliente]);
                if ($check->rowCount() > 0) {
                    $errore = "Questa Partita IVA è già registrata per un altro cliente";
                } else {
                    // Aggiornamento
                    $sql = "UPDATE clienti SET Nome_Azienda = ?, Indirizzo = ?, P_IVA = ?, Email = ? 
                            WHERE ID_Cliente = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nome_azienda, $indirizzo, $p_iva, $email, $id_cliente]);
                    $messaggio = "Cliente aggiornato con successo!";
                    $operazione = 'lista';
                }
            }
        } catch(PDOException $e) {
            $errore = "Errore: " . $e->getMessage();
        }
    }
    
    // Se c'è errore, rimani nella stessa operazione
    if ($errore) {
        $operazione = ($op == 'crea') ? 'crea' : 'modifica';
        if ($op == 'modifica') {
            $id = $id_cliente;
        }
    }
}

// ==================== PREPARA DATI PER MODIFICA ====================
if ($operazione == 'modifica' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM clienti WHERE ID_Cliente = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        $errore = "Cliente non trovato!";
        $operazione = 'lista';
    } else {
        $nome_azienda = $cliente['Nome_Azienda'];
        $indirizzo = $cliente['Indirizzo'];
        $p_iva = $cliente['P_IVA'];
        $email = $cliente['Email'];
    }
}

// ==================== RICERCA CLIENTI ====================
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search && $operazione == 'lista') {
    $sql = "SELECT * FROM clienti WHERE Nome_Azienda LIKE :search OR Email LIKE :search OR P_IVA LIKE :search ORDER BY ID_Cliente DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
} elseif ($operazione == 'lista') {
    $sql = "SELECT * FROM clienti ORDER BY ID_Cliente DESC";
    $stmt = $pdo->query($sql);
}
if ($operazione == 'lista') {
    $clienti = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Clienti</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .content {
            padding: 20px;
        }
        
        /* Messaggi */
        .messaggio {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            animation: fadeIn 0.5s;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Pulsanti */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        input[type="text"],
        input[type="email"] {
            width: 100%;
            max-width: 500px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        /* Tabella */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        /* Barra azioni */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
        }
        
        .search-box input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Bottoni azioni tabella */
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            margin: 0 2px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                flex-direction: column;
            }
            
            .search-box input {
                width: 100%;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Gestione Clienti</h1>
        </div>
        
        <div class="content">
            <?php if ($messaggio): ?>
                <div class="messaggio success">
                    ✓ <?= htmlspecialchars($messaggio) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errore): ?>
                <div class="messaggio error">
                    ✗ <?= htmlspecialchars($errore) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($operazione == 'lista'): ?>
                <!-- ==================== LISTA CLIENTI ==================== -->
                <div class="action-bar">
                    <a href="?op=crea" class="btn btn-success">+ Nuovo Cliente</a>
                    
                    <form method="GET" class="search-box">
                        <input type="hidden" name="op" value="lista">
                        <input type="text" name="search" placeholder="Cerca per nome, email o P.IVA..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary">Cerca</button>
                        <?php if ($search): ?>
                            <a href="?op=lista" class="btn btn-secondary">Mostra tutti</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Azienda</th>
                            <th>Indirizzo</th>
                            <th>P.IVA</th>
                            <th>Email</th>
                            <th style="width: 120px;">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($clienti) && count($clienti) > 0): ?>
                            <?php foreach ($clienti as $cliente): ?>
                            <tr>
                                <td><?= $cliente['ID_Cliente'] ?></td>
                                <td><?= htmlspecialchars($cliente['Nome_Azienda']) ?></td>
                                <td><?= htmlspecialchars($cliente['Indirizzo']) ?></td>
                                <td><?= htmlspecialchars($cliente['P_IVA']) ?></td>
                                <td><?= htmlspecialchars($cliente['Email']) ?></td>
                                <td>
                                    <a href="?op=modifica&id=<?= $cliente['ID_Cliente'] ?>" 
                                       class="btn btn-warning btn-small">Modifica</a>
                                    <a href="?op=elimina&id=<?= $cliente['ID_Cliente'] ?>" 
                                       class="btn btn-danger btn-small"
                                       onclick="return confirm('Sei sicuro di voler eliminare questo cliente?')">Elimina</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px;">
                                    <?php if ($search): ?>
                                        Nessun cliente trovato per "<?= htmlspecialchars($search) ?>"
                                    <?php else: ?>
                                        Nessun cliente presente. <a href="?op=crea">Clicca qui per aggiungerne uno</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            <?php elseif ($operazione == 'crea' || $operazione == 'modifica'): ?>
                <!-- ==================== FORM CREA/MODIFICA CLIENTE ==================== -->
                <h2><?= $operazione == 'crea' ? 'Nuovo Cliente' : 'Modifica Cliente' ?></h2>
                
                <form method="POST" style="margin-top: 20px;">
                    <input type="hidden" name="operazione" value="<?= $operazione ?>">
                    <?php if ($operazione == 'modifica'): ?>
                        <input type="hidden" name="id" value="<?= $id ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Nome Azienda *</label>
                        <input type="text" name="nome_azienda" 
                               value="<?= htmlspecialchars($nome_azienda) ?>" 
                               required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label>Indirizzo</label>
                        <input type="text" name="indirizzo" 
                               value="<?= htmlspecialchars($indirizzo) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Partita IVA *</label>
                        <input type="text" name="p_iva" 
                               value="<?= htmlspecialchars($p_iva) ?>" 
                               required 
                               pattern="[A-Za-z0-9]{11,16}"
                               title="La Partita IVA deve essere di 11-16 caratteri alfanumerici">
                        <small style="color: #666; font-size: 12px;">Formato: 11-16 caratteri alfanumerici</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" 
                               value="<?= htmlspecialchars($email) ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <?= $operazione == 'crea' ? 'Salva Cliente' : 'Aggiorna Cliente' ?>
                        </button>
                        <a href="?op=lista" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
                
            <?php endif; ?>
        </div>
    </div>
</body>
</html>