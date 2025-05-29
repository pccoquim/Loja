<?php                                                                                                                           // Início do bloco PHP de administração de produtos
require '../api/db.php';                                                                                                        // Incluir o ficheiro de conexão com a base de dados

// CRIAR ou EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {                                                                                    // Verifica se o método de requisição é POST
    $nome = $_POST['nome'];                                                                                                     // Obtém o nome do produto do formulário
    $descricao = $_POST['descricao'];                                                                                           // Obtém a descrição do produto do formulário
    $preco = $_POST['preco'];                                                                                                   // Obtém o preço do produto do formulário
    $action = $_POST['action'];                                                                                                 // Obtém a ação (criar ou atualizar) do formulário

    if (!empty($_FILES['imagem']['tmp_name'])) {                                                                                // Verifica se uma imagem foi enviada
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);                                                             // Lê o conteúdo do arquivo de imagem
        $imagem = $con->real_escape_string($imagem);                                                                            // Evita caracteres especiais para evitar SQL Injection
    }

    if ($action === 'create') {                                                                                                 // Se a ação for criar
        $query = "INSERT INTO produtos (nome, descricao, preco, imagem) VALUES ('$nome', '$descricao', '$preco', '$imagem')";   // Cria a query de inserção
        $con->query($query);                                                                                                    // Executa a query
    }

    if ($action === 'update') {                                                                                                 // Se a ação for atualizar
        $id = $_POST['id'];                                                                                                     // Obtém o ID do produto a ser atualizado
        if (!empty($imagem)) {                                                                                                  // Se uma nova imagem foi enviada
            $query = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco', imagem='$imagem' WHERE id=$id"; // Cria a query de atualização com a nova imagem
        } else {                                                                                                                // Se não foi enviada uma nova imagem
            $query = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco' WHERE id=$id";                   // Cria a query de atualização sem a imagem
        }
        $con->query($query);                                                                                                    // Executa a query
    }

    header("Location: adminarea.php");                                                                                          // Redireciona para a página de administração
    exit;                                                                                                                       // Encerra o script para evitar que o resto do código seja executado
}

// ELIMINAR
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {                                                           // Verifica se o método de requisição é GET e se existe um parâmetro 'delete'
    $id = $_GET['delete'];                                                                                                      // Obtém o ID do produto a ser eliminado
    $con->query("DELETE FROM produtos WHERE id = $id");                                                                         // Executa a query de eliminação
    header("Location: adminarea.php");                                                                                          // Redireciona para a página de administração
    exit;                                                                                                                       // Encerra o script para evitar que o resto do código seja executado
}
?>                                                                                                                              <!-- Fim do bloco PHP de administração de produtos -->

<!DOCTYPE html>                                                                                                                 <!-- HTML para a página de administração de produtos -->
<html>                                                                                                                          <!-- Início do documento HTML -->
<head>                                                                                                                          <!-- Cabeçalho do documento HTML -->
    <title>Administração de Produtos</title>                                                                                    <!-- Título da página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">                      <!-- Link para o CSS do Bootstrap -->
