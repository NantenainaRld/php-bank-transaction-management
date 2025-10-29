<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- boostrap include css -->
    <link rel="stylesheet" href="../public/bootstrap/css/bootstrap.min.css">
    <!-- font awesome include css -->
    <link rel="stylesheet" href="../public/fontawesome-free-6.7.2-web/css/all.css">
    <style>
        .styled-link {
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
            text-decoration: none;
            /* Supprime le soulignement par défaut */
        }

        .styled-link:hover {
            text-decoration: underline;
            /* Ajoute un soulignement au survol pour l'indication */
        }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card p-4">
                    <h3 class="text-center mb-4">LOGIN ADMIN</h3>
                    <form action="../public/index.php?route=login/controller" method="POST">

                        <!-- alert -->
                        <?php if (isset($_SESSION['message-login'])): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i><?= $_SESSION['message-login']; ?>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['message-login']); ?>
                        <?php endif; ?>
                        <!-- alert forgot password success-->
                        <?php if (isset($_SESSION['message-login-forgot-password-success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i><?= $_SESSION['message-login-forgot-password-success']; ?>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['message-login-forgot-password-success']); ?>
                        <?php endif; ?>

                        <!-- email-banque -->
                        <div class="form-group">
                            <label for="email-banque">Adresse email</label>
                            <input type="email" value="<?php if (isset($_SESSION['email-banque-login']))
                                                            echo $_SESSION['email-banque-login']; ?>"
                                class="form-control"
                                name="email-banque" id="email-banque" required>
                        </div>

                        <!-- password -->
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>

                        <!-- btn se connecter-->
                        <div class="form-group text-center">
                            <button type="submit"
                                class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter</button>
                        </div>

                        <!-- btn link -->
                        <div class="mt-3 text-center gap-4">
                            <a class="btn btn-link styled-link" href="../public/index.php?route=create_account">S'inscrire</a>
                            <button type="button" class="btn btn-link styled-link" id="btn-forgot">Mot de passe oublié</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            //emailBanque
            const emailBanque = document.getElementById("email-banque");
            //btn forgot password
            const btnForgot = document.getElementById("btn-forgot");

            //btn forgot clicked
            btnForgot.addEventListener("click", () => {
                window.location.href = "../public/index.php?route=forgot_password/controller&email=" + emailBanque.value;
            });
        });
    </script>
    <!-- bootstrap include js -->
    <script src="../public/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- font awesome include js -->
    <script src="../public/fontawesome-free-6.7.2-web/js/all.js"></script>
</body>

</html>