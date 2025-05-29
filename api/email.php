<?php                                                                   // Início do bloco PHP de administração de produtos
    use PHPMailer\PHPMailer\PHPMailer;                                  // Importa a classe PHPMailer
    use PHPMailer\PHPMailer\Exception;                                  // Importa a classe Exception do PHPMailer
    
    function send_Email($destino, $username, $conteudo, $assunto)       // Função para envio de email, para ativação da conta
    {
        require '../PHPMailer/src/Exception.php';                       // Inclui o ficheiro de exceção do PHPMailer
        require '../PHPMailer/src/PHPMailer.php';                       // Inclui o ficheiro principal do PHPMailer
        require '../PHPMailer/src/SMTP.php';                            // Inclui o ficheiro SMTP do PHPMailer
        require 'secrets.php';                                          // Inclui o ficheiro de segredos onde estão guardados as credenciais do email

        $mail = new PHPMailer(true);                                    // Cria uma instância do PHPMailer

        try {

            $mail->CharSet = "utf-8";                                   //Configurações do PHPMailer
            $mail->isSMTP();                                            // Enviar usando SMTP
            $mail->Host       = 'smtp.sapo.pt';                         // Definir o servidor SMTP para enviar através do Sapo
            $mail->SMTPAuth   = true;                                   // Ativar autenticação SMTP
            $mail->Username   = $EMAIL_SAPO;                            // Nome de utilizador SMTP (email)
            $mail->Password   = $EMAIL_PASS;                            // Password SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Ativar criptografia TLS; use `PHPMailer::ENCRYPTION_STARTTLS` se estiver a usar o porto 587
            $mail->Port       = 465;                                    // Porta TCP para se conectar ao servidor SMTP (465 para SMTPS, 587 para STARTTLS)
            $mail->setFrom($EMAIL_SAPO, $EMAIL_NOME);                   // Definir o remetente do email (email e nome)
            $mail->addAddress($destino, $username);                     // Adicionar um destinatário

            // Conteúdo
            $mail->isHTML(true);                                        // Definir o formato do email como HTML
            $mail->Subject = $assunto;                                  // Assunto do email
            $mail->Body    = $conteudo;                                 // Corpo do email em HTML
            $mail->send();                                              // Enviar o email
            return true;                                                // Retorna true se o email foi enviado com sucesso  
        } catch (Exception $e) {                                        // Se ocorrer um erro ao enviar o email
            return false;                                               // Retorna false se o email não foi enviado 
        }
    }
?>                                                                      <!-- Fim do ficheiro de administração de produtos -->