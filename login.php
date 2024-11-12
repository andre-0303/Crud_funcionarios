<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h1 class="text-center">Login</h1>

    <form method="POST" action="verificar_login.php" class="mt-4">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário:</label>
            <input type="text" name="usuario" id="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha:</label>
            <input type="password" name="senha" id="senha" class="form-control" required>
        </div>
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</body>
</html>
