<?php
session_start();    
require '../api/db.php';
require '../api/auth.php';
require '../api/secrets.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// Buscar produtos no carrinho
$stmt = $con->prepare("SELECT c.quantidade, p.nome, p.preco FROM Carrinho c JOIN produtos p ON c.produtoId = p.id WHERE c.userId = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$total = 0;
$produtos = [];

while ($row = $res->fetch_assoc()) {
    $subtotal = $row['quantidade'] * $row['preco'];
    $total += $subtotal;
    $produtos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $PAYPAL_ID ?>&currency=EUR"></script> <!-- Substituir SEU_CLIENT_ID -->
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if (empty($produtos)): ?>
        <div class="alert alert-warning">O seu carrinho está vazio.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th class="text-end">Quantidade</th>
                    <th class="text-end">Preço</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td class="text-end"><?= $p['quantidade'] ?></td>
                        <td class="text-end"><?= number_format($p['preco'] * $p['quantidade'], 2) ?> €</td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-success fw-bold">
                    <td>Total</td>
                    <td></td>
                    <td class="text-end"><?= number_format($total, 2) ?> €</td>
                </tr>
            </tbody>
        </table>

        <div id="paypal-button-container"></div>

        <script>
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?= number_format($total, 2, '.', '') ?>'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Enviar dados para gravar venda
                        fetch('../api/gravar_venda.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ paypal_id: data.orderID })
                        })
                        .then(async res => {
                            const contentType = res.headers.get('Content-Type');
                            if (!contentType || !contentType.includes('application/json')) {
                                const text = await res.text();
                                throw new Error('Resposta inválida do servidor: ' + text);
                            }
                            return res.json();
                        })
                        .then(res => res.text())
                        .then(text => {
                            console.log('Resposta bruta:', text); // <-- ISTO AJUDA A VER O ERRO EXATO
                            const data = JSON.parse(text);
                            if (data.success) {
                                window.location.href = 'sucesso.php?paymentId=' + data.orderID;
                            } else {
                                alert('Erro ao gravar a venda: ' + data.error);
                            }
                        })
                        .catch(err => {
                            alert('Erro inesperado: ' + err.message);
                        });
                    });
                },
                onCancel: function() {
                    alert('Pagamento cancelado.');
                },
                onError: function(err) {
                    console.error(err);
                    alert('Erro no pagamento.');
                }
            }).render('#paypal-button-container');
        </script>
    <?php endif; ?>
</div>
</body>
</html>
