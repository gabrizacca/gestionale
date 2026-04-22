<?php
require_once 'login.php';
session_set_cookie_params(0, "/");
session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}

$order_id = $_GET['order_id'] ?? null;
if(!$order_id) {
    die("Ordine non specificato.");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Prodotti all'Ordine</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        .products-list { margin-top: 30px; }
        .product-item { background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }
        .availability { font-size: 0.9em; margin-top: 5px; color: #666; }
        .availability.valid { color: green; }
        .availability.invalid { color: red; }
        .btn-danger { background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .btn-danger:hover { background-color: #c82333; }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="logo"><h1>Aggiungi Prodotti - Ordine #<?php echo htmlspecialchars($order_id); ?></h1></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['nome'] . ' ' . $_SESSION['cognome']); ?></span>
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </header>
    <section class="dashboard-section">
        <main class="content">
            <div class="back-button">
                <a href="dashboard.php" class="btn-secondary">← Torna alla Dashboard</a>
            </div>

            <!-- Form per aggiungere prodotto -->
            <div class="form-container">
                <h3>Aggiungi prodotto all'ordine</h3>
                <form id="addProductForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Prodotto *</label>
                            <select id="product_id" required>
                                <option value="">Seleziona prodotto</option>
                                <!-- popolato via JS -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Magazzino *</label>
                            <select id="warehouse_id" required disabled>
                                <option value="">Prima seleziona un prodotto</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantità *</label>
                            <input type="number" id="quantity" min="1" step="1" required>
                            <div id="availabilityMsg" class="availability"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-success" id="submitBtn">Aggiungi</button>
                        <button type="button" class="btn-cancel" onclick="resetForm()">Cancella</button>
                    </div>
                </form>
            </div>

            <!-- Lista prodotti già aggiunti -->
            <div class="products-list">
                <h3>Prodotti già aggiunti all'ordine</h3>
                <div id="productsList"></div>
            </div>
        </main>
    </section>
</div>

<script>
    const orderId = <?php echo json_encode($order_id); ?>;
    let currentAvailability = { available: 0, valid: false };

    // Logout
    function logout() { window.location.href = 'logout.php'; }

    // Carica lista prodotti disponibili
    function loadProducts() {
        fetch('api.php?action=getProdotti')
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    const select = document.getElementById('product_id');
                    select.innerHTML = '<option value="">Seleziona prodotto</option>';
                    data.data.forEach(p => {
                        select.innerHTML += `<option value="${p.id}">${p.nome}</option>`;
                    });
                }
            });
    }

    // Quando cambia il prodotto, carica i magazzini che lo hanno
    document.getElementById('product_id').addEventListener('change', function() {
        const prodId = this.value;
        const warehouseSelect = document.getElementById('warehouse_id');
        const quantityInput = document.getElementById('quantity');
        const availabilityMsg = document.getElementById('availabilityMsg');
        
        if(!prodId) {
            warehouseSelect.innerHTML = '<option value="">Prima seleziona un prodotto</option>';
            warehouseSelect.disabled = true;
            quantityInput.value = '';
            availabilityMsg.innerHTML = '';
            currentAvailability.valid = false;
            return;
        }
        
        fetch(`api.php?action=getWarehousesForProduct&id_prodotto=${prodId}`)
            .then(r => r.json())
            .then(data => {
                if(data.success && data.data.length > 0) {
                    warehouseSelect.disabled = false;
                    warehouseSelect.innerHTML = '<option value="">Seleziona magazzino</option>';
                    data.data.forEach(w => {
                        warehouseSelect.innerHTML += `<option value="${w.id}" data-qty="${w.quantita}">${w.indirizzo} (disp. ${w.quantita})</option>`;
                    });
                    // Reset availability
                    availabilityMsg.innerHTML = '';
                    currentAvailability.valid = false;
                } else {
                    warehouseSelect.innerHTML = '<option value="">Nessun magazzino con scorte</option>';
                    warehouseSelect.disabled = true;
                    availabilityMsg.innerHTML = '<span class="invalid">Prodotto non disponibile in nessun magazzino</span>';
                    currentAvailability.valid = false;
                }
            });
    });

    // Controlla disponibilità quando cambia magazzino o quantità
    function checkAvailability() {
        const warehouseSelect = document.getElementById('warehouse_id');
        const quantity = parseInt(document.getElementById('quantity').value);
        const availabilityMsg = document.getElementById('availabilityMsg');
        
        if(!warehouseSelect.value || isNaN(quantity) || quantity <= 0) {
            availabilityMsg.innerHTML = '';
            currentAvailability.valid = false;
            return;
        }
        
        const selectedOption = warehouseSelect.options[warehouseSelect.selectedIndex];
        const availableQty = parseInt(selectedOption.getAttribute('data-qty'));
        
        if(quantity > availableQty) {
            availabilityMsg.innerHTML = `<span class="invalid">❌ Prodotti insufficienti in magazzino. Disponibili: ${availableQty}</span>`;
            currentAvailability.valid = false;
        } else {
            availabilityMsg.innerHTML = `<span class="valid">✅ Disponibile: ${availableQty}</span>`;
            currentAvailability.valid = true;
        }
    }
    
    document.getElementById('warehouse_id').addEventListener('change', checkAvailability);
    document.getElementById('quantity').addEventListener('input', checkAvailability);

    // Submit del form
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if(!currentAvailability.valid) {
            alert("Controlla la disponibilità del prodotto nel magazzino selezionato.");
            return;
        }
        
        const productId = document.getElementById('product_id').value;
        const warehouseId = document.getElementById('warehouse_id').value;
        const quantity = document.getElementById('quantity').value;
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Salvataggio...';
        
        const formData = new FormData();
        formData.append('action', 'addOrderProduct');
        formData.append('id_ordine', orderId);
        formData.append('id_prodotto', productId);
        formData.append('id_magazzino', warehouseId);
        formData.append('quantita', quantity);
        
        fetch('api.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('✓ Prodotto aggiunto con successo!');
                    resetForm();
                    loadOrderProducts();
                    if(confirm('Vuoi aggiungere un altro prodotto a questo ordine?')) {
                        // reset solo i campi, mantieni ordine
                        document.getElementById('product_id').value = '';
                        document.getElementById('warehouse_id').innerHTML = '<option value="">Prima seleziona un prodotto</option>';
                        document.getElementById('warehouse_id').disabled = true;
                        document.getElementById('quantity').value = '';
                        document.getElementById('availabilityMsg').innerHTML = '';
                        currentAvailability.valid = false;
                    } else {
                        window.location.href = 'dashboard.php';
                    }
                } else {
                    alert('✗ Errore: ' + data.message);
                }
                submitBtn.disabled = false;
                submitBtn.innerText = 'Aggiungi';
            })
            .catch(err => {
                alert('Errore di comunicazione: ' + err.message);
                submitBtn.disabled = false;
                submitBtn.innerText = 'Aggiungi';
            });
    });
    
    function resetForm() {
        document.getElementById('product_id').value = '';
        document.getElementById('warehouse_id').innerHTML = '<option value="">Prima seleziona un prodotto</option>';
        document.getElementById('warehouse_id').disabled = true;
        document.getElementById('quantity').value = '';
        document.getElementById('availabilityMsg').innerHTML = '';
        currentAvailability.valid = false;
    }
    
    // Carica i prodotti già presenti nell'ordine
    function loadOrderProducts() {
        fetch(`api.php?action=getOrderProducts&id_ordine=${orderId}`)
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('productsList');
                if(data.success && data.data.length > 0) {
                    let html = '<div class="table-responsive"><table class="data-table"><thead><tr><th>Prodotto</th><th>Magazzino</th><th>Quantità</th><th></th></tr></thead><tbody>';
                    data.data.forEach(item => {
                        html += `<tr>
                                    <td>${item.nome_prodotto}</td>
                                    <td>${item.indirizzo_magazzino}</td>
                                    <td>${item.quantita}</td>
                                    <td><button class="btn-icon delete" onclick="removeProduct(${item.id_prodotto}, ${item.id_magazzino})">🗑️</button></td>
                                </tr>`;
                    });
                    html += '</tbody></table></div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p>Nessun prodotto aggiunto a questo ordine.</p>';
                }
            });
    }
    
    function removeProduct(prodId, magId) {
        if(confirm('Rimuovere questo prodotto dall\'ordine? Le quantità torneranno disponibili nel magazzino.')) {
            const formData = new FormData();
            formData.append('action', 'deleteOrderProduct');
            formData.append('id_ordine', orderId);
            formData.append('id_prodotto', prodId);
            formData.append('id_magazzino', magId);
            
            fetch('api.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        alert('Prodotto rimosso con successo.');
                        loadOrderProducts();
                        // Ricarica anche i magazzini se il prodotto era selezionato
                        const currentProd = document.getElementById('product_id').value;
                        if(currentProd) document.getElementById('product_id').dispatchEvent(new Event('change'));
                    } else {
                        alert('Errore: ' + data.message);
                    }
                });
        }
    }
    
    // Inizializzazione
    loadProducts();
    loadOrderProducts();
</script>
</body>
</html>