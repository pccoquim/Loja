<?php                                                                                                                           // Início do bloco PHP de administração de produtos
    session_start();                                                                                                            // Inicia a sessão para poder usar variáveis de sessão  
    require_once '../api/auth.php';                                                                                             // Inclui o arquivo de autenticação para verificar se o utilizador está autenticado

    $error_msg = false;                                                                                                         // Variável para controlar a exibição de mensagens de erro
    $msg = '';                                                                                                                  // Mensagem de erro a ser exibida, se necessário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {                                                                                // Verifica se o formulário foi enviado via POST
        $email     = $_POST['email'] ?? '';                                                                                     // Obtém o email do formulário, ou define como vazio se não estiver definido
        $username  = $_POST['username'] ?? '';                                                                                  // Obtém o nome de usuário do formulário, ou define como vazio se não estiver definido
        $password  = $_POST['password'] ?? '';                                                                                  // Obtém a senha do formulário, ou define como vazio se não estiver definido
        $confirm_password = $_POST['confirm_password'] ?? '';                                                                   // Obtém a confirmação da senha do formulário, ou define como vazio se não estiver definido
        $telemovel = $_POST['telemovel'] ?? '';                                                                                 // Obtém o telemóvel do formulário, ou define como vazio se não estiver definido
        $nif       = $_POST['nif'] ?? '';                                                                                       // Obtém o NIF do formulário, ou define como vazio se não estiver definido

        if (empty($_POST["username"])) {                                                                                        // Verifica se o campo username está vazio
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg .= "Preencha o campo username.";                                                                               // Adiciona mensagem de erro para o campo username
        }
        if (empty($_POST["email"])) {                                                                                           // Verifica se o campo email está vazio
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg .= "Preencha o campo email.";                                                                                  // Adiciona mensagem de erro para o campo email
        }
        if (empty($_POST["telemovel"])) {                                                                                       // Verifica se o campo telemóvel está vazio
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg .= "Preencha o campo telemovel.";                                                                              // Adiciona mensagem de erro para o campo telemóvel
        }
        if (empty($_POST["nif"])) {                                                                                             // Verifica se o campo NIF está vazio
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg .= "Preencha o campo nif.";                                                                                    // Adiciona mensagem de erro para o campo NIF
        }
        if (empty($_POST["password"])) {                                                                                        // Verifica se o campo password está vazio
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg .= "Preencha o campo password.";                                                                               // Adiciona mensagem de erro para o campo password
        }
        if (empty($_POST["confirm_password"])) {                                                                                // Verifica se o campo confirmar password está vazio
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg .= "Preencha o campo confirmar password.";                                                                     // Adiciona mensagem de erro para o campo confirmar password
        }
        if ($_POST["password"] != $_POST["confirm_password"]) {                                                                 // Verifica se a senha e a confirmação da senha coincidem
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg .= "As passwords não coincidem.";                                                                              // Adiciona mensagem de erro se as senhas não coincidirem
        }

        if (registo($email, $username, $password, $telemovel, $nif)) {                                                          // Chama a função de registo definida no arquivo de autenticação
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de sucesso
            $msg = "<div class='alert alert-success'>Registo efetuado com sucesso. Verifique o seu email para ativar a conta.</div>";   // Define a mensagem de sucesso
        } else {                                                                                                                // Se o registo falhar, define a mensagem de erro
            $error_msg = true;                                                                                                  // Define a variável de erro como verdadeira para exibir a mensagem de erro
            $msg = "<div class='alert alert-danger'>Erro ao efetuar o registo. Email ou utilizador já existem</div>";           // Define a mensagem de erro
        }
    }
?>                                                                                                                              <!-- Fim do bloco PHP de administração de produtos --> 

<!DOCTYPE html>                                                                                                                 <!-- Início do documento HTML     -->                        
<html lang="pt">                                                                                                                <!-- Define o idioma do documento como português -->
<head>                                                                                                                          <!-- Início do cabeçalho do documento HTML -->
    <meta charset="UTF-8">                                                                                                      <!-- Define a codificação de caracteres do documento como UTF-8 -->
    <title>Registo</title>                                                                                                      <!-- Define o título do documento como "Registo" -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">                      <!-- Inclui o CSS do Bootstrap para estilização -->
