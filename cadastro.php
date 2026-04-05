<?php
require_once 'config.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if ($senha != $confirmar_senha) {
        $erro = 'As senhas não coincidem!';
    } else {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $erro = 'Email já cadastrado!';
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nome, $email, $senha_hash])) {
                $sucesso = 'Cadastro realizado com sucesso! <a href="login.php">Faça login</a>';
            } else {
                $erro = 'Erro ao cadastrar!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sistema de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Cadastro de Usuário</h3>
                    </div>
                    <div class="card-body">
                        <?php if($erro): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        <?php if($sucesso): ?>
                            <div class="alert alert-success"><?php echo $sucesso; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label>Nome Completo</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Senha</label>
                                <input type="password" name="senha" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Confirmar Senha</label>
                                <input type="password" name="confirmar_senha" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login.php">Já tem conta? Faça login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>