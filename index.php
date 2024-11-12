<?php


include "Conexao.php";

// Configuração da paginação
$limite = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $limite;

// Termo de busca
$termoBusca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Campo e direção para ordenação
$campoOrdenacao = isset($_GET['ordem']) ? $_GET['ordem'] : 'nome';
$direcaoOrdenacao = isset($_GET['direcao']) && $_GET['direcao'] === 'desc' ? 'DESC' : 'ASC';

// Consulta para buscar o total de funcionários com filtro de busca
if ($termoBusca) {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) AS total FROM funcionarios WHERE nome LIKE :busca OR cargo LIKE :busca");
    $stmtTotal->bindValue(':busca', "%$termoBusca%", PDO::PARAM_STR);
} else {
    $stmtTotal = $conn->query("SELECT COUNT(*) AS total FROM funcionarios");
}
$stmtTotal->execute();
$totalFuncionarios = $stmtTotal->fetch()['total'];
$totalPaginas = ceil($totalFuncionarios / $limite);

// Consulta para buscar funcionários com filtro de busca e ordenação
if ($termoBusca) {
    $stmt = $conn->prepare("
        SELECT * 
        FROM funcionarios 
        WHERE nome LIKE :busca OR cargo LIKE :busca 
        ORDER BY $campoOrdenacao $direcaoOrdenacao 
        LIMIT :limite OFFSET :offset
    ");
    $stmt->bindValue(':busca', "%$termoBusca%", PDO::PARAM_STR);
} else {
    $stmt = $conn->prepare("
        SELECT * 
        FROM funcionarios 
        ORDER BY $campoOrdenacao $direcaoOrdenacao 
        LIMIT :limite OFFSET :offset
    ");
}
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$funcionarios = $stmt->fetchAll();

// Calcula o total pago aos funcionários
$totalPago = $conn->query("SELECT SUM(salario) AS totalPago FROM funcionarios")->fetch()['totalPago'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h1 class="text-center mb-4">Cadastro de Funcionários</h1>

    <!-- Informações antes da tabela -->
    <div class="mb-4">
        <p><strong>Quantidade de Funcionários:</strong> <?php echo $totalFuncionarios; ?></p>
        <p><strong>Total Pago aos Funcionários:</strong> R$ <?php echo number_format($totalPago, 2, ',', '.'); ?></p>
    </div>

    <!-- Campo de busca -->
    <form method="GET" action="index.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="busca" class="form-control" placeholder="Pesquisar por nome ou cargo" value="<?php echo htmlspecialchars($termoBusca); ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
        </div>
    </form>

    <!-- Lista de funcionários com ordenação -->
    <h2>Lista de Funcionários</h2>
    <?php if (count($funcionarios) > 0): ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>
                        <a href="?ordem=idC&direcao=<?php echo $direcaoOrdenacao === 'ASC' ? 'desc' : 'asc'; ?>&busca=<?php echo urlencode($termoBusca); ?>">
                            ID <?php echo $campoOrdenacao === 'idC' ? ($direcaoOrdenacao === 'ASC' ? '⬆' : '⬇') : ''; ?>
                        </a>
                    </th>
                    <th>
                        <a href="?ordem=nome&direcao=<?php echo $direcaoOrdenacao === 'ASC' ? 'desc' : 'asc'; ?>&busca=<?php echo urlencode($termoBusca); ?>">
                            Nome <?php echo $campoOrdenacao === 'nome' ? ($direcaoOrdenacao === 'ASC' ? '⬆' : '⬇') : ''; ?>
                        </a>
                    </th>
                    <th>Idade</th>
                    <th>CPF</th>
                    <th>Endereço</th>
                    <th>
                        <a href="?ordem=salario&direcao=<?php echo $direcaoOrdenacao === 'ASC' ? 'desc' : 'asc'; ?>&busca=<?php echo urlencode($termoBusca); ?>">
                            Salário <?php echo $campoOrdenacao === 'salario' ? ($direcaoOrdenacao === 'ASC' ? '⬆' : '⬇') : ''; ?>
                        </a>
                    </th>
                    <th>Cargo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($funcionarios as $funcionario): ?>
                    <tr>
                        <td><?php echo $funcionario['idC']; ?></td>
                        <td><?php echo $funcionario['nome']; ?></td>
                        <td><?php echo $funcionario['idade']; ?></td>
                        <td><?php echo $funcionario['cpf']; ?></td>
                        <td><?php echo $funcionario['endereco']; ?></td>
                        <td><?php echo $funcionario['salario']; ?></td>
                        <td><?php echo $funcionario['cargo']; ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $funcionario['idC']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $funcionario['idC']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Navegação de paginação -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>&busca=<?php echo urlencode($termoBusca); ?>&ordem=<?php echo $campoOrdenacao; ?>&direcao=<?php echo $direcaoOrdenacao; ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($termoBusca); ?>&ordem=<?php echo $campoOrdenacao; ?>&direcao=<?php echo $direcaoOrdenacao; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>&busca=<?php echo urlencode($termoBusca); ?>&ordem=<?php echo $campoOrdenacao; ?>&direcao=<?php echo $direcaoOrdenacao; ?>">Próxima</a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <p>Não possui funcionários cadastrados ou o termo buscado não encontrou resultados.</p>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('addFormBtn').addEventListener('click', function() {
            document.getElementById('cadastroForm').classList.toggle('d-none');
        });

        function confirmDelete(id) {
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            document.getElementById('confirmDeleteBtn').href = `excluir.php?id=${id}`;
            confirmModal.show();
        }
    </script>
</body>
</html>
