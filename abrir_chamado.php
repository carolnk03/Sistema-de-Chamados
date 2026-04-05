<?php
require_once 'config.php';
checkLogin();

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $status = 'aberto';
    $usuario_id = $_SESSION['usuario_id'];
    
    $stmt = $pdo->prepare("INSERT INTO chamados (titulo, descricao, status, usuario_id) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$titulo, $descricao, $status, $usuario_id])) {
        $sucesso = 'Chamado aberto com sucesso!';
    } else {
        $erro = 'Erro ao abrir chamado!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrir Chamado - Sistema de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Sistema de Chamados</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    Olá, <?php echo $_SESSION['usuario_nome']; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-plus-circle"></i> Abrir Novo Chamado</h5>
                    </div>
                    <div class="card-body">
                        <?php if($sucesso): ?>
                            <div class="alert alert-success"><?php echo $sucesso; ?></div>
                        <?php endif; ?>
                        
                        <?php if($erro): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título do Chamado</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição do Problema</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="5" required></textarea>
                                <div class="form-text">Descreva detalhadamente o problema que está enfrentando.</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Abrir Chamado</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>