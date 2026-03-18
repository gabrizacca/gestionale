<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Reindirizza al login
    exit; // Fondamentale per fermare l'esecuzione
}
?>