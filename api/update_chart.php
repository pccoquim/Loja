<?php
require 'auth.php';
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrinho_id'], $_POST['quantidade'])) {
    $carrinho_id = intval($_POST['carrinho_id']);
    $quantidade = max(1, intval($_POST['quantidade']));
    $user_id = $_SESSION['user']['id'];

    $stmt = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE id = ? AND userId = ?");
    $stmt->bind_param("iii", $quantidade, $carrinho_id, $user_id);
    $stmt->execute();
}

header("Location: ../views/carrinho.php");
exit();
?>