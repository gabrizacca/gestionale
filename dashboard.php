<?php
require_once 'login.php';
session_set_cookie_params(0, "/");
session_start();

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // L'utente è autenticato, mostra la dashboard
} else {
    // L'utente non è autenticato, reindirizza al login
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionale Aziendale</title>
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
    </style>
    
</head>
<body>
    <div id="notification" class="notification <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <h1>Gestionale Aziendale</h1>
            </div>
            <div class="user-info">
                <span>Benvenuto, <?php echo htmlspecialchars($_SESSION['nome'] . ' ' . $_SESSION['cognome']); ?><?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) echo ' 👑'; ?></span>
                <button class="btn-logout" onclick="logout()">Logout</button>
            </div>
        </header>

        <!-- Dashboard Section -->
        <section class="dashboard-section">
            <!-- Menu di navigazione -->
            <nav class="sidebar">
                <ul class="nav-menu">
                            <li class="nav-item active" data-section="dashboard">
                                <a href="#" onclick="showSection('dashboard')">Dashboard</a>
                            </li>
                            <li class="nav-item" data-section="meiOrdini">
                                <a href="#" onclick="showSection('meiOrdini')">I Miei Ordini</a>
                            </li>
                            <li class="nav-item" data-section="prodotti">
                                <a href="#" onclick="showSection('prodotti')">Prodotti</a>
                            </li>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?> 
                            <li class="nav-item" data-section="ordini">
                                <a href="#" onclick="showSection('ordini')">Ordini Totali</a>
                            </li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?> 
                            <li class="nav-item" data-section="clienti">
                                <a href="#" onclick="showSection('clienti')">Clienti</a>
                            </li>
                            <li class="nav-item" data-section="magazzini">
                                <a href="#" onclick="showSection('magazzini')">Magazzini</a>
                            </li>
                            <li class="nav-item" data-section="filiali">
                                <a href="#" onclick="showSection('filiali')">Filiali</a>
                            </li>
                            <li class="nav-item" data-section="dipendenti">
                                <a href="#" onclick="showSection('dipendenti')">Dipendenti</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
        
                    <!-- Contenuto principale -->
                    <main class="content">
                        <div id="dashboardContent" class="content-section active">
                            <h2>Dashboard</h2>
                            <div class="stats-cards" id="statsCards">
                                <div class="stat-card">
                                    <h3>Ordini Totali</h3>
                                    <p class="stat-number" id="totalOrdini">0</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Prodotti</h3>
                                    <p class="stat-number" id="totalProdotti">0</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Clienti</h3>
                                    <p class="stat-number" id="totalClienti">0</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Magazzini</h3>
                                    <p class="stat-number" id="totalMagazzini">0</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Filiali</h3>
                                    <p class="stat-number" id="totalFiliali">0</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Dipendenti</h3>
                                    <p class="stat-number" id="totalDipendenti">0</p>
                                </div>
                            </div>
                        </div>
                        <!-- I Miei Ordini -->
                        <div id="meiOrdiniContent" class="content-section">
                            <div class="section-header">
                                <h2>I Miei Ordini</h2>
                                <button class="btn-primary" onclick="showForm('meiOrdini')">Nuovo Ordine</button>
                            </div>
                            <div class="search-bar">
                                <select id="meiOrdiniSearchField" onchange="searchAndSort('meiOrdini')">
                                    <option value="">Seleziona campo ricerca</option>
                                    <option value="cliente">Cliente</option>
                                </select>
                                <input type="text" id="meiOrdiniSearchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort('meiOrdini')">
                                <button class="btn-secondary" onclick="sortDescending('meiOrdini')">↓ Ordina Decrescente</button>
                                <button class="btn-secondary" onclick="clearSearchAndSort('meiOrdini')">✕ Ripristina</button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID Ordine</th>
                                            <th>Cliente</th>
                                            <th>Data Ordine</th>
                                            <th>Data Arrivo</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="meiOrdiniTable">
                                    </tbody>
                                </table>
                            </div>
                            <div id="meiOrdiniForm" class="form-container" style="display: none;"></div>
                        </div>
                        
                        <!-- Ordini Totali (Solo Admin) -->
                        <div id="ordiniContent" class="content-section">
                            <div class="section-header">
                                <h2>Gestione Ordini (Totali)</h2>
                                <button class="btn-primary" onclick="showForm('ordini')">Nuovo Ordine</button>
                            </div>
                            <div class="search-bar">
                                <select id="ordiniSearchField" onchange="searchAndSort('ordini')">
                                    <option value="">Seleziona campo ricerca</option>
                                    <option value="cliente">Cliente</option>
                                </select>
                                <input type="text" id="ordiniSearchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort('ordini')">
                                <button class="btn-secondary" onclick="sortDescending('ordini')">↓ Ordina Decrescente</button>
                                <button class="btn-secondary" onclick="clearSearchAndSort('ordini')">✕ Ripristina</button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID Ordine</th>
                                            <th>Cliente</th>
                                            <th>Data Ordine</th>
                                            <th>Data Arrivo</th>
                                            <th>Dipendente</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ordiniTable">
                                    </tbody>
                                </table>
                            </div>
                            <div id="ordiniForm" class="form-container" style="display: none;"></div>
                        </div>
        
                        <!-- Prodotti -->
                        <div id="prodottiContent" class="content-section">
                            <div class="section-header">
                                <h2>Gestione Prodotti</h2>
                                <button class="btn-primary" onclick="showForm('prodotti')">Nuovo Prodotto</button>
                            </div>
                            <div class="search-bar">
                                <select id="prodottiSearchField" onchange="searchAndSort('prodotti')">
                                    <option value="">Seleziona campo ricerca</option>
                                    <option value="nome">Nome</option>
                                </select>
                                <input type="text" id="prodottiSearchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort('prodotti')">
                                <button class="btn-secondary" onclick="sortDescending('prodotti')">↓ Ordina Decrescente</button>
                                <button class="btn-secondary" onclick="clearSearchAndSort('prodotti')">✕ Ripristina</button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Descrizione</th>
                                            <th>Prezzo</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="prodottiTable">
                                    </tbody>
                                </table>
                            </div>
                            <div id="prodottiForm" class="form-container" style="display: none;"></div>
                        </div>
        
                        <!-- Clienti -->
                        <div id="clientiContent" class="content-section">
                            <div class="section-header">
                                <h2>Gestione Clienti</h2>
                                <button class="btn-primary" onclick="showForm('clienti')">Nuovo Cliente</button>
                            </div>
                            <div class="search-bar">
                                <select id="clientiSearchField" onchange="searchAndSort('clienti')">
                                    <option value="">Seleziona campo ricerca</option>
                                    <option value="nome_azienda">Nome Azienda</option>
                                    <option value="indirizzo">Indirizzo</option>
                                    <option value="email">Email</option>
                                </select>
                                <input type="text" id="clientiSearchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort('clienti')">
                                <button class="btn-secondary" onclick="sortDescending('clienti')">↓ Ordina Decrescente</button>
                                <button class="btn-secondary" onclick="clearSearchAndSort('clienti')">✕ Ripristina</button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome Azienda</th>
                                            <th>Indirizzo</th>
                                            <th>P.IVA</th>
                                            <th>Email</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="clientiTable">
                                    </tbody>
                                </table>
                            </div>
                            <div id="clientiForm" class="form-container" style="display: none;"></div>
                        </div>
        
                        <!-- Magazzini -->
                        <div id="magazziniContent" class="content-section">
                            <div class="section-header">
                                <h2>Gestione Magazzini</h2>
                                <button class="btn-primary" onclick="showForm('magazzini')">Nuovo Magazzino</button>
                            </div>
                            <div class="search-bar">
                                <select id="magazziniSearchField" onchange="searchAndSort('magazzini')">
                                    <option value="">Seleziona campo ricerca</option>
                                    <option value="indirizzo">Indirizzo</option>
                                    <option value="descrizione">Descrizione</option>
                                </select>
                                <input type="text" id="magazziniSearchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort('magazzini')">
                                <button class="btn-secondary" onclick="sortDescending('magazzini')">↓ Ordina Decrescente</button>
                                <button class="btn-secondary" onclick="clearSearchAndSort('magazzini')">✕ Ripristina</button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Indirizzo</th>
                                            <th>Descrizione</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="magazziniTable">
                                    </tbody>
                                </table>
                            </div>
                            <div id="magazziniForm" class="form-container" style="display: none;"></div>
                        </div>
        
                        <!-- Filiali -->
                        <div id="filialiContent" class="content-section">
                            <div class="section-header">
                                <h2>Gestione Filiali</h2>
                                <button class="btn-primary" onclick="showForm('filiali')">Nuova Filiale</button>
                            </div>
                            <div class="search-bar">
                                <select id="filialiSearchField" onchange="searchAndSort('filiali')">
                                    <option value="">Seleziona campo ricerca</option>
                                    <option value="indirizzo">Indirizzo</option>
                                    <option value="tipo">Tipo</option>
                                </select>
                                <input type="text" id="filialiSearchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort('filiali')">
                                <button class="btn-secondary" onclick="sortDescending('filiali')">↓ Ordina Decrescente</button>
                                <button class="btn-secondary" onclick="clearSearchAndSort('filiali')">✕ Ripristina</button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Indirizzo</th>
                                            <th>Tipo</th>
                                            <th>Recapito</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filialiTable">
                                    </tbody>
                                </table>
                            </div>
                            <div id="filialiForm" class="form-container" style="display: none;"></div>
                        </div>
        
                        <!-- Dipendenti -->
                        <div id="dipendentiContent" class="content-section">
                            <div class="section-header">
                                <h2>Dipendenti</h2>
                                <button class="btn-primary" onclick="add_employee()">Nuovo Dipendente</button>
                            </div>
                            <div class="section-list">
                                <h3>Lista Dipendenti</h3>
                                <div class="search-bar">
                                    <select id="dipendentiSearchField" onchange="searchAndSort('dipendenti')">
                                        <option value="">Seleziona campo ricerca</option>
                                        <option value="nome">Nome</option>
                                        <option value="cognome">Cognome</option>
                                        <option value="posizione">Posizione</option>
                                        <option value="filiale">Filiale</option>
                                    </select>
                                    <input type="text" id="dipendentiSearchInput" placeholder="Inserisci termine di ricerca..." onkeyup="searchAndSort('dipendenti')">
                                    <button class="btn-secondary" onclick="sortDescending('dipendenti')">↓ Ordina Decrescente</button>
                                    <button class="btn-secondary" onclick="clearSearchAndSort('dipendenti')">✕ Ripristina</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID Dipendente</th>
                                                <th>Nome</th>
                                                <th>Cognome</th>
                                                <th>Posizione</th>
                                                <th>Filiale</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dipendentiTable">
                                        </tbody>
                                    </table>
                                </div>
                                <div id="dipendentiPagination" class="pagination" style="display: none;">
                                    <button class="btn-pagination" onclick="changePage('dipendenti', 'prev')">Precedente</button>
                                    <span id="dipendentiPageInfo"></span>
                                    <button class="btn-pagination" onclick="changePage('dipendenti', 'next')">Successivo</button>
                                </div>
                            </div>
                            <div id="dipendentiForm" class="form-container" style="display: none;"></div>
                        </div>
                    </main>
                </section>
            </div>  
            <script>
                // Variabili per la paginazione
                let dipendentiData = [];
                let dipendentiCurrentPage = 1;
                const dipendentiPerPage = 10;
                
                // Variabili per memorizzare i dati originali di tutte le sezioni
                let tableDataCache = {
                    ordini: [],
                    meiOrdini: [],
                    prodotti: [],
                    clienti: [],
                    magazzini: [],
                    filiali: [],
                    dipendenti: []
                };
                
                // Variabili per tracciare lo stato di ordinamento
                let sortState = {
                    ordini: false,
                    meiOrdini: false,
                    prodotti: false,
                    clienti: false,
                    magazzini: false,
                    filiali: false,
                    dipendenti: false
                };
                
                /**
                 * Funzione principale di ricerca e ordinamento
                 * @param {string} section - Sezione della tabella (ordini, clienti, etc.)
                 */
                function searchAndSort(section) {
                    const searchField = document.getElementById(`${section}SearchField`)?.value || '';
                    const searchInput = document.getElementById(`${section}SearchInput`)?.value.toLowerCase() || '';
                    
                    if (!searchField || !searchInput) {
                        // Se nessun filtro è selezionato, mostra i dati originali
                        if (section === 'dipendenti') {
                            dipendentiData = tableDataCache[section];
                            dipendentiCurrentPage = 1;
                            updateDipendentiTable();
                        } else {
                            updateTable(section, tableDataCache[section]);
                        }
                        return;
                    }
                    
                    // Filtra i dati in base al campo e al termine di ricerca
                    const filtered = tableDataCache[section].filter(item => {
                        const fieldValue = item[searchField]?.toString().toLowerCase() || '';
                        return fieldValue.includes(searchInput);
                    });
                    
                    if (section === 'dipendenti') {
                        dipendentiData = filtered;
                        dipendentiCurrentPage = 1;
                        updateDipendentiTable();
                    } else {
                        updateTable(section, filtered);
                    }
                }
                
                /**
                 * Ordina i dati in ordine decrescente
                 * @param {string} section - Sezione della tabella
                 */
                function sortDescending(section) {
                    const searchField = document.getElementById(`${section}SearchField`)?.value;
                    const tableBody = document.getElementById(`${section}Table`);
                    
                    if (!tableBody) return;
                    
                    // Se non c'è un campo selezionato, ordina per ID/prima colonna
                    let sortKey = searchField || (section === 'dipendenti' ? 'id_dipendente' : 'id');
                    
                    // Prendi i dati visualizzati e ordina in modo decrescente
                    const rows = Array.from(tableBody.querySelectorAll('tr'));
                    const dataToSort = rows.map(row => {
                        const cells = row.querySelectorAll('td');
                        return {row: row, data: cells[0]?.textContent || ''};
                    });
                    
                    // Ordina decrescente (alfabetico per stringhe, numerico per numeri)
                    dataToSort.sort((a, b) => {
                        const aVal = isNaN(a.data) ? a.data : parseFloat(a.data);
                        const bVal = isNaN(b.data) ? b.data : parseFloat(b.data);
                        return bVal > aVal ? 1 : -1;
                    });
                    
                    // Ricrea la tabella con i dati ordinati
                    tableBody.innerHTML = '';
                    dataToSort.forEach(item => {
                        tableBody.appendChild(item.row);
                    });
                    
                    sortState[section] = true;
                }
                
                /**
                 * Ripristina la vista originale senza filtri
                 * @param {string} section - Sezione della tabella
                 */
                function clearSearchAndSort(section) {
                    // Resetta i campi di input
                    const searchField = document.getElementById(`${section}SearchField`);
                    const searchInput = document.getElementById(`${section}SearchInput`);
                    
                    if (searchField) searchField.value = '';
                    if (searchInput) searchInput.value = '';
                    
                    // Ripristina i dati originali
                    if (section === 'dipendenti') {
                        updateDipendentiTable();
                    } else {
                        updateTable(section, tableDataCache[section]);
                    }
                    
                    sortState[section] = false;
                }  

                // Funzione per cambiare sezione
                function showSection(section) {
                    document.querySelectorAll('.content-section').forEach(el => el.classList.remove('active'));
                    document.getElementById(section + 'Content').classList.add('active');
                    
                    document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
                    document.querySelector(`.nav-item[data-section="${section}"]`).classList.add('active');
                    
                    // Carica i dati della sezione
                    loadData(section);
                }
                
                // Funzione per caricare i dati
                function loadData(section) {
                    // Per "meiOrdini" usa getOrdiniPersonali, per gli altri usa il nome della sezione
                    const action = section === 'meiOrdini' ? 'getOrdiniPersonali' : `get${section.charAt(0).toUpperCase() + section.slice(1)}`;
                    
                    fetch(`api.php?action=${action}`)
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                // Memorizza i dati nella cache
                                tableDataCache[section] = data.data;
                                
                                if(section === 'dipendenti') {
                                    dipendentiData = data.data;
                                    dipendentiCurrentPage = 1;
                                    updateDipendentiTable();
                                } else {
                                    updateTable(section, data.data);
                                }
                            }
                        })
                        .catch(error => console.error('Errore:', error));
                }
                
                // Funzione per aggiornare le tabelle
                function updateTable(section, data) {
                    const tableBody = document.getElementById(`${section}Table`);
                    if(!tableBody) return;
                    
                    tableBody.innerHTML = '';
                    
                    if(section === 'meiOrdini') {
                        data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.cliente}</td>
                                    <td>${item.data_ordine}</td>
                                    <td>${item.data_arrivo || '-'}</td>
                                    <td>
                                        <button class="btn-icon" onclick="window.location.href='add_order_products.php?order_id=${item.id}'" title="Aggiungi Prodotti">📦</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else if(section === 'ordini') {
                        data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.cliente}</td>
                                    <td>${item.data_ordine}</td>
                                    <td>${item.data_arrivo || '-'}</td>
                                    <td>${item.dipendente}</td>
                                    <td>
                                        <button class="btn-icon" onclick="window.location.href='add_order_products.php?order_id=${item.id}'" title="Aggiungi Prodotti">📦</button>
                                        <button class="btn-icon edit" onclick="editItem('ordini', ${item.id})">✏️</button>
                                        <button class="btn-icon delete" onclick="deleteItem('ordini', ${item.id})">🗑️</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else if(section === 'prodotti') {
                        data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.nome}</td>
                                    <td>${item.descrizione}</td>
                                    <td>${item.prezzo}</td>
                                    <td>
                                        <button class="btn-icon edit" onclick="editItem('prodotti', ${item.id})">✏️</button>
                                        <button class="btn-icon delete" onclick="deleteItem('prodotti', ${item.id})">🗑️</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else if(section === 'clienti') {
                        data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.nome_azienda}</td>
                                    <td>${item.indirizzo}</td>
                                    <td>${item.p_iva}</td>
                                    <td>${item.email}</td>
                                    <td>
                                        <button class="btn-icon edit" onclick="editItem('clienti', ${item.id})">✏️</button>
                                        <button class="btn-icon delete" onclick="deleteItem('clienti', ${item.id})">🗑️</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else if(section === 'magazzini') {
                        data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td><a href="prodotti_magazzini.php?idMagazzino=${item.id}" style="color: #2196F3; cursor: pointer; text-decoration: underline;">${item.id}</a></td>
                                    <td>${item.indirizzo}</td>
                                    <td>${item.descrizione}</td>
                                    <td>
                                        <button class="btn-icon edit" onclick="editItem('magazzini', ${item.id})">✏️</button>
                                        <button class="btn-icon delete" onclick="deleteItem('magazzini', ${item.id})">🗑️</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else if(section === 'filiali') {
                        data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.indirizzo}</td>
                                    <td>${item.tipo}</td>
                                    <td>${item.recapito}</td>
                                    <td>
                                        <button class="btn-icon edit" onclick="editItem('filiali', ${item.id})">✏️</button>
                                        <button class="btn-icon delete" onclick="deleteItem('filiali', ${item.id})">🗑️</button>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    
                    // Aggiorna dashboard stats
                    if(section === 'dashboard') {
                        updateDashboardStats();
                    }
                }
                
                // Funzione per aggiornare la tabella dipendenti con paginazione
                function updateDipendentiTable() {
                    const tableBody = document.getElementById('dipendentiTable');
                    const pagination = document.getElementById('dipendentiPagination');
                    const pageInfo = document.getElementById('dipendentiPageInfo');
                    
                    tableBody.innerHTML = '';
                    
                    // Usa i dati in cache (possono essere filtrati)
                    const dataToDisplay = dipendentiData.length > 0 ? dipendentiData : tableDataCache.dipendenti;
                    
                    const start = (dipendentiCurrentPage - 1) * dipendentiPerPage;
                    const end = start + dipendentiPerPage;
                    const pageData = dataToDisplay.slice(start, end);
                    
                    pageData.forEach(item => {
                        tableBody.innerHTML += `
                            <tr>
                                <td>${item.id_dipendente}</td>
                                <td>${item.nome}</td>
                                <td>${item.cognome}</td>
                                <td>${item.posizione}</td>
                                <td>${item.filiale}</td>
                                <td>
                                    <button class="btn-icon edit" onclick="editItem('dipendenti', ${item.id_dipendente})">✏️</button>
                                    <button class="btn-icon delete" onclick="deleteItem('dipendenti', ${item.id_dipendente})">🗑️</button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    const totalPages = Math.ceil(dataToDisplay.length / dipendentiPerPage);
                    pageInfo.textContent = `Pagina ${dipendentiCurrentPage} di ${totalPages}`;
                    
                    if(totalPages > 1) {
                        pagination.style.display = 'flex';
                    } else {
                        pagination.style.display = 'none';
                    }
                }
                
                // Funzione per cambiare pagina
                function changePage(section, direction) {
                    if(section === 'dipendenti') {
                        const totalPages = Math.ceil(dipendentiData.length / dipendentiPerPage);
                        if(direction === 'next' && dipendentiCurrentPage < totalPages) {
                            dipendentiCurrentPage++;
                        } else if(direction === 'prev' && dipendentiCurrentPage > 1) {
                            dipendentiCurrentPage--;
                        }
                        updateDipendentiTable();
                    }
                }
                
                // Funzione per mostrare il form di aggiunta/modifica
                function showForm(section, id = null) {
                    const formContainer = document.getElementById(`${section}Form`);
                    if(!formContainer) return;
                    
                    if(id) {
                        // Carica i dati per la modifica
                        // Per meiOrdini, usa getOrdini (non getMeiOrdini)
                        const actualSection = section === 'meiOrdini' ? 'ordini' : section;
                        fetch(`api.php?action=get${actualSection.charAt(0).toUpperCase() + actualSection.slice(1)}&id=${id}`)
                            .then(response => response.json())
                            .then(data => {
                                if(data.success) {
                                    renderForm(section, data.data, id);
                                }
                            });
                    } else {
                        renderForm(section, null, null);
                    }
                    
                    formContainer.style.display = 'block';
                }

                function add_employee() {
                    showForm('dipendenti');
                }
                
                // Funzione per renderizzare il form
                function renderForm(section, data, id) {
                    const formContainer = document.getElementById(`${section}Form`);
                    if(section === 'ordini' || section === 'meiOrdini') {
                        // Carica clienti, dipendenti e prodotti per i select
                        Promise.all([
                            fetch('api.php?action=getClienti').then(r => r.json()),
                            fetch('api.php?action=getDipendenti').then(r => r.json()),
                            fetch('api.php?action=getProdotti').then(r => r.json())
                        ]).then(([clientiRes, dipendentiRes, prodottiRes]) => {
                            if(clientiRes.success && dipendentiRes.success) {
                                const clientiOptions = clientiRes.data.map(c => `<option value="${c.id}" ${data && data.id_cliente == c.id ? 'selected' : ''}>${c.nome_azienda}</option>`).join('');
                                formContainer.innerHTML = `
                                    <h3>${id ? 'Modifica' : 'Nuovo'} Ordine</h3>
                                    <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                        <div class="form-group">
                                            <label>Cliente</label>
                                            <select name="ID_Cliente" required>
                                                <option value="">Seleziona Cliente</option>
                                                ${clientiOptions}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Data Ordine</label>
                                            <input type="date" name="Data_Ordine" value="${data ? data.data_ordine : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Data Arrivo</label>
                                            <input type="date" name="Data_Arrivo" value="${data ? data.data_arrivo : ''}">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn-success">Salva</button>
                                            <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                        </div>
                                    </form>
                                `;
                            }
                        });
                    } else if(section === 'prodotti') {
                        formContainer.innerHTML = `
                            <h3>${id ? 'Modifica' : 'Nuovo'} Prodotto</h3>
                            <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" name="Nome" value="${data ? data.nome : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Descrizione</label>
                                    <input type="text" name="Descrizione" value="${data ? data.descrizione : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Prezzo</label>
                                    <input type="text" name="Prezzo" value="${data ? data.prezzo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-success">Salva</button>
                                    <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                </div>
                            </form>
                        `;
                    } else if(section === 'clienti') {
                        formContainer.innerHTML = `
                            <h3>${id ? 'Modifica' : 'Nuovo'} Cliente</h3>
                            <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                <div class="form-group">
                                    <label>Nome Azienda</label>
                                    <input type="text" name="Nome_Azienda" value="${data ? data.nome_azienda : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Indirizzo</label>
                                    <input type="text" name="Indirizzo" value="${data ? data.indirizzo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Partita IVA</label>
                                    <input type="text" name="P_IVA" value="${data ? data.p_iva : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="Email" value="${data ? data.email : ''}" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-success">Salva</button>
                                    <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                </div>
                            </form>
                        `;
                    } else if(section === 'magazzini') {
                        formContainer.innerHTML = `
                            <h3>${id ? 'Modifica' : 'Nuovo'} Magazzino</h3>
                            <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                <div class="form-group">
                                    <label>Indirizzo</label>
                                    <input type="text" name="Indirizzo" value="${data ? data.indirizzo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Descrizione</label>
                                    <input type="text" name="Desc_Magazzino" value="${data ? data.descrizione : ''}" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-success">Salva</button>
                                    <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                </div>
                            </form>
                        `;
                    } else if(section === 'filiali') {
                        formContainer.innerHTML = `
                            <h3>${id ? 'Modifica' : 'Nuova'} Filiale</h3>
                            <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                <div class="form-group">
                                    <label>Indirizzo</label>
                                    <input type="text" name="Indirizzo" value="${data ? data.indirizzo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select name="Tipo" required>
                                        <option value="">Seleziona Tipo</option>
                                        <option value="prod" ${data && data.tipo === 'prod' ? 'selected' : ''}>Produzione</option>
                                        <option value="vendita" ${data && data.tipo === 'vendita' ? 'selected' : ''}>Vendita</option>
                                        <option value="misto" ${data && data.tipo === 'misto' ? 'selected' : ''}>Misto</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Recapito Telefonico</label>
                                    <input type="text" name="Recapito_Telefonico" value="${data ? data.recapito : ''}" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-success">Salva</button>
                                    <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                </div>
                            </form>
                        `;
                    } else if(section === 'dipendenti') {
                        // Carica filiali per il select
                        fetch('api.php?action=getFiliali').then(r => r.json()).then(filialiRes => {
                            if(filialiRes.success) {
                                const filialiOptions = filialiRes.data.map(f => `<option value="${f.id}" ${data && data.id_filiale == f.id ? 'selected' : ''}>${f.indirizzo}</option>`).join('');
                                
                                formContainer.innerHTML = `
                                    <h3>${id ? 'Modifica' : 'Nuovo'} Dipendente</h3>
                                    <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                        <div class="form-group">
                                            <label>Filiale</label>
                                            <select name="ID_Filiale" required>
                                                <option value="">Seleziona Filiale</option>
                                                ${filialiOptions}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" name="Username" value="${data ? data.username : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password ${id ? '(lascia vuoto per non cambiare)' : ''}</label>
                                            <input type="password" name="Pswd" id="pswd_dipendenti" ${id ? '' : 'required'}>
                                        </div>
                                        <div class="form-group">
                                            <label>Conferma Password ${id ? '(lascia vuoto per non cambiare)' : ''}</label>
                                            <input type="password" name="ConfirmPswd" id="confirm_pswd_dipendenti" ${id ? '' : 'required'}>
                                        </div>
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <input type="text" name="Nome" value="${data ? data.nome : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Cognome</label>
                                            <input type="text" name="Cognome" value="${data ? data.cognome : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="Email" value="${data ? data.email : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Data Assunzione</label>
                                            <input type="date" name="Data_Assunzione" value="${data ? data.data_assunzione : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Stipendio</label>
                                            <input type="number" step="0.01" name="Stipendio" value="${data ? data.stipendio : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>IBAN</label>
                                            <input type="text" name="IBAN" value="${data ? data.iban : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Posizione</label>
                                            <input type="text" name="Tipo" value="${data ? data.posizione : ''}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Admin</label>
                                            <input type="checkbox" name="Is_admin" value="1" ${data && data.is_admin ? 'checked' : ''}>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn-success">Salva</button>
                                            <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                        </div>
                                    </form>
                                `;
                            }
                        });
                    }
                }
                
                // Funzione per salvare l'elemento
                function saveItem(event, section, id) {
                    event.preventDefault();
                    const form = event.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    
                    // Valida le password per i dipendenti
                    if(section === 'dipendenti') {
                        const pswd = document.getElementById('pswd_dipendenti')?.value || '';
                        const confirmPswd = document.getElementById('confirm_pswd_dipendenti')?.value || '';
                        
                        // Se è una modifica, almeno uno dei campi deve essere vuoto (non obbligatori) o corrispondenti
                        // Se è una nuova aggiunta, entrambi devono essere compilati
                        if(pswd || confirmPswd) {
                            if(pswd !== confirmPswd) {
                                alert('✗ Le password non corrispondono!');
                                return;
                            }
                        } else if(!id) {
                            alert('✗ Inserisci una password!');
                            return;
                        }
                    }
                    
                    // Disabilita il bottone durante l'invio
                    if(submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerText = 'Salvataggio...';
                    }
                    
                    const formData = new FormData(form);
                    // Per meiOrdini, usa sempre addOrdini/updateOrdini (non addMeiOrdini)
                    const actualSection = section === 'meiOrdini' ? 'ordini' : section;
                    formData.append('action', id ? `update${actualSection.charAt(0).toUpperCase() + actualSection.slice(1)}` : `add${actualSection.charAt(0).toUpperCase() + actualSection.slice(1)}`);
                    if(id) formData.append('id', id);
                    
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
                            // Se è un nuovo ordine, reindirizza a add_order_products.php
                            if((section === 'ordini' || section === 'meiOrdini') && !id && data.id) {
                                alert('✓ Ordine creato con successo!');
                                window.location.href = `add_order_products.php?order_id=${data.id}`;
                            } else {
                                hideForm(section);
                                loadData(section);
                                updateDashboardStats(); // Aggiorna le statistiche della homepage
                                alert('✓ Elemento salvato con successo!');
                            }
                        } else {
                            alert('✗ Errore: ' + (data.message || 'Errore sconosciuto'));
                            if(submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerText = 'Salva';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Dettagli errore:', error);
                        alert('✗ Errore nella comunicazione con il server: ' + error.message);
                        if(submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerText = 'Salva';
                        }
                    });
                }
                
                // Funzione per modificare un elemento
                function editItem(section, id) {
                    showForm(section, id);
                }
                
                // Funzione per eliminare un elemento
                function deleteItem(section, id) {
                    if(confirm('Sei sicuro di voler eliminare questo elemento?')) {
                        const formData = new FormData();
                        formData.append('action', `delete${section.charAt(0).toUpperCase() + section.slice(1)}`);
                        formData.append('id', id);
                        
                        fetch('api.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                loadData(section);
                                updateDashboardStats(); // Aggiorna le statistiche della homepage
                                if(section === 'dashboard') loadData('dashboard');
                            } else {
                                alert('Errore: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Errore:', error));
                    }
                }
                
                // Funzione per nascondere il form
                function hideForm(section) {
                    const formContainer = document.getElementById(`${section}Form`);
                    if(formContainer) {
                        formContainer.style.display = 'none';
                        formContainer.innerHTML = '';
                    }
                }
                
                // Funzione per aggiornare le statistiche della dashboard
                function updateDashboardStats() {
                    fetch('api.php?action=getDashboardStats')
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                document.getElementById('totalOrdini').innerText = data.data.totalOrdini;
                                document.getElementById('totalProdotti').innerText = data.data.totalProdotti;
                                document.getElementById('totalClienti').innerText = data.data.totalClienti;
                                document.getElementById('totalMagazzini').innerText = data.data.totalMagazzini;
                                document.getElementById('totalFiliali').innerText = data.data.totalFiliali;
                                document.getElementById('totalDipendenti').innerText = data.data.totalDipendenti;
                            }
                        })
                        .catch(error => console.error('Errore:', error));
                }
                
                // Funzione di logout
                function logout() {
                    if(confirm('Sei sicuro di voler uscire?')) {
                        alert('Logout effettuato con successo!');
                        // Qui puoi aggiungere la reindirizzazione alla pagina di login
                        window.location.href = 'logout.php';
                    }
                }
                
                // Carica i dati iniziali
                document.addEventListener('DOMContentLoaded', () => {
                    updateDashboardStats();
                    // Mostra notifica se presente
                    <?php if ($message): ?>
                        document.getElementById('notification').style.display = 'block';
                        setTimeout(() => {
                            document.getElementById('notification').style.display = 'none';
                        }, 5000);
                    <?php endif; ?>
                });
            </script>
        </body>
        </html>
