<?php
require_once 'config.php';
checkLogin();

// Buscar estatísticas
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM chamados WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$total_chamados = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM chamados WHERE usuario_id = ? AND status = 'aberto'");
$stmt->execute([$_SESSION['usuario_id']]);
$chamados_abertos = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM chamados WHERE usuario_id = ? AND status = 'em_andamento'");
$stmt->execute([$_SESSION['usuario_id']]);
$chamados_andamento = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM chamados WHERE usuario_id = ? AND status = 'finalizado'");
$stmt->execute([$_SESSION['usuario_id']]);
$chamados_finalizados = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema de Chamados</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    Olá, <?php echo $_SESSION['usuario_nome']; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total de Chamados</h5>
                        <h2><?php echo $total_chamados; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Abertos</h5>
                        <h2><?php echo $chamados_abertos; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Em Andamento</h5>
                        <h2><?php echo $chamados_andamento; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Finalizados</h5>
                        <h2><?php echo $chamados_finalizados; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Menu Rápido</h5>
                    </div>
                    <div class="card-body">
                        <a href="abrir_chamado.php" class="btn btn-success me-2">
                            <i class="bi bi-plus-circle"></i> Abrir Chamado
                        </a>
                        <a href="listar_chamados.php" class="btn btn-primary">
                            <i class="bi bi-list-ul"></i> Ver Meus Chamados
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Últimos Chamados</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM chamados WHERE usuario_id = ? ORDER BY data_abertura DESC LIMIT 5");
                        $stmt->execute([$_SESSION['usuario_id']]);
                        $chamados = $stmt->fetchAll();
                        ?>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($chamados as $chamado): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($chamado['titulo']); ?></td>
                                        <td>
                                            <?php
                                            $status_class = [
                                                'aberto' => 'warning',
                                                'em_andamento' => 'info',
                                                'finalizado' => 'success'
                                            ];
                                            $status_texto = [
                                                'aberto' => 'Aberto',
                                                'em_andamento' => 'Em Andamento',
                                                'finalizado' => 'Finalizado'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo $status_class[$chamado['status']]; ?>">
                                                <?php echo $status_texto[$chamado['status']]; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($chamado['data_abertura'])); ?></td>
                                        <td>
                                            <a href="editar_chamado.php?id=<?php echo $chamado['id']; ?>" class="btn btn-sm btn-primary">
                                                Atualizar
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if(count($chamados) == 0): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum chamado encontrado</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>