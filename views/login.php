<?php                                                                                                                           // Início do bloco PHP de administração de produtos

    session_start();                                                                                                            // Iniciar a sessão para gerenciar o estado do usuário

    require '../api/auth.php';                                                                                                  // Incluir o arquivo de autenticação que contém a função de login

    $error_message = false;                                                                                                     // Variável para controlar a exibição de mensagens de erro
    $message = '';                                                                                                              // Mensagem de erro a ser exibida, se necessário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {                                                                                // Verificar se o formulário foi enviado via POST
        $username = $_POST['username'] ?? '';                                                                                   // Obter o nome de usuário do formulário, ou definir como vazio se não estiver definido
        $password = $_POST['password'] ?? '';                                                                                   // Obter a senha do formulário, ou definir como vazio se não estiver definido

        if(empty($username) || empty($password)) {                                                                              // Verificar se os campos de nome de usuário e senha estão vazios
            $error_message = true;                                                                                              // Definir a variável de erro como verdadeira para exibir a mensagem de erro
            $message = "Por favor, preencha todos os campos.";                                                                  // Definir a mensagem de erro
        } else {                                                                                                                // Se os campos não estiverem vazios, tentar fazer o login
            if (login($username, $password)) {                                                                                  // Chamar a função de login definida no arquivo de autenticação
                header("Location: ../index.php");                                                                               // Redirecionar após login com sucesso
                exit;                                                                                                           // Sair do script para evitar que o restante do código seja executado
            } else {                                                                                                            // Se o login falhar, definir a mensagem de erro
                $error_message = true;                                                                                          // Definir a variável de erro como verdadeira para exibir a mensagem de erro
                $message = "Credenciais inválidas ou conta inativa.";                                                           // Definir a mensagem de erro
            }       
        }
    }
?>                                                                                                                              <!-- Fim do bloco PHP de administração de produtos -->

<!DOCTYPE html>                                                                                                                 <!-- Início do documento HTML -->
<html lang="pt">                                                                                                                <!-- Definir o idioma do documento como português -->
<head>                                                                                                                          <!-- Início do cabeçalho do documento -->
    <meta charset="UTF-8">                                                                                                      <!-- Definir a codificação de caracteres do documento como UTF-8 -->
    <title>Login</title>                                                                                                        <!-- Definir o título do documento -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">                      <!-- Incluir o CSS do Bootstrap para estilização -->
</head>                                                                                                                         <!-- Fim do cabeçalho do documento -->
<body class="bg-light">                                                                                                         <!-- Definir o corpo do documento com uma cor de fundo clara -->
    <?php                                                                                                                       // Início do bloco PHP para exibir mensagens de erro
    if ($error_message) {                                                                                                       // Verificar se há uma mensagem de erro a ser exibida
        echo "<div class='position-fixed top-0 end-0 p-3' style='z-index: 1050;'>   
                  <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                      $message      
                      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>
              </div>";
    }
    ?>                                                                                                                          <!-- Fim do bloco PHP para exibir mensagens de erro -->
    <div class="container mt-5 d-flex justify-content-center">                                                                  <!-- Início do contêiner para centralizar o formulário de login -->
        <div class="w-25">                                                                                                      <!-- Definir a largura do formulário de login -->    
            <h2 class="text-center mb-4">Login</h2>                                                                             <!-- Título do formulário de login -->
            <form method="post">                                                                                                <!-- Início do formulário de login -->
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para nome de usuário -->
                    <label for="username" class="form-label">Email ou Nome de utilizador</label>                                <!-- Rótulo para o campo de nome de usuário -->
                    <input type="text" class="form-control" name="username" required>                                           <!-- Campo de entrada para nome de usuário, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para nome de usuário -->
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para senha -->
                    <label for="password" class="form-label">Palavra-passe</label>                                              <!-- Rótulo para o campo de senha -->
                    <input type="password" class="form-control" name="password" required>                                       <!-- Campo de entrada para senha, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para senha -->
                <div class="d-grid justify-content-center">                                                                     <!-- Início do contêiner para o botão de envio -->
                    <button type="submit" class="btn btn-success" style="width: 150px;">Entrar</button>                         <!-- Botão de envio do formulário com estilo Bootstrap -->
                </div>                                                                                                          <!-- Fim do contêiner para o botão de envio -->
            </form>                                                                                                             <!-- Fim do formulário de login -->
        </div>                                                                                                                  <!-- Fim do contêiner para o formulário de login -->
    </div>                                                                                                                      <!-- Fim do contêiner para centralizar o formulário de login -->
</body>                                                                                                                         <!-- Fim do corpo do documento -->
</html>                                                                                                                         <!-- Fim do documento HTML -->
