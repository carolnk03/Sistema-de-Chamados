<?php
session_start();

$host = 'localhost';
$dbname = 'sistema_chamados';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para verificar se usuário está logado
function checkLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}
?>