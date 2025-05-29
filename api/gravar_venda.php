<?php                                                                                                       // Início do bloco PHP de administração de produtos
session_start();                                                                                            // Verifica se a sessão está iniciada e se o utilizador está autenticado           

if (!isset($_SESSION['user'])) {                                                                            // Verifica se o utilizador está autenticado       
        header("Location: ../views/login.php");                                                             // Se não estiver autenticado, redireciona para a página de login
    exit();                                                                                                 // Sai da função para evitar que o código continue a ser executado
}

header('Content-Type: application/json');                                                                   // Define o tipo de conteúdo da resposta como JSON    
require '../api/auth.php';                                                                                  // Inclui o ficheiro de autenticação
require '../api/db.php';                                                                                    // Inclui o ficheiro de ligação à base de dados

$data = json_decode(file_get_contents("php://input"), true);                                                // Lê os dados JSON enviados na requisição

$paypal_id = $data['paypal_id'] ?? '';                                                                      // Obtém o ID do PayPal dos dados JSON, ou define como vazio se não existir
$user_id = $_SESSION['user']['id'];                                                                         // Obtém o ID do utilizador da sessão

if (!$paypal_id) {                                                                                          // Verifica se o ID do PayPal está vazio
    echo json_encode(['success' => false, 'error' => 'ID PayPal em falta']);                                // Se estiver vazio, retorna um erro em formato JSON
    exit();                                                                                                 // Sai da função para evitar que o código continue a ser executado
}

$con->begin_transaction();                                                                                  // Inicia uma transação na base de dados para garantir que todas as operações sejam atômicas

try {
    $insert_venda = $con->prepare("INSERT INTO Vendas (userId, data, paypal_id) VALUES (?, NOW(), ?)");     // Prepara a instrução SQL para inserir uma nova venda
    $insert_venda->bind_param("is", $user_id, $paypal_id);                                                  // Liga os parâmetros à instrução SQL
    $insert_venda->execute();                                                                               // Executa a instrução SQL para inserir a venda
    $venda_id = $insert_venda->insert_id;                                                                   // Obtém o ID da venda recém-inserida

    $produtos = $con->prepare("SELECT produtoId, quantidade FROM Carrinho WHERE userId = ?");               // Prepara a instrução SQL para selecionar os produtos do carrinho do utilizador
    $produtos->bind_param("i", $user_id);                                                                   // Liga o ID do utilizador ao parâmetro da instrução SQL
    $produtos->execute();                                                                                   // Executa a instrução SQL para obter os produtos do carrinho
    $res = $produtos->get_result();                                                                         // Obtém o resultado da consulta

    while ($row = $res->fetch_assoc()) {                                                                    // Itera sobre cada linha do resultado
        $item = $con->prepare("INSERT INTO VendaItens (vendaId, produtoId, quantidade) VALUES (?, ?, ?)");  // Prepara a instrução SQL para inserir os itens da venda
        $item->bind_param("iii", $venda_id, $row['produtoId'], $row['quantidade']);                         // Liga os parâmetros à instrução SQL
        $item->execute();                                                                                   // Executa a instrução SQL para inserir o item da venda
    }

    $limpar = $con->prepare("DELETE FROM Carrinho WHERE userId = ?");                                       // Prepara a instrução SQL para limpar o carrinho do utilizador
    $limpar->bind_param("i", $user_id);                                                                     // Liga o ID do utilizador ao parâmetro da instrução SQL
    $limpar->execute();                                                                                     // Executa a instrução SQL para limpar o carrinho

    $con->commit();                                                                                         // Confirma a transação na base de dados, aplicando todas as alterações feitas

    echo json_encode(['success' => true]);                                                                  // Retorna uma resposta JSON indicando que a operação foi bem-sucedida
} catch (Exception $e) {                                                                                    // Captura qualquer exceção que ocorra durante o processo
    $con->rollback();                                                                                       // Reverte a transação na base de dados, desfazendo todas as alterações feitas
    echo json_encode(['success' => false, 'error' => 'Erro ao gravar: ' . $e->getMessage()]);               // Retorna uma resposta JSON indicando que houve um erro, incluindo a mensagem de erro
}