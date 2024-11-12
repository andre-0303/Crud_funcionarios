<?php
include "Conexao.php";

if(isset($_GET['id'])){
    $id = $_GET['id']; 

    try {
        $stmt = $conn->prepare("DELETE FROM funcionarios WHERE idC = ?");
        $stmt->execute([$id]);

        header("Location: index.php");
        exit;
    }catch (PDOException $e) {
        echo "Erro ao excluir o funcionário: " . $e->getMessage();
    }
}else{
    header("Location: index.php");
}


