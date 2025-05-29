<?php                                                           // Início do bloco PHP de administração de produtos
    
    mysqli_report(MYSQLI_REPORT_ERROR);                         // Ativa o relatório de erros do MySQLi
    
    $con = new mysqli("localhost", "root", "", "loja");         // Cria a conexão com a base de dados MySQLi
    if ($con->connect_error) {                                  // Verifica se a conexão foi bem-sucedida
        die("Connection failed: " . $con->connect_error);       // Se a conexão falhar, exibe uma mensagem de erro e encerra o script
    }