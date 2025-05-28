<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');
require '../api/auth.php';
require '../api/db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$paypal_id = $data['paypal_id'] ?? '';
$user_id = $_SESSION['user']['id'];

if (!$paypal_id) {
    echo json_encode(['success' => false, 'error' => 'ID PayPal em falta']);
    exit();
}

$con->begin_transaction();

try {
    $insert_venda = $con->prepare("INSERT INTO Vendas (userId, data, paypal_id) VALUES (?, NOW(), ?)");
    $insert_venda->bind_param("is", $user_id, $paypal_id);
    $insert_venda->execute();
    $venda_id = $insert_venda->insert_id;

    $produtos = $con->prepare("SELECT produtoId, quantidade FROM Carrinho WHERE userId = ?");
    $produtos->bind_param("i", $user_id);
    $produtos->execute();
    $res = $produtos->get_result();

    while ($row = $res->fetch_assoc()) {
        $item = $con->prepare("INSERT INTO VendaItens (vendaId, produtoId, quantidade) VALUES (?, ?, ?)");
        $item->bind_param("iii", $venda_id, $row['produtoId'], $row['quantidade']);
        $item->execute();
    }

    $limpar = $con->prepare("DELETE FROM Carrinho WHERE userId = ?");
    $limpar->bind_param("i", $user_id);
    $limpar->execute();

    $con->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode(['success' => false, 'error' => 'Erro ao gravar: ' . $e->getMessage()]);
}
$output = ob_get_clean();
if (!empty($output)) {
    echo json_encode(['success' => false, 'error' => 'Output inesperado: ' . $output]);
}