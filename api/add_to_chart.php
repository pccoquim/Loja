<?php

require "auth.php";

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../views/login.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$produtoId = $_POST['produto_id'];
$quantidade = $_POST['quantidade'];

// Verifica se já existe esse produto no carrinho
$stmt = $con->prepare("SELECT * FROM Carrinho WHERE userId = ? AND produtoId = ?");
$stmt->bind_param("ii", $userId, $produtoId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Atualiza a quantidade
    $stmt = $con->prepare("UPDATE Carrinho SET quantidade = quantidade + ? WHERE userId = ? AND produtoId = ?");
    $stmt->bind_param("iii", $quantidade, $userId, $produtoId);
} else {
    // Insere novo registo
    $stmt = $con->prepare("INSERT INTO Carrinho (userId, produtoId, quantidade) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $userId, $produtoId, $quantidade);
}

$stmt->execute();

// Redireciona de volta à página principal (ou a outra página se preferires)
header("Location: ../index.php");
exit();

?>