</head>                                                                                                                         <!-- Fim do cabeçalho do documento HTML -->
<body class="bg-light">                                                                                                         <!-- Define o corpo do documento HTML com um fundo claro -->
    <?php                                                                                                                       // Início do bloco PHP para exibir mensagens de erro ou sucesso
    if ($error_msg) {                                                                                                           // Verifica se há mensagens de erro ou sucesso a serem exibidas
        echo "<div class='position-fixed top-0 end-0 p-3' style='z-index: 1050;'>   
                  <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                      $msg
                      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>
              </div>";
    }
    ?>                                                                                                                          <!-- Exibe a mensagem de erro ou sucesso, se houver -->
    <div class="container mt-5 d-flex justify-content-center">                                                                  <!-- Início do contêiner para centralizar o conteúdo -->
        <div class="w-25">                                                                                                      <!-- Define a largura do contêiner como 25% da largura total -->
            <h2 class="text-center mb-4">Registo</h2>                                                                           <!-- Título do formulário de registo -->
            <form method="post">                                                                                                <!-- Início do formulário de registo -->
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para o email -->
                    <label for="email" class="form-label">E-mail</label>                                                        <!-- Rótulo do campo de entrada para o email -->
                    <input type="email" id="email" class="form-control" name="email" required>                                  <!-- Campo de entrada para o email, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para o email -->
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para o nome de utilizador -->
                    <label for="username" class="form-label">Nome de utilizador</label>                                         <!-- Rótulo do campo de entrada para o nome de utilizador -->
                    <input type="text" id="username" class="form-control" name="username" required>                             <!-- Campo de entrada para o nome de utilizador, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para o nome de utilizador -->
                        
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para o telemóvel -->
                    <label for="telemovel" class="form-label">Telemóvel</label>                                                 <!-- Rótulo do campo de entrada para o telemóvel -->
                    <input type="text" id="telemovel" class="form-control" name="telemovel" required>                           <!-- Campo de entrada para o telemóvel, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para o telemóvel -->
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para o NIF -->
                    <label for="nif" class="form-label">NIF</label>                                                             <!-- Rótulo do campo de entrada para o NIF -->
                    <input type="text" id="nif" class="form-control" name="nif" required>                                       <!-- Campo de entrada para o NIF, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para o NIF -->
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para a palavra-passe -->
                    <label for="password" class="form-label">Palavra-passe</label>                                              <!-- Rótulo do campo de entrada para a palavra-passe -->
                    <input type="password" id="password" class="form-control" name="password" required>                         <!-- Campo de entrada para a palavra-passe, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para a palavra-passe -->
                <div class="mb-3">                                                                                              <!-- Início do campo de entrada para a confirmação da palavra-passe -->
                    <label for="confirm_password" class="form-label">Confirmar palavra-passe</label>                            <!-- Rótulo do campo de entrada para a confirmação da palavra-passe -->
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" required>         <!-- Campo de entrada para a confirmação da palavra-passe, obrigatório -->
                </div>                                                                                                          <!-- Fim do campo de entrada para a confirmação da palavra-passe -->
                <div class="d-grid justify-content-center">                                                                     <!-- Início do contêiner para o botão de registo -->
                    <button type="submit" class="btn btn-success" style="width: 150px;">Registar</button>                       <!-- Botão de registo com estilo Bootstrap -->
                </div>                                                                                                          <!-- Fim do contêiner para o botão de registo -->
            </form>                                                                                                             <!-- Fim do formulário de registo -->
        </div>                                                                                                                  <!-- Fim do contêiner para o formulário de registo -->
    </div>                                                                                                                      <!-- Fim do contêiner para centralizar o conteúdo -->
</body>                                                                                                                         <!-- Fim do corpo do documento HTML -->
</html>                                                                                                                         <!-- Fim do documento HTML -->