</head>                                                                                                                         <!-- Fim do cabeçalho -->
<body class="p-4">                                                                                                              <!-- Início do corpo do documento HTML -->
<div class="container">                                                                                                         <!-- Início do container Bootstrap -->
    <h1 class="mb-4">Administração de Produtos</h1>                                                                             <!-- Título da página -->

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Novo Produto</button>             <!-- Botão para abrir o modal de criação de produto -->

    <table class="table table-bordered">                                                                                        <!-- Início da tabela de produtos -->
        <thead>                                                                                                                 <!-- Cabeçalho da tabela -->
        <tr>                                                                                                                    <!-- Cabeçalho da tabela -->
            <th>Nome</th>                                                                                                       <!-- Coluna para o nome do produto -->
            <th>Descrição</th>                                                                                                  <!-- Coluna para a descrição do produto -->
            <th>Preço</th>                                                                                                      <!-- Coluna para o preço do produto -->
            <th>Imagem</th>                                                                                                     <!-- Coluna para a imagem do produto -->
            <th>Ações</th>                                                                                                      <!-- Coluna para as ações do produto -->
        </tr>                                                                                                                   <!-- Fim do cabeçalho da tabela -->
        </thead>                                                                                                                <!-- Fim do cabeçalho da tabela -->
        <tbody>                                                                                                                 <!-- Corpo da tabela -->
        <?php                                                                                                                   // Início do bloco PHP para exibir os produtos
            $result = $con->query("SELECT * FROM produtos");                                                                    // Executa a query para obter todos os produtos
            while ($produto = $result->fetch_assoc()):                                                                          // Itera sobre cada produto retornado pela query
        ?>                                                                                                                      <!-- Fim do bloco PHP para exibir cada produto -->
            <tr>                                                                                                                <!-- Início da linha da tabela para o produto -->
                <td><?= htmlspecialchars($produto['nome']) ?></td>                                                              <!-- Exibe o nome do produto, escapando caracteres especiais para evitar XSS -->
                <td><?= htmlspecialchars($produto['descricao']) ?></td>                                                         <!-- Exibe a descrição do produto, escapando caracteres especiais para evitar XSS -->
                <td>€<?= number_format($produto['preco'], 2) ?></td>                                                            <!-- Exibe o preço do produto formatado com duas casas decimais -->
                <td>                                                                                                            <!-- Coluna para a imagem do produto -->
                    <?php if (!empty($produto['imagem'])): ?>                                                                   <!-- Verifica se a imagem do produto não está vazia -->
                        <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" width="80">                  <!-- Exibe a imagem do produto convertida para base64 -->
                    <?php endif; ?>                                                                                             <!-- Fim da verificação da imagem do produto -->
                </td>                                                                                                           <!-- Fim da coluna da imagem do produto -->
                <td>                                                                                                            <!-- Coluna para as ações do produto -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $produto['id'] ?>">Editar</button>  <!-- Botão para abrir o modal de edição do produto -->
                    <a href="?delete=<?= $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar este produto?')">Eliminar</a>   <!-- Link para eliminar o produto, com confirmação -->
                </td>                                                                                                           <!-- Fim da coluna das ações do produto -->
            </tr>                                                                                                               <!-- Fim da linha da tabela para o produto -->

            <!-- Modal de Edição -->
            <div class="modal fade" id="editModal<?= $produto['id'] ?>" tabindex="-1">                                          <!-- Modal para editar o produto -->
                <div class="modal-dialog">                                                                                      <!-- Diálogo do modal -->
                    <form class="modal-content" method="post" enctype="multipart/form-data">                                    <!-- Formulário para editar o produto -->
                        <div class="modal-header">                                                                              <!-- Cabeçalho do modal -->
                            <h5 class="modal-title">Editar Produto</h5>                                                         <!-- Título do modal -->
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>                           <!-- Botão para fechar o modal -->
                        </div>                                                                                                  <!-- Fim do cabeçalho do modal -->
                        <div class="modal-body">                                                                                <!-- Corpo do modal -->
                            <input type="hidden" name="action" value="update">                                                  <!-- Campo oculto para a ação de atualização -->
                            <input type="hidden" name="id" value="<?= $produto['id'] ?>">                                       <!-- Campo oculto para o ID do produto -->
                            <div class="mb-2">                                                                                  <!-- Campo para o nome do produto -->
                                <label>Nome:</label>                                                                            <!-- Rótulo para o campo de nome -->
                                <input name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required> <!-- Campo de entrada para o nome do produto, com valor pré-preenchido -->
                            </div>                                                                                              <!-- Fim do campo para o nome do produto -->
                            <div class="mb-2">                                                                                  <!-- Campo para a descrição do produto --> 
                                <label>Descrição:</label>                                                                       <!-- Rótulo para o campo de descrição -->
                                <textarea name="descricao" class="form-control"><?= htmlspecialchars($produto['descricao']) ?></textarea>   <!-- Área de texto para a descrição do produto, com valor pré-preenchido -->
                            </div>                                                                                              <!-- Fim do campo para a descrição do produto -->
                            <div class="mb-2">                                                                                  <!-- Campo para o preço do produto -->
                                <label>Preço:</label>                                                                           <!-- Rótulo para o campo de preço -->
                                <input name="preco" type="number" step="0.01" class="form-control" value="<?= $produto['preco'] ?>" required>   <!-- Campo de entrada para o preço do produto, com valor pré-preenchido -->
                            </div>                                                                                              <!-- Fim do campo para o preço do produto -->
                            <div class="mb-2">                                                                                  <!-- Campo para a nova imagem do produto -->
                                <label>Nova Imagem (opcional):</label>                                                          <!-- Rótulo para o campo de nova imagem -->
                                <input name="imagem" type="file" class="form-control">                                          <!-- Campo de entrada para a nova imagem do produto, opcional -->
                            </div>                                                                                              <!-- Fim do campo para a nova imagem do produto -->
                        </div>                                                                                                  <!-- Fim do corpo do modal -->
                        <div class="modal-footer">                                                                              <!-- Rodapé do modal -->
                            <button class="btn btn-primary">Salvar</button>                                                     <!-- Botão para salvar as alterações -->
                        </div>                                                                                                  <!-- Fim do rodapé do modal -->
                    </form>                                                                                                     <!-- Fim do formulário de edição do produto -->
                </div>                                                                                                          <!-- Fim do diálogo do modal -->
            </div>                                                                                                              <!-- Fim do modal de edição -->
        <?php endwhile; ?>                                                                                                      <!-- Fim do bloco PHP para exibir os produtos -->
        </tbody>                                                                                                                <!-- Fim do corpo da tabela -->
    </table>                                                                                                                    <!-- Fim da tabela de produtos -->
