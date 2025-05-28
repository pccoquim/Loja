<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Compra Finalizada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5 text-center">
    <div class="alert alert-success">
        <h2>✅ Compra Finalizada com Sucesso!</h2>
        <p>Obrigado pela sua compra. Um email de confirmação será enviado em breve.</p>
        <a href="../index.php" class="btn btn-primary mt-3">Voltar à Loja</a>
    </div>
</div>

</body>
</html>