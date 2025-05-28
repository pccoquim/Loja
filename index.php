<?php
session_start();

require 'api/auth.php';
require 'api/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ./views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = intval($_POST['produto_id']);
    $quantidade = max(1, intval($_POST['quantidade'] ?? 1));
    $user_id = $_SESSION['user']['id'];

    // Verifica se já existe esse produto no carrinho do utilizador
    $stmt = $con->prepare("SELECT id, quantidade FROM Carrinho WHERE userId = ? AND produtoId = ?");
    $stmt->bind_param("ii", $user_id, $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Atualiza a quantidade
        $nova_qtd = $row['quantidade'] + $quantidade;
        $update = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE id = ?");
        $update->bind_param("ii", $nova_qtd, $row['id']);
        $update->execute();
    } else {
        // Insere novo registo
        $insert = $con->prepare("INSERT INTO Carrinho (userId, produtoId, quantidade) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $user_id, $produto_id, $quantidade);
        $insert->execute();
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function filtrarProdutos() {
            let termo = document.getElementById('pesquisa').value.toLowerCase();
            document.querySelectorAll('.produto').forEach(p => {
                const nome = p.querySelector('.nome-produto').innerText.toLowerCase();
                p.style.display = nome.includes(termo) ? 'block' : 'none';
            });
        }

        function alterarQuantidade(id, delta) {
            const input = document.getElementById('qtd_' + id);
            let novaQtd = parseInt(input.value) + delta;
            if (novaQtd < 1) novaQtd = 1;
            input.value = novaQtd;
        }
    </script>
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Loja</h1>
        <div class="d-flex gap-2">
            <?php if (isAdmin()): ?>
                <a href="./views/adminarea.php" class="btn btn-secondary">Administração</a>
            <?php endif; ?>
            <a href="./views/carrinho.php" class="btn btn-primary">Ver Carrinho</a>
            <a href="views/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- 🔍 Barra de Pesquisa -->
    <div class="input-group mb-4">
        <input type="text" id="pesquisa" class="form-control" placeholder="Pesquisar produto..." onkeyup="filtrarProdutos()">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
    </div>

    <!-- Lista de Produtos -->
    <div class="row">
        <?php
        $result = $con->query("SELECT * FROM produtos");
        while ($produto = $result->fetch_assoc()):
            $id = $produto['id'];
        ?>
            <div class="col-md-4 mb-4 produto">
                <div class="card h-100">
                    <?php if (!empty($produto['imagem'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title nome-produto"><?= htmlspecialchars($produto['nome']) ?></h5>
                        <p><?= htmlspecialchars($produto['descricao']) ?></p>
                        <p class="fw-bold"><?= number_format($produto['preco'], 2) ?> €</p>

                        <form method="post" class="mt-auto">
                            <input type="hidden" name="produto_id" value="<?= $id ?>">
                            <div class="input-group mb-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(<?= $id ?>, -1)">−</button>
                                <input type="number" id="qtd_<?= $id ?>" name="quantidade" value="1" min="1" class="form-control text-center" style="max-width: 60px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(<?= $id ?>, 1)">+</button>
                                <button type="submit" class="btn btn-success ms-2">Adicionar</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</div>
</body>
</html>