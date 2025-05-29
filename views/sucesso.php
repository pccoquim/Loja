<?php                                                                                                                           // Início do bloco de PHP de administração de produtos    
session_start();                                                                                                                // Inicia a sessão para acessar as variáveis de sessão
if (!isset($_SESSION['user'])) {                                                                                                // Verifica se o usuário está logado
    header("Location: login.php");                                                                                              // Se não estiver logado, redireciona para a página de login
    exit();                                                                                                                     // Encerra o script para evitar que o restante do código seja executado
}
?>                                                                                                                              <!-- Início do HTML para a página de sucesso -->

<!DOCTYPE html>                                                                                                                 <!-- Declaração do tipo de documento HTML5 -->
<html lang="pt">                                                                                                                <!-- Define o idioma da página como português -->
<head>                                                                                                                          <!-- Início do cabeçalho da página -->
    <meta charset="UTF-8">                                                                                                      <!-- Define a codificação de caracteres como UTF-8 -->
    <title>Compra Finalizada</title>                                                                                            <!-- Título da página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">                      <!-- Link para o CSS do Bootstrap -->
</head>                                                                                                                         <!-- Fim do cabeçalho da página -->
<body class="bg-light">                                                                                                         <!-- Início do corpo da página com fundo claro -->

<div class="container py-5 text-center">                                                                                        <!-- Início do container centralizado com padding e texto centralizado -->
    <div class="alert alert-success">                                                                                           <!-- Alerta de sucesso -->
        <h2>✅ Compra Finalizada com Sucesso!</h2>                                                                              <!-- Título do alerta -->
        <p>Obrigado pela sua compra. Um email de confirmação será enviado em breve.</p>                                         <!-- Mensagem de agradecimento -->
        <a href="../index.php" class="btn btn-primary mt-3">Voltar à Loja</a>                                                   <!-- Botão para voltar à loja -->
    </div>                                                                                                                      <!-- Fim do alerta de sucesso -->
</div>                                                                                                                          <!-- Fim do container centralizado -->

</body>                                                                                                                         <!-- Fim do corpo da página -->
</html>                                                                                                                         <!-- Fim do HTML para a página de sucesso -->