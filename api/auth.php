<?php
    require_once 'db.php';
    require_once 'email.php';

    /**
    * Login de um utilizador
    * @param string $username -> Nome de utilizador ou email
    * @param string $password -> Password do utilizador
    * @return bool -> true se o login foi bem sucedido, false caso contrário
    */
    function login($username,$password)
    {
        global $con;
        // Procurar utilizador por username ou email
        $sql = $con->prepare("SELECT * FROM utilizador WHERE (username = ? OR email = ?) AND active = 1");
        $sql->bind_param("ss", $username, $username); 
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION ["user"] = $row; // Guardar o ID do utilizador na sessão
            // Verificar a password
            if (password_verify($password, $row['password']))
            {
                // Login bem-sucedido
                return true;
            }
        }
        // Login falhado
        return false;
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

    function registo($email,$username,$password,$telemovel,$nif)
    {
        global $con;

         // Verificar se já existe username ou email
        $check = $con->prepare("SELECT id FROM utilizador WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Já existe utilizador com esse email ou username
            return false;
        }

        $check->close();


        $sql = $con->prepare("INSERT INTO utilizador (email, username, password, telemovel, nif, token) VALUES (?, ?, ?, ?, ?, ?)");
        $token = bin2hex(random_bytes(16));                     // Generate a random token
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $sql->bind_param("ssssss", $email, $username, $password, $telemovel, $nif, $token);
        $sql->execute();
        if ($sql->affected_rows > 0) {
            send_email($email, $username, "<a href='http://localhost/loja/views/ativar.php?email=$email&token=$token'>Clique aqui para ativar a sua conta</a>", "Activar a sua conta");
            return true;
        } else {
            return false;
        }
    }

    /**
    * Ativar a conta do utilizador
    * @param string $email -> Email do utilizador
    * @param string $token -> Token de ativação
    * @return bool -> true se a conta foi ativada, false caso contrário
    */
    function ativarConta($email,$token)
    {
        global $con;

         // Verificar se já está ativa
        $check = $con->prepare("SELECT active FROM utilizador WHERE email = ? AND token = ?");
        $check->bind_param("ss", $email, $token);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            return false; // Token ou email inválido
        }

        $row = $result->fetch_assoc();
        if ($row['active'] == 1) {
            return "already_active"; // Já estava ativa
        }
    
        $sql = $con->prepare("UPDATE utilizador SET active = 1, updated_at = Now() WHERE email = ? AND token = ?");
        $sql->bind_param("ss", $_GET["email"], $_GET["token"]);
        $sql->execute();
        if($sql->affected_rows > 0){
            return true;
        }else{
            return false;
        }
    }



    function logout(){}

    function apagarConta(){}

    function isAdmin(){
        if($_SESSION["user"]["RoleID"] == 1){
            return true;
        }else{
            return false;
        }
    }