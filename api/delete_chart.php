<?php
require 'auth.php';
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrinho_id'])) {
    $carrinho_id = intval($_POST['carrinho_id']);
    $user_id = $_SESSION['user']['id'];

    $stmt = $con->prepare("DELETE FROM Carrinho WHERE id = ? AND userId = ?");
    $stmt->bind_param("ii", $carrinho_id, $user_id);
    $stmt->execute();
}

header("Location: ../views/carrinho.php");
exit();
?>