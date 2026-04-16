<?php
require_once 'login.php';
session_set_cookie_params(0, "/");
session_start();

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
    <link rel="stylesheet" href="../stile.css">
    
</head>
<body>
    <div class="container"><!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestionale Aziendale</title>
            <link rel="stylesheet" href="stile.css">
        </head>
        <body>
            <div class="container">
                <!-- Header -->
                <header class="header">
                    <div class="logo">
                        <h1>Gestionale Aziendale</h1>
                    </div>
                    <div class="user-info">
                        <span>Benvenuto, Amministratore</span>
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
                            <li class="nav-item" data-section="ordini">
                                <a href="#" onclick="showSection('ordini')">Ordini</a>
                            </li>
                            <li class="nav-item" data-section="prodotti">
                                <a href="#" onclick="showSection('prodotti')">Prodotti</a>
                            </li>
                            <li class="nav-item" data-section="clienti">
                                <a href="#" onclick="showSection('clienti')">Clienti</a>
                            </li>
                            <li class="nav-item" data-section="magazzini">
                                <a href="#" onclick="showSection('magazzini')">Magazzini</a>
                            </li>
                            <li class="nav-item" data-section="filiali">
                                <a href="#" onclick="showSection('filiali')">Filiali</a>
                            </li>
                            <li class="nav-item" data-section="spedizioni">
                                <a href="#" onclick="showSection('spedizioni')">Spedizioni</a>
                            </li>
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
                            </div>
                            
                            <div class="recent-orders">
                                <h3>Ordini Recenti</h3>
                                <div class="table-responsive">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID Ordine</th>
                                                <th>Cliente</th>
                                                <th>Data</th>
                                                <th>Stato</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentOrdersTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
        
                        <!-- Ordini -->
                        <div id="ordiniContent" class="content-section">
                            <div class="section-header">
                                <h2>Gestione Ordini</h2>
                                <button class="btn-primary" onclick="showForm('ordini')">Nuovo Ordine</button>
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
                                            <th>Prodotti</th>
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
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Descrizione</th>
                                            <th>Magazzino</th>
                                            <th>Quantità</th>
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
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Indirizzo</th>
                                            <th>Descrizione</th>
                                            <th>Numero Prodotti</th>
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
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Indirizzo</th>
                                            <th>Tipo</th>
                                            <th>Recapito</th>
                                            <th>Magazzino Collegato</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filialiTable">
                                    </tbody>
                                </table>
                            </div>
                            <div id="filialiForm" class="form-container" style="display: none;"></div>
                        </div>
        
                        <!-- Spedizioni -->
                        <div id="spedizioniContent" class="content-section">
                            <div class="section-header">
                                <h2>Spedizioni Magazzino → Filiale</h2>
                                <button class="btn-primary" onclick="showForm('spedizioni')">Nuova Spedizione</button>
                            </div>
                            
                            <div class="spedizioni-list">
                                <h3>Spedizioni in corso</h3>
                                <div class="table-responsive">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID Spedizione</th>
                                                <th>Data</th>
                                                <th>Magazzino Origine</th>
                                                <th>Filiale Destinazione</th>
                                                <th>Prodotti</th>
                                                <th>Stato</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody id="spedizioniTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="spedizioniForm" class="form-container" style="display: none;"></div>
                        </div>
                    </main>
                </section>
            </div>
        
            <script>
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
                    fetch(`api.php?action=get${section.charAt(0).toUpperCase() + section.slice(1)}`)
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                updateTable(section, data.data);
                            }
                        })
                        .catch(error => console.error('Errore:', error));
                }
                
                // Funzione per aggiornare le tabelle
                function updateTable(section, data) {
                    const tableBody = document.getElementById(`${section}Table`);
                    if(!tableBody) return;
                    
                    tableBody.innerHTML = '';
                    
                    if(section === 'ordini') {
                        data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.cliente}</td>
                                    <td>${item.data_ordine}</td>
                                    <td>${item.data_arrivo || '-'}</td>
                                    <td>${item.dipendente}</td>
                                    <td>${item.prodotti}</td>
                                    <td>
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
                                    <td>${item.descrizione}</td>
                                    <td>${item.magazzino}</td>
                                    <td>${item.quantita}</td>
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
                                    <td>${item.id}</td>
                                    <td>${item.indirizzo}</td>
                                    <td>${item.descrizione}</td>
                                    <td>${item.numero_prodotti}</td>
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
                                    <td>${item.magazzino_collegato}</td>
                                    <td>
                                        <button class="btn-icon edit" onclick="editItem('filiali', ${item.id})">✏️</button>
                                        <button class="btn-icon delete" onclick="deleteItem('filiali', ${item.id})">🗑️</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else if(section === 'spedizioni') {
                        data.forEach(item => {
                            const badgeClass = item.stato === 'Consegnato' ? 'success' : 'warning';
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.data}</td>
                                    <td>${item.magazzino_origine}</td>
                                    <td>${item.filiale_destinazione}</td>
                                    <td>${item.prodotti}</td>
                                    <td><span class="badge ${badgeClass}">${item.stato}</span></td>
                                    <td>
                                        <button class="btn-icon edit" onclick="editItem('spedizioni', ${item.id})">✏️</button>
                                        <button class="btn-icon delete" onclick="deleteItem('spedizioni', ${item.id})">🗑️</button>
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
                
                // Funzione per mostrare il form di aggiunta/modifica
                function showForm(section, id = null) {
                    const formContainer = document.getElementById(`${section}Form`);
                    if(!formContainer) return;
                    
                    if(id) {
                        // Carica i dati per la modifica
                        fetch(`api.php?action=get${section.charAt(0).toUpperCase() + section.slice(1)}&id=${id}`)
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
                
                // Funzione per renderizzare il form
                function renderForm(section, data, id) {
                    const formContainer = document.getElementById(`${section}Form`);
                    
                    if(section === 'ordini') {
                        formContainer.innerHTML = `
                            <h3>${id ? 'Modifica' : 'Nuovo'} Ordine</h3>
                            <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <input type="text" name="cliente" value="${data ? data.cliente : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Data Ordine</label>
                                    <input type="date" name="data_ordine" value="${data ? data.data_ordine : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Data Arrivo</label>
                                    <input type="date" name="data_arrivo" value="${data ? data.data_arrivo : ''}">
                                </div>
                                <div class="form-group">
                                    <label>Dipendente</label>
                                    <input type="text" name="dipendente" value="${data ? data.dipendente : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Numero Prodotti</label>
                                    <input type="number" name="prodotti" value="${data ? data.prodotti : ''}" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-success">Salva</button>
                                    <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                </div>
                            </form>
                        `;
                    } else if(section === 'prodotti') {
                        formContainer.innerHTML = `
                            <h3>${id ? 'Modifica' : 'Nuovo'} Prodotto</h3>
                            <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                <div class="form-group">
                                    <label>Descrizione</label>
                                    <input type="text" name="descrizione" value="${data ? data.descrizione : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Magazzino</label>
                                    <input type="text" name="magazzino" value="${data ? data.magazzino : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Quantità</label>
                                    <input type="number" name="quantita" value="${data ? data.quantita : ''}" required>
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
                                    <input type="text" name="nome_azienda" value="${data ? data.nome_azienda : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Indirizzo</label>
                                    <input type="text" name="indirizzo" value="${data ? data.indirizzo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Partita IVA</label>
                                    <input type="text" name="p_iva" value="${data ? data.p_iva : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="${data ? data.email : ''}" required>
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
                                    <input type="text" name="indirizzo" value="${data ? data.indirizzo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Descrizione</label>
                                    <input type="text" name="descrizione" value="${data ? data.descrizione : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Numero Prodotti</label>
                                    <input type="number" name="numero_prodotti" value="${data ? data.numero_prodotti : ''}" required>
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
                                    <input type="text" name="indirizzo" value="${data ? data.indirizzo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <input type="text" name="tipo" value="${data ? data.tipo : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Recapito</label>
                                    <input type="text" name="recapito" value="${data ? data.recapito : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Magazzino Collegato</label>
                                    <input type="text" name="magazzino_collegato" value="${data ? data.magazzino_collegato : ''}" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-success">Salva</button>
                                    <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                </div>
                            </form>
                        `;
                    } else if(section === 'spedizioni') {
                        formContainer.innerHTML = `
                            <h3>${id ? 'Modifica' : 'Nuova'} Spedizione</h3>
                            <form onsubmit="saveItem(event, '${section}', ${id || 'null'})">
                                <div class="form-group">
                                    <label>Data</label>
                                    <input type="date" name="data" value="${data ? data.data : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Magazzino Origine</label>
                                    <input type="text" name="magazzino_origine" value="${data ? data.magazzino_origine : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Filiale Destinazione</label>
                                    <input type="text" name="filiale_destinazione" value="${data ? data.filiale_destinazione : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Numero Prodotti</label>
                                    <input type="number" name="prodotti" value="${data ? data.prodotti : ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Stato</label>
                                    <select name="stato" required>
                                        <option value="In transito" ${data && data.stato === 'In transito' ? 'selected' : ''}>In transito</option>
                                        <option value="Consegnato" ${data && data.stato === 'Consegnato' ? 'selected' : ''}>Consegnato</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-success">Salva</button>
                                    <button type="button" class="btn-cancel" onclick="hideForm('${section}')">Annulla</button>
                                </div>
                            </form>
                        `;
                    }
                }
                
                // Funzione per salvare l'elemento
                function saveItem(event, section, id) {
                    event.preventDefault();
                    const form = event.target;
                    const formData = new FormData(form);
                    formData.append('action', id ? `update${section.charAt(0).toUpperCase() + section.slice(1)}` : `add${section.charAt(0).toUpperCase() + section.slice(1)}`);
                    if(id) formData.append('id', id);
                    
                    fetch('api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            hideForm(section);
                            loadData(section);
                            if(section === 'dashboard') loadData('dashboard');
                        } else {
                            alert('Errore: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Errore:', error));
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
                                
                                // Aggiorna ordini recenti
                                const recentOrdersTable = document.getElementById('recentOrdersTable');
                                recentOrdersTable.innerHTML = '';
                                data.data.recentOrders.forEach(order => {
                                    const badgeClass = order.stato === 'Completato' ? 'success' : 'warning';
                                    recentOrdersTable.innerHTML += `
                                        <tr>
                                            <td>#${order.id}</td>
                                            <td>${order.cliente}</td>
                                            <td>${order.data}</td>
                                            <td><span class="badge ${badgeClass}">${order.stato}</span></td>
                                        </tr>
                                    `;
                                });
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
                    loadData('dashboard');
                });
            </script>
        </body>
        </html>
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <h1>Gestionale Aziendale</h1>
            </div>
            <div class="user-info">
                <span>Benvenuto, Amministratore</span>
                <button class="btn-logout">Logout</button>
            </div>
        </header>

        <!-- Dashboard Section -->
        <section class="dashboard-section">
            <!-- Menu di navigazione -->
            <nav class="sidebar">
                <ul class="nav-menu">
                    <li class="nav-item active" data-section="dashboard">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="nav-item" data-section="ordini">
                        <a href="#">Ordini</a>
                    </li>
                    <li class="nav-item" data-section="prodotti">
                        <a href="#">Prodotti</a>
                    </li>
                    <li class="nav-item" data-section="clienti">
                        <a href="#">Clienti</a>
                    </li>
                    <li class="nav-item" data-section="magazzini">
                        <a href="#">Magazzini</a>
                    </li>
                    <li class="nav-item" data-section="filiali">
                        <a href="#">Filiali</a>
                    </li>
                    <li class="nav-item" data-section="spedizioni">
                        <a href="#">Spedizioni</a>
                    </li>
                </ul>
            </nav>

            <!-- Contenuto principale -->
            <main class="content">
                <!-- Dashboard -->
                <div id="dashboardContent" class="content-section active">
                    <h2>Dashboard</h2>
                    <div class="stats-cards">
                        <div class="stat-card">
                            <h3>Ordini Totali</h3>
                            <p class="stat-number">158</p>
                        </div>
                        <div class="stat-card">
                            <h3>Prodotti</h3>
                            <p class="stat-number">45</p>
                        </div>
                        <div class="stat-card">
                            <h3>Clienti</h3>
                            <p class="stat-number">23</p>
                        </div>
                        <div class="stat-card">
                            <h3>Magazzini</h3>
                            <p class="stat-number">3</p>
                        </div>
                    </div>
                    
                    <div class="recent-orders">
                        <h3>Ordini Recenti</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID Ordine</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#101</td>
                                    <td>Rossi SRL</td>
                                    <td>2024-03-15</td>
                                    <td><span class="badge success">Completato</span></td>
                                </tr>
                                <tr>
                                    <td>#102</td>
                                    <td>Bianchi SpA</td>
                                    <td>2024-03-14</td>
                                    <td><span class="badge warning">In lavorazione</span></td>
                                </tr>
                                <tr>
                                    <td>#103</td>
                                    <td>Verdi & C.</td>
                                    <td>2024-03-13</td>
                                    <td><span class="badge success">Completato</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ordini -->
                <div id="ordiniContent" class="content-section">
                    <div class="section-header">
                        <h2>Gestione Ordini</h2>
                        <button class="btn-primary">Nuovo Ordine</button>
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
                                    <th>Prodotti</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>101</td>
                                    <td>Rossi SRL</td>
                                    <td>2024-03-15</td>
                                    <td>2024-03-20</td>
                                    <td>Mario Rossi</td>
                                    <td>3</td>
                                    <td>
                                        <button class="btn-icon view">👁️</button>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>102</td>
                                    <td>Bianchi SpA</td>
                                    <td>2024-03-14</td>
                                    <td>-</td>
                                    <td>Laura Bianchi</td>
                                    <td>5</td>
                                    <td>
                                        <button class="btn-icon view">👁️</button>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>103</td>
                                    <td>Verdi & C.</td>
                                    <td>2024-03-13</td>
                                    <td>2024-03-18</td>
                                    <td>Giuseppe Verdi</td>
                                    <td>2</td>
                                    <td>
                                        <button class="btn-icon view">👁️</button>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Prodotti -->
                <div id="prodottiContent" class="content-section">
                    <div class="section-header">
                        <h2>Gestione Prodotti</h2>
                        <button class="btn-primary">Nuovo Prodotto</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descrizione</th>
                                    <th>Magazzino</th>
                                    <th>Quantità</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Prodotto A</td>
                                    <td>Magazzino Centrale</td>
                                    <td>150</td>
                                    <td>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Prodotto B</td>
                                    <td>Magazzino Nord</td>
                                    <td>80</td>
                                    <td>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Prodotto C</td>
                                    <td>Magazzino Sud</td>
                                    <td>200</td>
                                    <td>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Clienti -->
                <div id="clientiContent" class="content-section">
                    <div class="section-header">
                        <h2>Gestione Clienti</h2>
                        <button class="btn-primary">Nuovo Cliente</button>
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
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Rossi SRL</td>
                                    <td>Via Roma 1, Milano</td>
                                    <td>12345678901</td>
                                    <td>info@rossi.it</td>
                                    <td>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Bianchi SpA</td>
                                    <td>Via Milano 10, Torino</td>
                                    <td>98765432109</td>
                                    <td>info@bianchi.it</td>
                                    <td>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Verdi & C.</td>
                                    <td>Piazza Dante 5, Bologna</td>
                                    <td>45678901234</td>
                                    <td>info@verdi.it</td>
                                    <td>
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon delete">🗑️</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Magazzini -->
                <div id="magazziniContent" class="content-section">
                    <h2>Gestione Magazzini</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Indirizzo</th>
                                    <th>Descrizione</th>
                                    <th>Numero Prodotti</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Via Industria 5, Milano</td>
                                    <td>Magazzino Centrale</td>
                                    <td>45</td>
                                    <td><button class="btn-icon view">👁️</button></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Via Roma 23, Torino</td>
                                    <td>Magazzino Nord</td>
                                    <td>30</td>
                                    <td><button class="btn-icon view">👁️</button></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Via Napoli 15, Roma</td>
                                    <td>Magazzino Sud</td>
                                    <td>28</td>
                                    <td><button class="btn-icon view">👁️</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Filiali -->
                <div id="filialiContent" class="content-section">
                    <h2>Gestione Filiali</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Indirizzo</th>
                                    <th>Tipo</th>
                                    <th>Recapito</th>
                                    <th>Magazzino Collegato</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Via Garibaldi 10, Milano</td>
                                    <td>Vendita</td>
                                    <td>02 1234567</td>
                                    <td>Magazzino Centrale</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Corso Italia 5, Torino</td>
                                    <td>Vendita</td>
                                    <td>011 7654321</td>
                                    <td>Magazzino Nord</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Via Veneto 20, Roma</td>
                                    <td>Logistica</td>
                                    <td>06 9876543</td>
                                    <td>Magazzino Sud</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Spedizioni -->
                <div id="spedizioniContent" class="content-section">
                    <div class="section-header">
                        <h2>Spedizioni Magazzino → Filiale</h2>
                        <button class="btn-primary">Nuova Spedizione</button>
                    </div>
                    
                    <div class="spedizioni-list">
                        <h3>Spedizioni in corso</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID Spedizione</th>
                                    <th>Data</th>
                                    <th>Magazzino Origine</th>
                                    <th>Filiale Destinazione</th>
                                    <th>Prodotti</th>
                                    <th>Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>S001</td>
                                    <td>2024-03-15</td>
                                    <td>Magazzino Centrale</td>
                                    <td>Filiale Milano</td>
                                    <td>3</td>
                                    <td><span class="badge warning">In transito</span></td>
                                </tr>
                                <tr>
                                    <td>S002</td>
                                    <td>2024-03-14</td>
                                    <td>Magazzino Nord</td>
                                    <td>Filiale Torino</td>
                                    <td>5</td>
                                    <td><span class="badge success">Consegnato</span></td>
                                </tr>
                                <tr>
                                    <td>S003</td>
                                    <td>2024-03-13</td>
                                    <td>Magazzino Sud</td>
                                    <td>Filiale Roma</td>
                                    <td>2</td>
                                    <td><span class="badge success">Consegnato</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </section>
    </div>
</body>
</html>
