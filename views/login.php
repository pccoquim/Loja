<?php

    session_start();

    require '../api/auth.php';

    $error_message = false;
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if(empty($username) || empty($password)) {
            $error_message = true;
            $message = "Por favor, preencha todos os campos.";
        } else {
            if (login($username, $password)) {
                header("Location: ../index.php"); // Redirecionar após login com sucesso
                exit;
            } else {
                $error_message = true;
                $message = "Credenciais inválidas ou conta inativa.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php
    if ($error_message) {
        echo "<div class='position-fixed top-0 end-0 p-3' style='z-index: 1050;'>
                  <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                      $message
                      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>
              </div>";
    }
    ?>
    <div class="container mt-5 d-flex justify-content-center">
        <div class="w-25">
            <h2 class="text-center mb-4">Login</h2>
            <form method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Email ou Nome de utilizador</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Palavra-passe</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="d-grid justify-content-center">
                    <button type="submit" class="btn btn-success" style="width: 150px;">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
