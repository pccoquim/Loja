<?php
session_start();

require '../api/auth.php';
require '../api/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Buscar produtos no carrinho do utilizador
$stmt = $con->prepare("
    SELECT c.id AS carrinho_id, c.quantidade, p.nome, p.preco, p.imagem
    FROM Carrinho c
    JOIN produtos p ON c.produtoId = p.id
    WHERE c.userId = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒 Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🛒 Carrinho de Compras</h2>
        <a href="../index.php" class="btn btn-secondary">← Continuar Compras</a>
    </div>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">O seu carrinho está vazio.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th class="text-end">Preço</th>
                    <th class="text-end">Quantidade</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $result->fetch_assoc()):
                    $subtotal = $item['preco'] * $item['quantidade'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($item['nome']) ?></strong><br>
                            <?php if (!empty($item['imagem'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($item['imagem']) ?>" width="60">
                            <?php endif; ?>
                        </td>
                        <td class="text-end"><?= number_format($item['preco'], 2) ?> €</td>
                        <td class="text-end">
                            <form method="post" action="../api/update_chart.php" class="d-inline">
                                <input type="hidden" name="carrinho_id" value="<?= $item['carrinho_id'] ?>">
                                <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" class="form-control d-inline w-auto text-end">
                                <button class="btn btn-sm btn-primary mt-1">Atualizar</button>
                            </form>
                        </td>
                        <td class="text-end"><?= number_format($subtotal, 2) ?> €</td>
                        <td class="text-end">
                            <form method="post" action="../api/delete_chart.php" onsubmit="return confirm('Remover este produto?')">
                                <input type="hidden" name="carrinho_id" value="<?= $item['carrinho_id'] ?>">
                                <button class="btn btn-sm btn-danger">❌ Remover</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total:</th>
                    <th class="text-end"><?= number_format($total, 2) ?> €</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <form method="post" action="checkout.php">
            <input type="hidden" name="total" value="<?= number_format($total, 2, '.', '') ?>">
            <button type="submit" class="btn btn-success">Finalizar Compra</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>