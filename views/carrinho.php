<?php                                                                                                                           // Início do bloco PHP de administração de produtos        
session_start();                                                                                                                // Inicia a sessão para utilizar variáveis de sessão

require '../api/auth.php';                                                                                                      // Inclui o ficheiro de autenticação para verificar se o utilizador está autenticado
require '../api/db.php';                                                                                                        // Inclui o ficheiro de ligação à base de dados

if (!isset($_SESSION['user'])) {                                                                                                // Verifica se o utilizador está autenticado
    header("Location: login.php");                                                                                              // Se não estiver autenticado, redireciona para a página de login
    exit();                                                                                                                     // Termina a execução do script
}

$user_id = $_SESSION['user']['id'];                                                                                             // Obtém o ID do utilizador autenticado a partir da sessão

                                                                                                                                // Seleciona os produtos no carrinho do utilizador
$stmt = $con->prepare("
    SELECT c.id AS carrinho_id, c.quantidade, p.nome, p.preco, p.imagem
    FROM Carrinho c
    JOIN produtos p ON c.produtoId = p.id
    WHERE c.userId = ?
");
$stmt->bind_param("i", $user_id);                                                                                               // Liga o parâmetro do ID do utilizador à consulta
$stmt->execute();                                                                                                               // Executa a consulta preparada
$result = $stmt->get_result();                                                                                                  // Obtém o resultado da consulta

$total = 0;                                                                                                                     // Inicializa a variável total para calcular o valor total do carrinho
?>                                                                                                                              <!-- Fim do bloco PHP de administração de produtos -->

<!DOCTYPE html>                                                                                                                 <!-- Início do documento HTML -->   
<html lang="pt">                                                                                                                <!-- Define o idioma do documento como português -->
<head>                                                                                                                          <!-- Cabeçalho do documento HTML -->
    <meta charset="UTF-8">                                                                                                      <!-- Define a codificação de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">                                                      <!-- Define a viewport para responsividade -->
    <title>🛒 Carrinho</title>                                                                                                  <!-- Título da página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">                      <!-- Inclui o CSS do Bootstrap para estilização -->
</head>                                                                                                                         <!-- Fim do cabeçalho do documento HTML -->
<body class="bg-light">                                                                                                         <!-- Corpo do documento HTML com fundo claro -->

<div class="container py-4">                                                                                                    <!-- Contêiner principal com padding vertical -->
    <div class="d-flex justify-content-between align-items-center mb-4">                                                        <!-- Linha para o título e botão de continuar compras -->
        <h2>🛒 Carrinho de Compras</h2>                                                                                         <!-- Título do carrinho de compras -->
        <a href="../index.php" class="btn btn-secondary">← Continuar Compras</a>                                                <!-- Botão para continuar as compras -->
    </div>                                                                                                                      <!-- Fim da linha para o título e botão de continuar compras -->

    <?php if ($result->num_rows === 0): ?>                                                                                      <!-- Verifica se o carrinho está vazio -->
        <div class="alert alert-info">O seu carrinho está vazio.</div>                                                          <!-- Mensagem informando que o carrinho está vazio -->
    <?php else: ?>                                                                                                              <!-- Se o carrinho não estiver vazio -->
        <table class="table table-striped">                                                                                     <!-- Tabela para exibir os produtos no carrinho -->
            <thead>                                                                                                             <!-- Cabeçalho da tabela -->
                <tr>                                                                                                            <!-- Linha do cabeçalho -->
                    <th>Produto</th>                                                                                            <!-- Coluna para o nome do produto -->
                    <th class="text-end">Preço</th>                                                                             <!-- Coluna para o preço do produto -->
                    <th class="text-end">Quantidade</th>                                                                        <!-- Coluna para a quantidade do produto -->
                    <th class="text-end">Subtotal</th>                                                                          <!-- Coluna para o subtotal do produto -->
                    <th class="text-end">Ações</th>                                                                             <!-- Coluna para as ações (atualizar/remover) -->
                </tr>                                                                                                           <!-- Fim da linha do cabeçalho -->
            </thead>                                                                                                            <!-- Fim do cabeçalho da tabela -->
            <tbody>                                                                                                             <!-- Corpo da tabela onde os produtos serão listados -->
                <?php while ($item = $result->fetch_assoc()):                                                                   // Loop para iterar sobre cada produto no carrinho
                    $subtotal = $item['preco'] * $item['quantidade'];                                                           // Calcula o subtotal do produto (preço * quantidade)
                    $total += $subtotal;                                                                                        // Adiciona o subtotal ao total do carrinho
                ?>                                                                                                              <!-- Fim do loop para iterar sobre cada produto no carrinho -->
                    <tr>                                                                                                        <!-- Linha da tabela para cada produto -->
                        <td>                                                                                                    <!-- Coluna para o nome do produto -->
                            <strong><?= htmlspecialchars($item['nome']) ?></strong><br>                                         <!-- Exibe o nome do produto em negrito -->
                            <?php if (!empty($item['imagem'])): ?>                                                              <!-- Verifica se a imagem do produto não está vazia -->
                                <img src="data:image/jpeg;base64,<?= base64_encode($item['imagem']) ?>" width="60">             <!-- Exibe a imagem do produto convertida para base64 -->
                            <?php endif; ?>                                                                                     <!-- Fim da verificação da imagem do produto -->
                        </td>                                                                                                   <!-- Fim da coluna para o nome do produto -->    
                        <td class="text-end"><?= number_format($item['preco'], 2) ?> €</td>                                     <!-- Coluna para o preço do produto formatado com duas casas decimais -->
                        <td class="text-end">                                                                                   <!-- Coluna para a quantidade do produto -->
                            <form method="post" action="../api/update_chart.php" class="d-inline">                              <!-- Formulário para atualizar a quantidade do produto -->
                                <input type="hidden" name="carrinho_id" value="<?= $item['carrinho_id'] ?>">                    <!-- Campo oculto para o ID do carrinho -->
                                <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" class="form-control d-inline w-auto text-end"> <!-- Campo de entrada para a quantidade do produto com valor mínimo de 1 -->
                                <button class="btn btn-sm btn-primary mt-1">Atualizar</button>                                  <!-- Botão para atualizar a quantidade do produto -->
                            </form>                                                                                             <!-- Fim do formulário para atualizar a quantidade do produto -->
                        </td>                                                                                                   <!-- Fim da coluna para a quantidade do produto -->
                        <td class="text-end"><?= number_format($subtotal, 2) ?> €</td>                                          <!-- Coluna para o subtotal do produto formatado com duas casas decimais -->
                        <td class="text-end">                                                                                   <!-- Coluna para as ações (atualizar/remover) -->
                            <form method="post" action="../api/delete_chart.php" onsubmit="return confirm('Remover este produto?')">            <!-- Formulário para remover o produto do carrinho -->
                                <input type="hidden" name="carrinho_id" value="<?= $item['carrinho_id'] ?>">                    <!-- Campo oculto para o ID do carrinho -->
                                <button class="btn btn-sm btn-danger">❌ Remover</button>                                       <!-- Botão para remover o produto do carrinho -->
                            </form>                                                                                             <!-- Fim do formulário para remover o produto do carrinho -->
                        </td>                                                                                                   <!-- Fim da coluna para as ações (atualizar/remover) -->
                    </tr>                                                                                                       <!-- Fim da linha da tabela para cada produto -->
                <?php endwhile; ?>                                                                                              <!-- Fim do loop para iterar sobre cada produto no carrinho -->
            </tbody>                                                                                                            <!-- Fim do corpo da tabela onde os produtos são listados -->
            <tfoot>                                                                                                             <!-- Rodapé da tabela -->
                <tr>                                                                                                            <!-- Linha do rodapé -->
                    <th colspan="3" class="text-end">Total:</th>                                                                <!-- Coluna para o total do carrinho -->
                    <th class="text-end"><?= number_format($total, 2) ?> €</th>                                                 <!-- Coluna para o total do carrinho formatado com duas casas decimais -->
                    <th></th>                                                                                                   <!-- Coluna vazia para alinhamento -->
                </tr>                                                                                                           <!-- Fim da linha do rodapé -->
            </tfoot>                                                                                                            <!-- Fim do rodapé da tabela -->
        </table>                                                                                                                <!-- Fim da tabela para exibir os produtos no carrinho -->

        <form method="post" action="checkout.php">                                                                              <!-- Formulário para finalizar a compra -->
            <input type="hidden" name="total" value="<?= number_format($total, 2, '.', '') ?>">                                 <!-- Campo oculto para o total do carrinho -->
            <button type="submit" class="btn btn-success">Finalizar Compra</button>                                             <!-- Botão para finalizar a compra -->
        </form>                                                                                                                 <!-- Fim do formulário para finalizar a compra -->
    <?php endif; ?>                                                                                                             <!-- Fim da verificação se o carrinho está vazio -->
</div>                                                                                                                          <!-- Fim do contêiner principal -->
</body>                                                                                                                         <!-- Fim do corpo do documento HTML -->
</html>                                                                                                                         <!-- Fim do documento HTML -->