<?php
require '../api/db.php';

// CRIAR ou EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $action = $_POST['action'];

    if (!empty($_FILES['imagem']['tmp_name'])) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
        $imagem = $con->real_escape_string($imagem);
    }

    if ($action === 'create') {
        $query = "INSERT INTO produtos (nome, descricao, preco, imagem) VALUES ('$nome', '$descricao', '$preco', '$imagem')";
        $con->query($query);
    }

    if ($action === 'update') {
        $id = $_POST['id'];
        if (!empty($imagem)) {
            $query = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco', imagem='$imagem' WHERE id=$id";
        } else {
            $query = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco' WHERE id=$id";
        }
        $con->query($query);
    }

    header("Location: adminarea.php");
    exit;
}

// ELIMINAR
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $con->query("DELETE FROM produtos WHERE id = $id");
    header("Location: adminarea.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Administração de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1 class="mb-4">Administração de Produtos</h1>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Novo Produto</button>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Preço</th>
            <th>Imagem</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $result = $con->query("SELECT * FROM produtos");
        while ($produto = $result->fetch_assoc()):
            ?>
            <tr>
                
                <td><?= htmlspecialchars($produto['nome']) ?></td>
                <td><?= htmlspecialchars($produto['descricao']) ?></td>
                <td>€<?= number_format($produto['preco'], 2) ?></td>
                <td>
                    <?php if (!empty($produto['imagem'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" width="80">
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $produto['id'] ?>">Editar</button>
                    <a href="?delete=<?= $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar este produto?')">Eliminar</a>
                </td>
            </tr>

            <!-- Modal de Edição -->
            <div class="modal fade" id="editModal<?= $produto['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Produto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                            <div class="mb-2">
                                <label>Nome:</label>
                                <input name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label>Descrição:</label>
                                <textarea name="descricao" class="form-control"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                            </div>
                            <div class="mb-2">
                                <label>Preço:</label>
                                <input name="preco" type="number" step="0.01" class="form-control" value="<?= $produto['preco'] ?>" required>
                            </div>
                            <div class="mb-2">
                                <label>Nova Imagem (opcional):</label>
                                <input name="imagem" type="file" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Criação -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Novo Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="create">
                <div class="mb-2">
                    <label>Nome:</label>
                    <input name="nome" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Descrição:</label>
                    <textarea name="descricao" class="form-control"></textarea>
                </div>
                <div class="mb-2">
                    <label>Preço:</label>
                    <input name="preco" type="number" step="0.01" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Imagem:</label>
                    <input name="imagem" type="file" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Criar</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
