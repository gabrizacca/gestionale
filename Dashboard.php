<?php
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: index.html"); // Reindirizza al login
        exit; // Fondamentale per fermare l'esecuzione
    }
?>