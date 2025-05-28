<?php
    session_start();
    require_once("../api/db.php");
    require_once("../api/auth.php");

    $msg = "";
    $redirect = false;

    if (isset($_GET['email']) && isset($_GET['token'])) {
        $email = $_GET['email'];
        $token = $_GET['token'];

        $status = ativarConta($email, $token);

        if ($status === true) {
            $msg = "<div class='alert alert-success'>Conta ativada com sucesso! A redirecionar para o login...</div>";
            $redirect = true;
        } elseif ($status === "already_active") {
            $msg = "<div class='alert alert-warning'>A conta já se encontra ativada.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Token inválido ou conta não encontrada.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Parâmetros em falta.</div>";
    }
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ativação de Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php if ($redirect): ?>
        <meta http-equiv="refresh" content="5;url=login.php">
    <?php endif; ?>
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?= $msg ?>
            </div>
        </div>
    </div>
</body>
</html>