</div>                                                                                                                          <!-- Fim do container Bootstrap -->

<!-- Modal de Criação -->
<div class="modal fade" id="createModal" tabindex="-1">                                                                         <!-- Modal para criar um novo produto -->
    <div class="modal-dialog">                                                                                                  <!-- Diálogo do modal -->
        <form class="modal-content" method="post" enctype="multipart/form-data">                                                <!-- Formulário para criar um novo produto -->
            <div class="modal-header">                                                                                          <!-- Cabeçalho do modal -->
                <h5 class="modal-title">Novo Produto</h5>                                                                       <!-- Título do modal -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>                                       <!-- Botão para fechar o modal -->
            </div>                                                                                                              <!-- Fim do cabeçalho do modal -->
            <div class="modal-body">                                                                                            <!-- Corpo do modal -->
                <input type="hidden" name="action" value="create">                                                              <!-- Campo oculto para a ação de criação -->
                <div class="mb-2">                                                                                              <!-- Campo para o nome do produto -->
                    <label>Nome:</label>                                                                                        <!-- Rótulo para o campo de nome -->
                    <input name="nome" class="form-control" required>                                                           <!-- Campo de entrada para o nome do produto, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo para o nome do produto -->
                <div class="mb-2">                                                                                              <!-- Campo para a descrição do produto -->
                    <label>Descrição:</label>                                                                                   <!-- Rótulo para o campo de descrição -->
                    <textarea name="descricao" class="form-control"></textarea>                                                 <!-- Área de texto para a descrição do produto -->
                </div>                                                                                                          <!-- Fim do campo para a descrição do produto -->
                <div class="mb-2">                                                                                              <!-- Campo para o preço do produto -->
                    <label>Preço:</label>                                                                                       <!-- Rótulo para o campo de preço -->
                    <input name="preco" type="number" step="0.01" class="form-control" required>                                <!-- Campo de entrada para o preço do produto, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo para o preço do produto -->
                <div class="mb-2">                                                                                              <!-- Campo para a imagem do produto -->
                    <label>Imagem:</label>                                                                                      <!-- Rótulo para o campo de imagem -->
                    <input name="imagem" type="file" class="form-control" required>                                             <!-- Campo de entrada para a imagem do produto, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo para a imagem do produto -->
            </div>                                                                                                              <!-- Fim do corpo do modal -->
            <div class="modal-footer">                                                                                          <!-- Rodapé do modal -->
                <button class="btn btn-success">Criar</button>                                                                  <!-- Botão para criar o novo produto -->
            </div>                                                                                                              <!-- Fim do rodapé do modal -->
        </form>                                                                                                                 <!-- Fim do formulário de criação de produto -->
    </div>                                                                                                                      <!-- Fim do diálogo do modal -->
</div>                                                                                                                          <!-- Fim do modal de criação -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>                               <!-- Script do Popper.js para o Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>                                   <!-- Script do Bootstrap -->
</body>                                                                                                                         <!-- Fim do corpo do documento HTML -->
</html>                                                                                                                         <!-- Fim do documento HTML -->
