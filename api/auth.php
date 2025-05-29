<?php                                                                                                               // Início do bloco PHP de administração de produtos
    require 'db.php';                                                                                               // Inclui o ficheiro de conexão à base de dados
    require 'email.php';                                                                                            // Inclui o ficheiro de envio de email

    /**
    * Login de um utilizador
    * @param string $username -> Nome de utilizador ou email
    * @param string $password -> Password do utilizador
    * @return bool -> true se o login foi bem sucedido, false caso contrário
    */
    
    function login($username,$password)                                                                             // Função para efetuar o login do utilizador
    {
        global $con;                                                                                                // Variável global que contém a ligação à base de dados
        $sql = $con->prepare("SELECT * FROM utilizador WHERE (username = ? OR email = ?) AND active = 1");          // Prepara a query de localização do utilizador por username ou email
        $sql->bind_param("ss", $username, $username);                                                               // Liga os parâmetros da query, substituindo os ? pelos valores de $username e $email
        $sql->execute();                                                                                            // Executa a query preparada
        $result = $sql->get_result();                                                                               // Obtém o resultado da query executada
        if ($result->num_rows > 0) {                                                                                // Verifica se foi encontrado algum utilizador com o username ou email fornecido
            $row = $result->fetch_assoc();                                                                          // Se foi encontrado, obtém os dados do utilizador
            $_SESSION ["user"] = $row;                                                                              // Guardar o ID do utilizador na sessão
            if (password_verify($password, $row['password']))                                                       // Verifica se a password fornecida corresponde à password armazenada na base de dados
            {
                return true;                                                                                        // Password é igual: Login bem-sucedido
            }
        }
        
        return false;                                                                                               // Se não foi encontrado nenhum utilizador com o username ou email fornecido, ou se a password não corresponde, o login falhou
    }

    /**
    * Registo de um novo utilizador
    * @param string $email     -> Email do utilizador
    * @param string $username  -> Nome de utilizador
    * @param string $password  -> Password do utilizador
    * @param string $telemovel -> Número de telemóvel
    * @param string $nif       -> Número de Identificação Fiscal
    * @return bool -> true se o registo foi bem sucedido, false caso contrário
    */
    
    function registo($email,$username,$password,$telemovel,$nif)                                                    // Função para registar um novo utilizador
    {
        global $con;                                                                                                // Variável global que contém a ligação à base de dados
        $check = $con->prepare("SELECT id FROM utilizador WHERE email = ? OR username = ?");                        // Prepara a query de verificação de existência de utilizador
        $check->bind_param("ss", $email, $username);                                                                // Liga os parâmetros da query, substituindo os ? pelos valores de $email e $username
        $check->execute();                                                                                          // Executa a query preparada
        $result = $check->get_result();                                                                             // Obtém o resultado da query executada
        if ($result->num_rows > 0) {                                                                                // Verifica se foi encontrado algum utilizador com o email ou username fornecido
            return false;                                                                                           // Já existe utilizador com esse email ou username - retorna false
        }
        $check->close();                                                                                            // Fecha a query de verificação
        $sql = $con->prepare("INSERT INTO utilizador (email, username, password, telemovel, nif, token) VALUES (?, ?, ?, ?, ?, ?)");    // Prepara a query de inserção de novo utilizador de modo a evitar SQL Injection
        $token = bin2hex(random_bytes(16));                                                                         // Gera um token aleatório de 32 caracteres            
        $password = password_hash($password, PASSWORD_DEFAULT);                                                     // Cria um hash da password fornecida
        $sql->bind_param("ssssss", $email, $username, $password, $telemovel, $nif, $token);                         // Liga os parâmetros da query, substituindo os ? pelos valores de $email, $username, $password, $telemovel, $nif e $token
        $sql->execute();                                                                                            // Executa a query preparada
        if ($sql->affected_rows > 0) {                                                                              // Verifica se a query afetou alguma linha (ou seja, se o utilizador foi inserido com sucesso)
            send_email($email, $username, "<a href='http://localhost/loja/views/ativar.php?email=$email&token=$token'>Clique aqui para ativar a sua conta</a>", "Activar a sua conta");     // Se a query afetou alguma linha, envia o email de ativação
            return true;                                                                                            // Retorna true indicando que o registo foi bem-sucedido
        } else {
            return false;                                                                                           // Retorna false indicando que o registo falhou
        }
    }

    /**
    * Ativar a conta do utilizador
    * @param string $email -> Email do utilizador
    * @param string $token -> Token de ativação
    * @return bool -> true se a conta foi ativada, false caso contrário
    */
    
    function ativarConta($email,$token)                                                                             // Função para ativar a conta do utilizador
    {
        global $con;                                                                                                // Variável global que contém a ligação à base de dados
        $check = $con->prepare("SELECT active FROM utilizador WHERE email = ? AND token = ?");                      // Prepara a query de verificação do token e email
        $check->bind_param("ss", $email, $token);                                                                   // Liga os parâmetros da query, substituindo os ? pelos valores de $email e $token
        $check->execute();                                                                                          // Executa a query preparada
        $result = $check->get_result();                                                                             // Obtém o resultado da query executada
        if ($result->num_rows === 0) {                                                                              // Verifica se foi encontrado algum utilizador com o email e token fornecidos
            return false;                                                                                           // Se não foi encontrado, retorna false indicando que o token ou email é inválido
        }
        $row = $result->fetch_assoc();                                                                              // Se foi encontrado, obtém os dados do utilizador
        if ($row['active'] == 1) {                                                                                  // Verifica se a conta já está ativa
            return "already_active";                                                                                // Se a conta já está ativa, retorna uma mensagem indicando que já estava ativa
        }
        $sql = $con->prepare("UPDATE utilizador SET active = 1, updated_at = Now() WHERE email = ? AND token = ?"); // Se a conta não está ativa, prepara a query de atualização do estado da conta
        $sql->bind_param("ss", $_GET["email"], $_GET["token"]);                                                     // Liga os parâmetros da query, substituindo os ? pelos valores de $email e $token
        $sql->execute();                                                                                            // Executa a query preparada
        if($sql->affected_rows > 0) {                                                                               // Verifica se a query afetou alguma linha (ou seja, se a conta foi ativada com sucesso)
            return true;                                                                                            // Se a query afetou alguma linha, retorna true indicando que a conta foi ativada com sucesso
        } else {
            return false;                                                                                           // Se a query não afetou nenhuma linha, retorna false indicando que a ativação falhou
        }
    }



    function logout(){}

    function apagarConta(){}
    
    function isAdmin(){                                                                                             // Função para verificar se o utilizador é um administrador
        if($_SESSION["user"]["RoleID"] == 1){                                                                       // Verifica se a sessão do utilizador está iniciada e se o utilizador é um administrador
            return true;                                                                                            // Se for um administrador, retorna true
        }else{                                                                                                      // Se não for um administrador 
            return false;                                                                                           // Se não for um administrador, retorna false
        }
    }