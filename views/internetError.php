<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de connexion Internet</title>
    <!-- boostrap include css -->
    <link rel="stylesheet" href="../public/bootstrap/css/bootstrap.min.css">
    <!-- font awesome include css -->
    <link rel="stylesheet" href="../public/fontawesome-free-6.7.2-web/css/all.css">


    <style>
        body {
            background-color: #f8f9fa;
        }

        .connection-error-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .connection-error-box {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
        }

        .connection-error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }

        .connection-error-message {
            color: #495057;
            margin-bottom: 1rem;
        }

        .connection-error-advice {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="connection-error-container">
        <div class="connection-error-box">
            <i class="fas fa-wifi connection-error-icon"></i>
            <h2 class="connection-error-message">Erreur de connexion Internet</h2>
            <p class="connection-error-advice">Veuillez vérifier votre connexion réseau et réessayer.</p>
            <a href="javascript:history.back()" class="btn btn-outline-primary mt-3">Retour</a>
        </div>
    </div>


    <!-- bootstrap include js -->
    <script src="../public/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- font awesome include js -->
    <script src="../public/fontawesome-free-6.7.2-web/js/all.js"></script>
</body>

</html>