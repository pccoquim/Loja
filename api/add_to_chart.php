<?php                                                                                                               // Início do bloco PHPde administração de produtos

require "auth.php";                                                                                                 // Inclui o ficheiro de autenticação para verificar se o utilizador está autenticado

session_start();                                                                                                    // Verifica se a sessão está iniciada e se o utilizador está autenticado

if (!isset($_SESSION['user'])) {                                                                                    // Verifica se o utilizador está autenticado
    header("Location: ../views/login.php");                                                                         // Se não estiver autenticado, redireciona para a página de login
    exit();                                                                                                         // Sai da função para evitar que o código continue a ser executado
}

$userId = $_SESSION['user']['id'];                                                                                  // guarda os dados de autenticação em variáveis
$produtoId = $_POST['produto_id'];                                                                                  // Guarda o ID do produto a adicionar ao carrinho
$quantidade = $_POST['quantidade'];                                                                                 // Guarda a quantidade do produto a adicionar ao carrinho

$stmt = $con->prepare("SELECT * FROM Carrinho WHERE userId = ? AND produtoId = ?");                                 // Prepara a query, para verificar se já existe um registo no carrinho para o utilizador e produto especificados
$stmt->bind_param("ii", $userId, $produtoId);                                                                       // Liga os parâmetros da query, substituindo os ? pelos valores de $userId e $produtoId
$stmt->execute();                                                                                                   // Executa a query preparada
$result = $stmt->get_result();                                                                                      // Obtém o resultado da query executada

if ($result->num_rows > 0) {                                                                                        // Verifica se já existe um registo para o utilizador e produto especificados
    $stmt = $con->prepare("UPDATE Carrinho SET quantidade = quantidade + ? WHERE userId = ? AND produtoId = ?");    // se existir: prepara a query para atualizar a quantidade
    $stmt->bind_param("iii", $quantidade, $userId, $produtoId);                                                     // Liga os parâmetros da query, substituindo os ? pelos valores de $quantidade, $userId e $produtoId
} else {
    $stmt = $con->prepare("INSERT INTO Carrinho (userId, produtoId, quantidade) VALUES (?, ?, ?)");                 // Se não existir: prepara a query, para inserir novo registo
    $stmt->bind_param("iii", $userId, $produtoId, $quantidade);                                                     // Liga os parâmetros da query, substituindo os ? pelos valores de $userId, $produtoId e $quantidade
}
$stmt->execute();                                                                                                   // Executa a query para adicionar ou atualizar o produto no carrinho

header("Location: ../index.php");                                                                                   // Redireciona de volta à página principal (ou a outra página se preferires)
exit();                                                                                                             // Sai da função para evitar que o código continue a ser executado
?>                                                                                                                  <!-- Fim do ficheiro de administração de produtos   -->