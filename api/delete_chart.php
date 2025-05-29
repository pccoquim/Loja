<?php                                                                           // Início do bloco PHP de administração de produtos
require 'auth.php';                                                             // Inclui o ficheiro de autenticação para verificar se o utilizador está autenticado
require 'db.php';                                                               // Inclui o ficheiro de conexão ao banco de dados

session_start();                                                                // Verifica se a sessão está iniciada e se o utilizador está autenticado
if (!isset($_SESSION['user'])) {                                                // Verifica se o utilizador está autenticado
    header("Location: ../views/login.php");                                     // Se não estiver autenticado, redireciona para a página de login
    exit();                                                                     // Sai da função para evitar que o código continue a ser executado
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrinho_id'])) {    // Verifica se a requisição é do tipo POST e se o ID do carrinho foi enviado
    $carrinho_id = intval($_POST['carrinho_id']);                               // Obtém o ID do carrinho a ser removido e o ID do utilizador da sessão
    $user_id = $_SESSION['user']['id'];
    $stmt = $con->prepare("DELETE FROM Carrinho WHERE id = ? AND userId = ?");  // Prepara a query para remover o item do carrinho
    $stmt->bind_param("ii", $carrinho_id, $user_id);                            // Liga os parâmetros da query, substituindo os ? pelos valores de $carrinho_id e $user_id
    $stmt->execute();
}

header("Location: ../views/carrinho.php");                                      // Redireciona de volta à página do carrinho
exit();                                                                         // Sai da função para evitar que o código continue a ser executado
?>                                                                              <!-- Fim do ficheiro de administração de produtos -->