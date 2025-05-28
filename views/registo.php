<?php
    session_start();
    require_once '../api/auth.php';

    $error_msg = false;
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email     = $_POST['email'] ?? '';
        $username  = $_POST['username'] ?? '';
        $password  = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $telemovel = $_POST['telemovel'] ?? '';
        $nif       = $_POST['nif'] ?? '';

        //Ver se os campos estão preenchidos
        if (empty($_POST["username"])) {
            $error_msg = true;
            $msg .= "Preencha o campo username.";
        }
        if (empty($_POST["email"])) {
            $error_msg = true;
            $msg .= "Preencha o campo email.";
        }
        if (empty($_POST["telemovel"])) {
            $error_msg = true;
            $msg .= "Preencha o campo telemovel.";
        }
        if (empty($_POST["nif"])) {
            $error_msg = true;
            $msg .= "Preencha o campo nif.";
        }
        if (empty($_POST["password"])) {
            $error_msg = true;
            $msg .= "Preencha o campo password.";
        }
        if (empty($_POST["confirm_password"])) {
            $error_msg = true;
            $msg .= "Preencha o campo confirmar password.";
        }
        if ($_POST["password"] != $_POST["confirm_password"]) {
            $error_msg = true;
            $msg .= "As passwords não coincidem.";
        }

        if (registo($email, $username, $password, $telemovel, $nif)) {
            $error_msg = true;
            $msg = "<div class='alert alert-success'>Registo efetuado com sucesso. Verifique o seu email para ativar a conta.</div>";
        } else {
            $error_msg = true;
            $msg = "<div class='alert alert-danger'>Erro ao efetuar o registo. Email ou utilizador já existem</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php
    if ($error_msg) {
        echo "<div class='position-fixed top-0 end-0 p-3' style='z-index: 1050;'>
                  <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                      $msg
                      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>
              </div>";
    }
    ?>
    <div class="container mt-5 d-flex justify-content-center">
        <div class="w-25">
            <h2 class="text-center mb-4">Registo</h2>
            <form method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Nome de utilizador</label>
                    <input type="text" id="username" class="form-control" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="telemovel" class="form-label">Telemóvel</label>
                    <input type="text" id="telemovel" class="form-control" name="telemovel" required>
                </div>
                <div class="mb-3">
                    <label for="nif" class="form-label">NIF</label>
                    <input type="text" id="nif" class="form-control" name="nif" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Palavra-passe</label>
                    <input type="password" id="password" class="form-control" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar palavra-passe</label>
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" required>
                </div>
                <div class="d-grid justify-content-center">
                    <button type="submit" class="btn btn-success" style="width: 150px;">Registar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
