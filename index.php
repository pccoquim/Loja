<?php                                                                                                                           // Início do bloco de PHP de administração de produtos                                                        
session_start();                                                                                                                // Inicia a sessão para acessar as variáveis de sessão

require 'api/auth.php';                                                                                                         // Inclui o arquivo de autenticação para verificar se o usuário é administrador
require 'api/db.php';                                                                                                           // Inclui o arquivo de conexão com o banco de dados

if (!isset($_SESSION['user'])) {                                                                                                // Verifica se o usuário está logado
    header("Location: ./views/login.php");                                                                                      // Se não estiver logado, redireciona para a página de login
    exit();                                                                                                                     // Encerra o script para evitar que o restante do código seja executado
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {                                                                                    // Verifica se o formulário foi enviado via POST
    $produto_id = intval($_POST['produto_id']);                                                                                 // Obtém o ID do produto enviado pelo formulário
    $quantidade = max(1, intval($_POST['quantidade'] ?? 1));                                                                    // Obtém a quantidade do produto, garantindo que seja pelo menos 1
    $user_id = $_SESSION['user']['id'];                                                                                         // Obtém o ID do usuário logado da sessão

    
    $stmt = $con->prepare("SELECT id, quantidade FROM Carrinho WHERE userId = ? AND produtoId = ?");                            // Verifica se já existe esse produto no carrinho do utilizador
    $stmt->bind_param("ii", $user_id, $produto_id);                                                                             // Prepara a consulta para evitar SQL Injection
    $stmt->execute();                                                                                                           // Executa a consulta
    $result = $stmt->get_result();                                                                                              // Obtém o resultado da consulta

    if ($row = $result->fetch_assoc()) {                                                                                        // Se já existe um registo para este produto no carrinho
        $nova_qtd = $row['quantidade'] + $quantidade;                                                                           // Calcula a nova quantidade somando a quantidade atual com a nova quantidade
        $update = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE id = ?");                                             // Prepara a consulta de atualização
        $update->bind_param("ii", $nova_qtd, $row['id']);                                                                       // Liga os parâmetros da consulta
        $update->execute();                                                                                                     // Executa a atualização
    } else {                                                                                                                    // Se não existe um registo para este produto no carrinho
        $insert = $con->prepare("INSERT INTO Carrinho (userId, produtoId, quantidade) VALUES (?, ?, ?)");                       // Prepara a consulta de inserção
        $insert->bind_param("iii", $user_id, $produto_id, $quantidade);                                                         // Liga os parâmetros da consulta
        $insert->execute();                                                                                                     // Executa a inserção
    }

    header("Location: index.php");                                                                                              // Redireciona de volta para a página principal após adicionar o produto ao carrinho
    exit();                                                                                                                     // Encerra o script para evitar que o restante do código seja executado
}
?>                                                                                                                              <!-- Fim do bloco de PHP -->

<!DOCTYPE html>                                                                                                                 <!-- Declaração do tipo de documento HTML5 -->
<html lang="pt">                                                                                                                <!-- Define o idioma da página como português -->
<head>                                                                                                                          <!-- Início do cabeçalho da página -->
    <meta charset="UTF-8">                                                                                                      <!-- Define a codificação de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">                                                      <!-- Define a viewport para responsividade -->
    <title>Loja - Produtos</title>                                                                                              <!-- Título da página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">                      <!-- Link para o CSS do Bootstrap -->
    <script>                                                                                                                    // Início do script JavaScript
        function filtrarProdutos() {                                                                                            // Função para filtrar produtos com base no termo de pesquisa
            let termo = document.getElementById('pesquisa').value.toLowerCase();                                                // Obtém o valor do campo de pesquisa e converte para minúsculas
            document.querySelectorAll('.produto').forEach(p => {                                                                // Seleciona todos os elementos com a classe 'produto' e itera sobre eles
                const nome = p.querySelector('.nome-produto').innerText.toLowerCase();                                          // Obtém o nome do produto e converte para minúsculas
                p.style.display = nome.includes(termo) ? 'block' : 'none';                                                      // Exibe o produto se o nome incluir o termo de pesquisa, caso contrário, oculta
            });
        }

        function alterarQuantidade(id, delta) {                                                                                 // Função para alterar a quantidade de um produto
            const input = document.getElementById('qtd_' + id);                                                                 // Obtém o campo de entrada de quantidade pelo ID
            let novaQtd = parseInt(input.value) + delta;                                                                        // Calcula a nova quantidade
            if (novaQtd < 1) novaQtd = 1;                                                                                       // Garante que a quantidade mínima seja 1
            input.value = novaQtd;                                                                                              // Atualiza o valor do campo de entrada
        }
    </script>                                                                                                                   <!-- Fim do script JavaScript -->
</head>                                                                                                                         <!-- Fim do cabeçalho da página -->
<body class="bg-light">                                                                                                         <!-- Início do corpo da página com fundo claro -->

<div class="container py-4">                                                                                                    <!-- Início do container principal com padding vertical -->

    <div class="d-flex justify-content-between align-items-center mb-3">                                                        <!-- Início do cabeçalho da loja com flexbox para alinhar itens -->
        <h1>Loja</h1>                                                                                                           <!-- Título da loja -->
        <div class="d-flex gap-2">                                                                                              <!-- Início do grupo de botões -->
            <?php if (isAdmin()): ?>                                                                                            <!-- Verifica se o usuário é administrador -->
                <a href="./views/adminarea.php" class="btn btn-secondary">Administração</a>                                     <!-- Botão para acessar a área de administração -->
            <?php endif; ?>                                                                                                     <!-- Fim da verificação de administrador -->
            <a href="./views/carrinho.php" class="btn btn-primary">Ver Carrinho</a>                                             <!-- Botão para ver o carrinho -->
            <a href="views/logout.php" class="btn btn-danger">Logout</a>                                                        <!-- Botão para fazer logout -->
        </div>                                                                                                                  <!-- Fim do grupo de botões -->
    </div>                                                                                                                      <!-- Fim do cabeçalho da loja -->

    <!-- 🔍 Barra de Pesquisa -->
    <div class="input-group mb-4">                                                                                              <!-- Início do grupo de entrada para a barra de pesquisa -->
        <input type="text" id="pesquisa" class="form-control" placeholder="Pesquisar produto..." onkeyup="filtrarProdutos()">   <!-- Campo de entrada para pesquisa de produtos, chama a função filtrarProdutos ao digitar -->
        <span class="input-group-text"><i class="bi bi-search"></i></span>                                                      <!-- Ícone de pesquisa -->
    </div>                                                                                                                      <!-- Fim do grupo de entrada da barra de pesquisa -->

    <!-- Lista de Produtos -->
    <div class="row">                                                                                                           <!-- Início da linha para exibir os produtos -->
        <?php                                                                                                                   // Início do bloco PHP para exibir os produtos
        $result = $con->query("SELECT * FROM produtos");                                                                        // Consulta para obter todos os produtos do banco de dados
        while ($produto = $result->fetch_assoc()):                                                                              // Itera sobre cada produto retornado pela consulta
            $id = $produto['id'];                                                                                               // Obtém o ID do produto
        ?>                                                                                                                      <!-- Fim do bloco PHP para exibir os produtos -->
            <div class="col-md-4 mb-4 produto">                                                                                 <!-- Início da coluna para exibir o produto -->
                <div class="card h-100">                                                                                        <!-- Início do cartão para exibir as informações do produto -->
                    <?php if (!empty($produto['imagem'])): ?>                                                                   <!-- Verifica se o produto tem uma imagem -->
                        <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">  <!-- Exibe a imagem do produto convertida para base64 -->
                    <?php endif; ?>                                                                                             <!-- Fim da verificação de imagem -->
                    <div class="card-body d-flex flex-column">                                                                  <!-- Início do corpo do cartão com flexbox para alinhar itens -->
                        <h5 class="card-title nome-produto"><?= htmlspecialchars($produto['nome']) ?></h5>                      <!-- Título do produto, escapando caracteres especiais para evitar XSS -->
                        <p><?= htmlspecialchars($produto['descricao']) ?></p>                                                   <!-- Descrição do produto, escapando caracteres especiais para evitar XSS -->
                        <p class="fw-bold"><?= number_format($produto['preco'], 2) ?> €</p>                                     <!-- Preço do produto formatado com duas casas decimais e símbolo de euro -->

                        <form method="post" class="mt-auto">                                                                    <!-- Início do formulário para adicionar o produto ao carrinho -->
                            <input type="hidden" name="produto_id" value="<?= $id ?>">                                          <!-- Campo oculto para enviar o ID do produto -->
                            <div class="input-group mb-2">                                                                      <!-- Início do grupo de entrada para a quantidade -->
                                <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(<?= $id ?>, -1)">−</button>      <!-- Botão para diminuir a quantidade -->
                                <input type="number" id="qtd_<?= $id ?>" name="quantidade" value="1" min="1" class="form-control text-center" style="max-width: 60px;"> <!-- Campo de entrada para a quantidade, com valor inicial de 1 e mínimo de 1 -->
                                <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(<?= $id ?>, 1)">+</button>       <!-- Botão para aumentar a quantidade -->
                                <button type="submit" class="btn btn-success ms-2">Adicionar</button>                           <!-- Botão para enviar o formulário e adicionar o produto ao carrinho -->
                            </div>                                                                                              <!-- Fim do grupo de entrada para a quantidade -->
                        </form>                                                                                                 <!-- Fim do formulário para adicionar o produto ao carrinho -->
                    </div>                                                                                                      <!-- Fim do corpo do cartão -->
                </div>                                                                                                          <!-- Fim do cartão para exibir as informações do produto -->
            </div>                                                                                                              <!-- Fim da coluna para exibir o produto -->
        <?php endwhile; ?>                                                                                                      <!-- Fim do bloco PHP para exibir os produtos -->
    </div>                                                                                                                      <!-- Fim da linha para exibir os produtos -->
</div>                                                                                                                          <!-- Fim do container principal -->
</body>                                                                                                                         <!-- Fim do corpo da página -->
</html>                                                                                                                         <!-- Fim do documento HTML -->