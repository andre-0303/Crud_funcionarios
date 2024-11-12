<?php
session_start();
include "Conexao.php"; // Certifique-se de que a conexão com o banco está funcionando

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = trim($_POST['senha']);

    if (!empty($usuario) && !empty($senha)) {
        try {
            // Consultar o banco de dados para verificar o usuário
            $stmt = $conn->prepare("SELECT idU, senha FROM usuarios WHERE usuario = :usuario");
            $stmt->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['senha'])) {
                // Criar sessões para o usuário autenticado
                $_SESSION['idU'] = $user['idU'];
                $_SESSION['nome'] = $usuario;

               header("Location: index.php");
                exit;
            } else {
                // Usuário ou senha inválidos
                $_SESSION['erro'] = "Usuário ou senha inválidos!";
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            die("Erro no banco de dados: " . $e->getMessage());
        }
    } else {
        // Campos não preenchidos
        $_SESSION['erro'] = "Por favor, preencha todos os campos!";
        header("Location: login.php");
        exit;
    }
} else {
    // Acessando a página sem ser via POST
    header("Location: login.php");
    exit;
}
