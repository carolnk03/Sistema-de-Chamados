<?php
session_start();

// Se já estiver logado, vai para dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

header('Location: login.php');
?>