<?php
include "Conexao.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM funcionarios WHERE idC = ?");
    $stmt->execute([$id]);
    $funcionario = $stmt->fetch();

    if (!$funcionario) {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $cpf = $_POST['cpf'];
    $endereco = $_POST['endereco'];
    $salario = $_POST['salario'];
    $cargo = $_POST['cargo'];

    $stmt = $conn->prepare("UPDATE funcionarios SET nome = ?, idade = ?, cpf = ?, endereco = ?, salario = ?, cargo = ? WHERE idC = ?");
    $stmt->execute([$nome, $idade, $cpf, $endereco, $salario, $cargo, $id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h1 class="text-center mb-4">Editar Funcionário</h1>

    <form method="POST" action="" class="border p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($funcionario['nome']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="idade" class="form-label">Idade:</label>
            <input type="number" name="idade" class="form-control" value="<?php echo htmlspecialchars($funcionario['idade']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="cpf" class="form-label">CPF:</label>
            <input type="text" name="cpf" class="form-control" value="<?php echo htmlspecialchars($funcionario['cpf']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço:</label>
            <input type="text" name="endereco" class="form-control" value="<?php echo htmlspecialchars($funcionario['endereco']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="salario" class="form-label">Salário:</label>
            <input type="text" name="salario" class="form-control" value="<?php echo htmlspecialchars($funcionario['salario']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="cargo" class="form-label">Cargo:</label>
            <input type="text" name="cargo" class="form-control" value="<?php echo htmlspecialchars($funcionario['cargo']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="index.php" class="btn btn-secondary ms-2">Cancelar</a>
    </form>

</body>
</html>
