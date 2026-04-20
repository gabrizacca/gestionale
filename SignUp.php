<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Controllo se l'utente è loggato e è amministratore
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['message'] = "Accesso negato. Solo gli amministratori possono creare nuovi utenti.";
    $_SESSION['message_type'] = 'error';
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    </style>
</head>
    <body>
        <div id="notification" class="notification <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <div class="container">
            <div class="main-box">
                <div class="header">
                    <i class="fas fa-user-circle"></i>
                    <h1>Benvenuto!</h1>
                    <p>Accedi al tuo account o registrati</p>
                </div>

            <!-- Nuova versione della sezione form con i nuovi campi -->
            <div class="form-section" id="registerSection">
                <h2><i class="fas fa-user-plus"></i> Registrati</h2>
                <form action="SignUp_processing.php" method="POST" onsubmit="return validatePasswords()">
                    <!-- Nome e Cognome separati -->
                    <div class="input-group">
                        <label for="register-firstname">
                            <i class="fas fa-user"></i>
                            <span>Nome</span>
                        </label>
                        <input type="text" id="register-firstname" name="firstname" placeholder="Inserisci nome" required>
                    </div>

                    <div class="input-group">
                        <label for="register-lastname">
                            <i class="fas fa-user"></i>
                            <span>Cognome</span>
                        </label>
                        <input type="text" id="register-lastname" name="lastname" placeholder="Inserisci cognome" required>
                    </div>

                    <div class="input-group">
                        <label for="register-username">
                            <i class="fas fa-at"></i>
                            <span>Username</span>
                        </label>
                        <input type="text" id="register-username" name="username" placeholder="Scegli username" required>
                        <small class="hint">Minimo 3 caratteri, solo lettere e numeri</small>
                    </div>

                    <div class="input-group">
                        <label for="register-email">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </label>
                        <input type="email" id="register-email" name="email" placeholder="Inserisci email" required>
                    </div>

                    <!-- IBAN -->
                    <div class="input-group">
                        <label for="register-iban">
                            <i class="fas fa-credit-card"></i>
                            <span>IBAN</span>
                        </label>
                        <input type="text" id="register-iban" name="iban" placeholder="Inserisci IBAN" required>
                    </div>

                    <!-- Is Admin -->
                    <div class="input-group">
                        <label for="register-is-admin">
                            <i class="fas fa-user-shield"></i>
                            <span>Amministratore</span>
                        </label>
                        <select id="register-is-admin" name="is_admin" required>
                            <option value="0">No</option>
                            <option value="1">Sì</option>
                        </select>
                    </div>

                    <!-- Stipendio -->
                    <div class="input-group">
                        <label for="register-salary">
                            <i class="fas fa-money-bill"></i>
                            <span>Stipendio</span>
                        </label>
                        <input type="text" id="register-salary" name="stipendio" placeholder="Inserisci stipendio" required>
                    </div>

                    <!-- Tipo -->
                    <div class="input-group">
                        <label for="register-type">
                            <i class="fas fa-clipboard"></i>
                            <span>Ruolo</span>
                        </label>
                        <input type="text" id="register-type" name="tipo" placeholder="Inserisci ruolo" required>
                    </div>

                    <!-- Filiale -->
                    <div class="input-group">
                        <label for="register-filiale">
                            <i class="fas fa-building"></i>
                            <span>Filiale (via)</span>
                        </label>
                        <input type="text" id="register-filiale" name="filiale_indirizzo" placeholder="Inserisci indirizzo filiale" required>
                        <small class="hint">Scrivi l'indirizzo della filiale per recuperare l'ID</small>
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <label for="register-password">
                            <i class="fas fa-lock"></i>
                            <span>Password</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="register-password" name="password" placeholder="Crea password" required onkeyup="checkPasswordStrength()">
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('register-password')"></i>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                        <small class="hint" id="strengthText">Inserisci una password</small>
                    </div>

                    <!-- Conferma Password -->
                    <div class="input-group">
                        <label for="register-confirm">
                            <i class="fas fa-lock"></i>
                            <span>Conferma Password</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="register-confirm" name="confirm_password" placeholder="Conferma password" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('register-confirm')"></i>
                        </div>
                    </div>

                    <!-- Termini -->
                    <div class="terms">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span>Accetto i <a href="#">Termini e Condizioni</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Registrati
                    </button>
                </form>
            </div>
        </div>
        <script>
            // Mostra notifica se presente
            <?php if ($message): ?>
                document.getElementById('notification').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('notification').style.display = 'none';
                }, 5000);
            <?php endif; ?>
                            
                // Validazione password
                function validatePasswords() {
                    const password = document.getElementById('register-password').value;
                    const confirm = document.getElementById('register-confirm').value;
                    
                    if (password !== confirm) {
                        alert('Le password non coincidono!');
                        return false;
                    }
                    
                    if (password.length < 8) {
                        alert('La password deve essere di almeno 8 caratteri!');
                        return false;
                    }
                    
                    return true;
                }

                // Funzione per mostrare/nascondere password
                function togglePassword(fieldId) {
                    const field = document.getElementById(fieldId);
                    const icon = field.nextElementSibling;
                    
                    if (field.type === 'password') {
                        field.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        field.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }

                // Controllo forza password
                function checkPasswordStrength() {
                    const password = document.getElementById('register-password').value;
                    const strengthBar = document.getElementById('strengthBar');
                    const strengthText = document.getElementById('strengthText');
                    
                    let strength = 0;
                    
                    if (password.length >= 8) strength++;
                    if (password.match(/[a-z]+/)) strength++;
                    if (password.match(/[A-Z]+/)) strength++;
                    if (password.match(/[0-9]+/)) strength++;
                    if (password.match(/[$@#&!]+/)) strength++;
                    
                    switch(strength) {
                        case 0:
                        case 1:
                            strengthBar.className = 'strength-bar weak';
                            strengthText.innerHTML = 'Password debole';
                            break;
                        case 2:
                        case 3:
                            strengthBar.className = 'strength-bar medium';
                            strengthText.innerHTML = 'Password media';
                            break;
                        case 4:
                        case 5:
                            strengthBar.className = 'strength-bar strong';
                            strengthText.innerHTML = 'Password forte';
                            break;
                    }
                }

                // Controlla se c'è un parametro URL per mostrare il form di registrazione
                window.onload = function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('action') === 'register') {
                        showRegister();
                    }
                };
        </script>
    </body>
</html>