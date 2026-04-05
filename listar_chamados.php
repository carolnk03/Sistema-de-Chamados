<?php
require_once 'config.php';
checkLogin();

$status_filtro = isset($_GET['status']) ? $_GET['status'] : 'todos';
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

$sql = "SELECT * FROM chamados WHERE usuario_id = ?";
$params = [$_SESSION['usuario_id']];

// Filtro por status
if ($status_filtro != 'todos') {
    $sql .= " AND status = ?";
    $params[] = $status_filtro;
}

// Filtro por busca (título)
if ($busca != '') {
    $sql .= " AND titulo LIKE ?";
    $params[] = "%$busca%";
}

$sql .= " ORDER BY data_abertura DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$chamados = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Chamados - Sistema de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .search-highlight {
            background-color: #fff3cd;
            transition: all 0.3s ease;
        }
        .btn-clear-search {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
        }
        .btn-clear-search:hover {
            color: #333;
        }
        .search-container {
            position: relative;
        }
    </style>
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
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-list-ul"></i> Meus Chamados</h5>
            </div>
            <div class="card-body">
                <!-- Barra de Pesquisa -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="" class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="search-container">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="busca" 
                                                   id="busca" 
                                                   placeholder="Pesquisar chamado pelo título..."
                                                   value="<?php echo htmlspecialchars($busca); ?>">
                                            <?php if($busca != ''): ?>
                                            <a href="?status=<?php echo $status_filtro; ?>" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle"></i> Limpar
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">Digite o título do chamado que deseja encontrar</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Pesquisar
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <a href="?status=<?php echo $status_filtro; ?>" class="btn btn-secondary w-100">
                                        <i class="bi bi-arrow-repeat"></i> Mostrar Todos
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Filtros por Status -->
                <div class="mb-3">
                    <label class="form-label">Filtrar por Status:</label>
                    <div class="btn-group flex-wrap" role="group">
                        <a href="?status=todos<?php echo $busca ? '&busca='.urlencode($busca) : ''; ?>" 
                           class="btn btn-outline-primary <?php echo $status_filtro == 'todos' ? 'active' : ''; ?>">
                            Todos
                        </a>
                        <a href="?status=aberto<?php echo $busca ? '&busca='.urlencode($busca) : ''; ?>" 
                           class="btn btn-outline-warning <?php echo $status_filtro == 'aberto' ? 'active' : ''; ?>">
                            Abertos
                        </a>
                        <a href="?status=em_andamento<?php echo $busca ? '&busca='.urlencode($busca) : ''; ?>" 
                           class="btn btn-outline-info <?php echo $status_filtro == 'em_andamento' ? 'active' : ''; ?>">
                            Em Andamento
                        </a>
                        <a href="?status=finalizado<?php echo $busca ? '&busca='.urlencode($busca) : ''; ?>" 
                           class="btn btn-outline-success <?php echo $status_filtro == 'finalizado' ? 'active' : ''; ?>">
                            Finalizados
                        </a>
                    </div>
                </div>
                
                <!-- Resultado da Pesquisa -->
                <?php if($busca != ''): ?>
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i> 
                    Resultados para: <strong>"<?php echo htmlspecialchars($busca); ?>"</strong>
                    <span class="badge bg-secondary ms-2"><?php echo count($chamados); ?> chamado(s) encontrado(s)</span>
                </div>
                <?php endif; ?>
                
                <!-- Lista de Chamados -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Data de Abertura</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($chamados as $chamado): ?>
                            <tr class="<?php echo ($busca != '' && stripos($chamado['titulo'], $busca) !== false) ? 'search-highlight' : ''; ?>">
                                <td>#<?php echo $chamado['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($chamado['titulo']); ?></strong>
                                    <?php if($busca != '' && stripos($chamado['titulo'], $busca) !== false): ?>
                                        <i class="bi bi-search text-primary ms-1" title="Corresponde à busca"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($chamado['descricao'], 0, 50)) . '...'; ?></td>
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
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                 </td>
                             </tr>
                            <?php endforeach; ?>
                            
                            <?php if(count($chamados) == 0): ?>
                             <tr>
                                <td colspan="6" class="text-center">
                                    <?php if($busca != ''): ?>
                                        <i class="bi bi-search"></i> Nenhum chamado encontrado com o título "<?php echo htmlspecialchars($busca); ?>"
                                        <br>
                                        <a href="?status=<?php echo $status_filtro; ?>" class="btn btn-sm btn-link mt-2">
                                            Limpar pesquisa
                                        </a>
                                    <?php else: ?>
                                        <i class="bi bi-inbox"></i> Nenhum chamado encontrado
                                        <br>
                                        <a href="abrir_chamado.php" class="btn btn-sm btn-primary mt-2">
                                            Abrir primeiro chamado
                                        </a>
                                    <?php endif; ?>
                                </td>
                             </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <a href="abrir_chamado.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Chamado
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>