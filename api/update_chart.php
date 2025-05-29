<?php                                                                                                               // Início do bloco PHP de administração de produtos
require 'auth.php';                                                                                                 // Inclui o ficheiro de autenticação para verificar se o utilizador está autenticado
require 'db.php';                                                                                                   // Inclui o ficheiro de conexão ao banco de dados

session_start();                                                                                                    // Verifica se a sessão está iniciada e se o utilizador está autenticado

if (!isset($_SESSION['user'])) {                                                                                    // Verifica se o utilizador está autenticado
    header("Location: ../views/login.php");                                                                         // Se não estiver autenticado, redireciona para a página de login
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrinho_id'], $_POST['quantidade'])) {                  // Verifica se a requisição é do tipo POST e se os parâmetros necessários estão definidos
    $carrinho_id = intval($_POST['carrinho_id']);                                                                   // Obtém o ID do carrinho e converte para inteiro
    $quantidade = max(1, intval($_POST['quantidade']));                                                             // Obtém a quantidade e garante que é pelo menos 1
    $user_id = $_SESSION['user']['id'];                                                                             // Obtém o ID do utilizador autenticado

    $stmt = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE id = ? AND userId = ?");                        // Prepara a consulta SQL para atualizar a quantidade do produto no carrinho
    $stmt->bind_param("iii", $quantidade, $carrinho_id, $user_id);                                                  // Vincula os parâmetros à consulta
    $stmt->execute();                                                                                               // Executa a consulta
}

header("Location: ../views/carrinho.php");                                                                          // Redireciona para a página do carrinho
exit();                                                                                                             // Encerra o script para evitar que mais código seja executado
?>                                                                                                                  <!-- Fim do ficheiro de administração de produtos -->