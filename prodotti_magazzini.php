<?php
require_once 'login.php';
session_set_cookie_params(0, "/");
session_start();

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // L'utente è autenticato, mostra la pagina
} else {
    // L'utente non è autenticato, reindirizza al login
    header("Location: index.html");
    exit;
}

$id_prodotto = $_GET['idProdotto'] ?? null;
$id_magazzino = $_GET['idMagazzino'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionale Aziendale - Prodotti Magazzini</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            display: none;
        }
        .notification.success { background-color: #4CAF50; }
        .notification.error { background-color: #f44336; }
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
            flex-wrap: wrap;
        }
        .search-bar select,
        .search-bar input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-bar input {
            flex: 1;
            min-width: 200px;
        }
        .search-bar button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-secondary {
            background-color: #2196F3;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #0b7dda;
        }
        .search-bar .btn-secondary:last-child {
            background-color: #6c757d;
        }
        .search-bar .btn-secondary:last-child:hover {
            background-color: #5a6268;
        }
        .back-button {
            margin-bottom: 20px;
        }
        .back-button a {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }
        .back-button a:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div id="notification" class="notification <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <h1>Gestionale Aziendale - Prodotti Magazzini</h1>
            </div>
            <div class="user-info">
                <span>Benvenuto, <?php echo htmlspecialchars($_SESSION['nome'] . ' ' . $_SESSION['cognome']); ?></span>
                <button class="btn-logout" onclick="logout()">Logout</button>
            </div>
        </header>

        <!-- Dashboard Section -->
        <section class="dashboard-section">
            <!-- Bottone indietro -->
            <div class="back-button">
                <a href="javascript:history.back()">← Indietro</a>
            </div>

            <!-- Contenuto principale -->
            <main class="content">
                <div id="prodottiMagazziniContent" class="content-section active">
                    <div class="section-header">
                        <h2 id="pageTitle">Gestione Prodotti Magazzini</h2>
                        <button class="btn-primary" onclick="showForm()">Nuovo Prodotto Magazzino</button>
                    </div>
                    <div class="search-bar">
                        <select id="searchField" onchange="searchAndSort()">
                            <option value="">Seleziona campo ricerca</option>
                            <option value="prodotto">Prodotto</option>
                            <option value="magazzino">Magazzino</option>
                            <option value="quantita">Quantità</option>
                        </select>
                        <input type="text" id="searchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort()">
                        <button class="btn-secondary" onclick="sortDescending()">↓ Ordina Decrescente</button>
                        <button class="btn-secondary" onclick="clearSearchAndSort()">✕ Ripristina</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Prodotto</th>
                                    <th>Magazzino (Via)</th>
                                    <th>Quantità</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="prodottiMagazziniTable">
                            </tbody>
                        </table>
                    </div>
                    <div id="prodottiMagazziniForm" class="form-container" style="display: none;"></div>
                </div>
            </main>
        </section>
    </div>

    <script>
        const idProdotto = <?php echo json_encode($id_prodotto); ?>;
        const idMagazzino = <?php echo json_encode($id_magazzino); ?>;
        let tableDataCache = [];
        let filteredData = [];

        // Funzione per notificare
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Funzione per logout
        function logout() {
            window.location.href = 'logout.php';
        }

        // Carica i dati al caricamento della pagina
        window.addEventListener('load', () => {
            loadData();
        });

        // Funzione per caricare i dati
        function loadData() {
            let url = 'api.php?action=getProdottiMagazzini';
            if(idProdotto) url += '&idProdotto=' + idProdotto;
            if(idMagazzino) url += '&idMagazzino=' + idMagazzino;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        tableDataCache = data.data;
                        filteredData = [...data.data];
                        updateTable(data.data);
                        
                        // Aggiorna il titolo se filtrato per magazzino
                        if(idMagazzino && data.data.length > 0) {
                            const nomeMagazzino = data.data[0].indirizzo_magazzino;
                            document.getElementById('pageTitle').textContent = `Prodotti del Magazzino: ${nomeMagazzino}`;
                        }
                    } else {
                        showNotification('Errore nel caricamento dei dati', 'error');
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    showNotification('Errore nella comunicazione con il server', 'error');
                });
        }

        // Funzione per aggiornare la tabella
        function updateTable(data) {
            const tableBody = document.getElementById('prodottiMagazziniTable');
            tableBody.innerHTML = '';
            
            data.forEach(item => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${item.id_prodotto}</td>
                        <td>${item.nome_prodotto}</td>
                        <td>${item.indirizzo_magazzino}</td>
                        <td>${item.quantita}</td>
                        <td>
                            <button class="btn-icon edit" onclick="editItem(${item.id_prodotto}, ${item.id_magazzino})">✏️</button>
                            <button class="btn-icon delete" onclick="deleteItem(${item.id_prodotto}, ${item.id_magazzino})">🗑️</button>
                        </td>
                    </tr>
                `;
            });
        }

        // Funzione per mostrare il form di aggiunta/modifica
        function showForm(idProdotto = null, idMagazzino = null) {
            const formContainer = document.getElementById('prodottiMagazziniForm');
            
            if(idProdotto && idMagazzino) {
                // Carica i dati per la modifica
                fetch(`api.php?action=getProdottiMagazzini&idProdotto=${idProdotto}&idMagazzino=${idMagazzino}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success && data.data.length > 0) {
                            renderForm(data.data[0], idProdotto, idMagazzino);
                        }
                    });
            } else {
                renderForm(null, null, null);
            }
            
            formContainer.style.display = 'block';
            formContainer.scrollIntoView({ behavior: 'smooth' });
        }

        // Funzione per renderizzare il form
        function renderForm(data, idProdotto, idMagazzino) {
            const formContainer = document.getElementById('prodottiMagazziniForm');
            
            // Carica prodotti e magazzini per i select
            Promise.all([
                fetch('api.php?action=getProdotti').then(r => r.json()),
                fetch('api.php?action=getMagazzini').then(r => r.json())
            ]).then(([prodottiRes, magazziniRes]) => {
                if(prodottiRes.success && magazziniRes.success) {
                    const prodottiOptions = prodottiRes.data.map(p => `<option value="${p.id}" ${data && data.id_prodotto == p.id ? 'selected' : ''}>${p.nome}</option>`).join('');
                    const magazziniOptions = magazziniRes.data.map(m => `<option value="${m.id}" ${data && data.id_magazzino == m.id ? 'selected' : ''}>${m.indirizzo}</option>`).join('');
                    
                    formContainer.innerHTML = `
                        <h3>${idProdotto && idMagazzino ? 'Modifica' : 'Nuovo'} Prodotto Magazzino</h3>
                        <form onsubmit="saveItem(event, ${idProdotto || 'null'}, ${idMagazzino || 'null'})">
                            <div class="form-group">
                                <label>Prodotto</label>
                                <select name="ID_Prodotto" required>
                                    <option value="">Seleziona Prodotto</option>
                                    ${prodottiOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Magazzino</label>
                                <select name="ID_Magazzino" required>
                                    <option value="">Seleziona Magazzino</option>
                                    ${magazziniOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantità</label>
                                <input type="number" step="0.01" name="Quantita" value="${data ? data.quantita : ''}" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-success">Salva</button>
                                <button type="button" class="btn-cancel" onclick="hideForm()">Annulla</button>
                            </div>
                        </form>
                    `;
                }
            });
        }

        // Funzione per salvare l'elemento
        function saveItem(event, idProdotto, idMagazzino) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Disabilita il bottone durante l'invio
            if(submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Salvataggio...';
            }
            
            const formData = new FormData(form);
            formData.append('action', idProdotto && idMagazzino ? 'updateProdottiMagazzini' : 'addProdottiMagazzini');
            if(idProdotto && idMagazzino) {
                formData.append('id_prodotto', idProdotto);
                formData.append('id_magazzino', idMagazzino);
            }
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Errore HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    hideForm();
                    loadData();
                    showNotification('✓ Elemento salvato con successo!', 'success');
                } else {
                    showNotification('✗ Errore: ' + (data.message || 'Errore sconosciuto'), 'error');
                    if(submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Salva';
                    }
                }
            })
            .catch(error => {
                console.error('Dettagli errore:', error);
                showNotification('✗ Errore nella comunicazione con il server: ' + error.message, 'error');
                if(submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Salva';
                }
            });
        }

        // Funzione per modificare un elemento
        function editItem(idProdotto, idMagazzino) {
            showForm(idProdotto, idMagazzino);
        }

        // Funzione per eliminare un elemento
        function deleteItem(idProdotto, idMagazzino) {
            if(confirm('Sei sicuro di voler eliminare questo elemento?')) {
                const formData = new FormData();
                formData.append('action', 'deleteProdottiMagazzini');
                formData.append('id_prodotto', idProdotto);
                formData.append('id_magazzino', idMagazzino);
                
                fetch('api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        loadData();
                        showNotification('✓ Elemento eliminato con successo!', 'success');
                    } else {
                        showNotification('✗ Errore: ' + (data.message || 'Errore sconosciuto'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    showNotification('✗ Errore nella comunicazione con il server', 'error');
                });
            }
        }

        // Funzione per nascondere il form
        function hideForm() {
            const formContainer = document.getElementById('prodottiMagazziniForm');
            formContainer.style.display = 'none';
        }

        // Funzione per la ricerca e il filtro
        function searchAndSort() {
            const searchField = document.getElementById('searchField').value;
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            
            filteredData = tableDataCache.filter(item => {
                if(!searchField) return true;
                
                let fieldValue = '';
                if(searchField === 'prodotto') fieldValue = item.nome_prodotto.toLowerCase();
                else if(searchField === 'magazzino') fieldValue = item.indirizzo_magazzino.toLowerCase();
                else if(searchField === 'quantita') fieldValue = item.quantita.toString().toLowerCase();
                
                return fieldValue.includes(searchInput);
            });
            
            updateTable(filteredData);
        }

        // Funzione per ordinare in modo decrescente
        function sortDescending() {
            filteredData.sort((a, b) => b.id - a.id);
            updateTable(filteredData);
        }

        // Funzione per ripristinare la ricerca
        function clearSearchAndSort() {
            document.getElementById('searchField').value = '';
            document.getElementById('searchInput').value = '';
            filteredData = [...tableDataCache];
            updateTable(tableDataCache);
        }
    </script>
</body>
</html>
