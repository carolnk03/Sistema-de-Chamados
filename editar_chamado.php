<?php
require_once 'config.php';
checkLogin();

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Verificar se o chamado pertence ao usuário
$stmt = $pdo->prepare("SELECT * FROM chamados WHERE id = ? AND usuario_id = ?");
$stmt->execute([$id, $_SESSION['usuario_id']]);
$chamado = $stmt->fetch();

if (!$chamado) {
    header('Location: listar_chamados.php');
    exit();
}

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE chamados SET status = ? WHERE id = ? AND usuario_id = ?");
    
    if ($stmt->execute([$status, $id, $_SESSION['usuario_id']])) {
        $sucesso = 'Status atualizado com sucesso!';
        // Recarregar dados
        $stmt = $pdo->prepare("SELECT * FROM chamados WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $_SESSION['usuario_id']]);
        $chamado = $stmt->fetch();
    } else {
        $erro = 'Erro ao atualizar status!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Chamado - Sistema de Chamados</title>
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
                        <h5><i class="bi bi-pencil"></i> Editar Chamado #<?php echo $chamado['id']; ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if($sucesso): ?>
                            <div class="alert alert-success"><?php echo $sucesso; ?></div>
                        <?php endif; ?>
                        
                        <?php if($erro): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Título:</strong></label>
                            <p><?php echo htmlspecialchars($chamado['titulo']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Descrição:</strong></label>
                            <div class="border p-3 rounded bg-light">
                                <?php echo nl2br(htmlspecialchars($chamado['descricao'])); ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Data de Abertura:</strong></label>
                            <p><?php echo date('d/m/Y H:i', strtotime($chamado['data_abertura'])); ?></p>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="status" class="form-label"><strong>Status Atual:</strong></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="aberto" <?php echo $chamado['status'] == 'aberto' ? 'selected' : ''; ?>>
                                        Aberto
                                    </option>
                                    <option value="em_andamento" <?php echo $chamado['status'] == 'em_andamento' ? 'selected' : ''; ?>>
                                        Em Andamento
                                    </option>
                                    <option value="finalizado" <?php echo $chamado['status'] == 'finalizado' ? 'selected' : ''; ?>>
                                        Finalizado
                                    </option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Atualizar Status</button>
                            <a href="listar_chamados.php" class="btn btn-secondary">Voltar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>