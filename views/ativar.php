<?php                                                                                                                           // Início do bloco PHP de administração de produtos
    session_start();                                                                                                            // Inicia a sessão para utilizar variáveis de sessão
    require_once("../api/db.php");                                                                                              // Inclui o ficheiro de ligação à base de dados
    require_once("../api/auth.php");                                                                                            // Inclui o ficheiro de autenticação para verificar se o utilizador está autenticado

    $msg = "";                                                                                                                  // Variável para armazenar mensagens de feedback
    $redirect = false;                                                                                                          // Variável para controlar o redirecionamento após ativação

    if (isset($_GET['email']) && isset($_GET['token'])) {                                                                       // Verifica se os parâmetros email e token estão definidos
        $email = $_GET['email'];                                                                                                // Obtém o email do utilizador a partir dos parâmetros da URL
        $token = $_GET['token'];                                                                                                // Obtém o token de ativação do utilizador a partir dos parâmetros da URL

        $status = ativarConta($email, $token);                                                                                  // Chama a função ativarConta para ativar a conta do utilizador

        if ($status === true) {                                                                                                 // Verifica se a ativação foi bem-sucedida
            $msg = "<div class='alert alert-success'>Conta ativada com sucesso! A redirecionar para o login...</div>";          // Mensagem de sucesso
            $redirect = true;                                                                                                   // Define a variável de redirecionamento como verdadeira
        } elseif ($status === "already_active") {                                                                               // Verifica se a conta já está ativa
            $msg = "<div class='alert alert-warning'>A conta já se encontra ativada.</div>";                                    // Mensagem de aviso se a conta já estiver ativa
        } else {                                                                                                                // Caso contrário, se a ativação falhar
            $msg = "<div class='alert alert-danger'>Token inválido ou conta não encontrada.</div>";                             // Mensagem de erro se o token for inválido ou a conta não for encontrada
        }   
    } else {                                                                                                                    // Se os parâmetros email e token não estiverem definidos
        $msg = "<div class='alert alert-danger'>Parâmetros em falta.</div>";                                                    // Mensagem de erro indicando que os parâmetros estão em falta
    }
?>                                                                                                                              <!-- Fim do bloco PHP de administração de produtos -->

<!DOCTYPE html>                                                                                                                 <!-- Início do documento HTML -->
<html lang="pt">                                                                                                                <!-- Define o idioma do documento como português -->
<head>                                                                                                                          <!-- Cabeçalho do documento HTML -->
    <meta charset="UTF-8">                                                                                                      <!-- Define a codificação de caracteres como UTF-8 -->
    <title>Ativação de Conta</title>                                                                                            <!-- Título da página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">                      <!-- Inclui o CSS do Bootstrap para estilização -->
    <?php if ($redirect): ?>                                                                                                    <!-- Se a variável de redirecionamento for verdadeira -->
        <meta http-equiv="refresh" content="5;url=login.php">                                                                   <!-- Redireciona automaticamente para a página de login após 5 segundos -->
    <?php endif; ?>                                                                                                             <!-- Fim da verificação de redirecionamento -->
</head>                                                                                                                         <!-- Fim do cabeçalho do documento HTML -->
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">                                 <!-- Corpo do documento HTML -->
    <div class="container">                                                                                                     <!-- Contêiner principal -->
        <div class="row justify-content-center">                                                                                <!-- Linha para centralizar o conteúdo -->
            <div class="col-md-6">                                                                                              <!-- Coluna para o conteúdo -->
                <?= $msg ?>                                                                                                     <!-- Exibe a mensagem de feedback -->
            </div>                                                                                                              <!-- Fim da coluna -->
        </div>                                                                                                                  <!-- Fim da linha -->
    </div>                                                                                                                      <!-- Fim do contêiner principal -->
</body>                                                                                                                         <!-- Fim do corpo do documento HTML -->
</html>                                                                                                                         <!-- Fim do documento HTML -->
