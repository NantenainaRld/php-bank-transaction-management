<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création du compte - Banque Admin</title>
    <!-- boostrap include css -->
    <link rel="stylesheet" href="../public/bootstrap/css/bootstrap.min.css">
    <!-- font awesome include css -->
    <link rel="stylesheet" href="../public/fontawesome-free-6.7.2-web/css/all.css">
</head>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5 shadow p-3">

                    <h3 class="text-center mt-4">
                        <!-- title form -->
                        <i class="fas fa-user-shield me-2 text-danger"></i>Création du compte Admin banque
                    </h3>

                    <!-- alert success -->
                    <?php if (isset($_SESSION['message-add-bank-success'])): ?>
                        <div class="alert alert-info mt-3 fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <?= $_SESSION['message-add-bank-success']; ?>
                        </div>
                        <a href="../public/index.php?route=login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter</a>
                        <?php unset($_SESSION['message-add-bank-success']); ?>
                    <?php else: ?>
                        <!-- alert message -->
                        <?php if (isset($_SESSION['message-add-bank'])): ?>
                            <div class="alert alert-dismissible alert-warning mt-3 fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <?= $_SESSION['message-add-bank']; ?>
                                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['message-add-bank']); ?>
                        <?php endif; ?>
                        <!-- form -->
                        <form action="../../public/index.php?route=create_account/controller" method="POST">

                            <!-- div nomBanque -->
                            <div class="mb-4 form-group mt-5 mx-4">
                                <label for="nom-banque" class="form-label">
                                    <i class="fas fa-address-card me-2"></i>
                                    Nom de la banque
                                </label>
                                <input type="text" value="<?php if (isset($_SESSION['value-add-bank-nom-banque']))
                                                                echo $_SESSION['value-add-bank-nom-banque']; ?>"
                                    id="nom-banque" name="nom-banque" class="form-control" required>
                            </div>

                            <!-- div emailBanque -->
                            <div class="mb-4 form-group mt-4 mx-4">
                                <label for="email-banque" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    Email
                                </label>
                                <input type="email"
                                    class="form-control"
                                    id="email-banque" value="<?php if (isset($_SESSION['value-add-bank-email-banque']))
                                                                    echo $_SESSION['value-add-bank-email-banque']; ?>"
                                    id="email-banque" name="email-banque" required>
                            </div>
                            <!-- div password -->
                            <div class="mb-4 form-group mt-4 mx-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    Mot de passe
                                </label>
                                <input type="password"
                                    class="form-control"
                                    id="password" id="password" name="password" required>
                            </div>
                            <!-- div repeat password -->
                            <div class="mb-4 form-group mt-4 mx-4">
                                <label for="repeat-password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    Répéter le mot de passe
                                </label>
                                <input type="password"
                                    class="form-control" id="repeat-password" name="repeat-password" required>
                            </div>
                            <!-- div btn submit -->
                            <div class="form-group text-center mb-3">
                                <button type="submit"
                                    class="btn btn-sm btn-primary mb-2">
                                    <i class="fas fa-user-plus me-2"></i>Créer</button><br>
                                <a href="../public/index.php?route=login" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <!-- bootstrap include js -->
    <script src="../public/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- font awesome include js -->
    <script src="../public/fontawesome-free-6.7.2-web/js/all.js"></script>
</body>

</html>