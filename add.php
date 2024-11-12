<?php
include 'conexao.php';
$senhaSegura = password_hash("senha", PASSWORD_DEFAULT);

// Insira um exemplo de usuário
$conn->prepare("INSERT INTO usuarios (nome, usuario, senha) VALUES ('saulo', 'saulo', :senha)")
     ->execute([':senha' => $senhaSegura]);
?>
