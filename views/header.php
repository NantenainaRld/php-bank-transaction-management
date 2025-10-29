<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php switch ($page) {
            case 'client':
                echo "Tableau de bord - Client";
                break;
            case 'depot':
                echo "Tableau de bord - Dépôt";
                break;
            case 'virement':
                echo "Tableau de bord - Virement";
                break;
            case 'pret':
                echo "Tableau de bord - Prêt";
                break;
            case 'rendu':
                echo "Tableau de bord - Rendu";
                break;
            default:
                header("Location: ../public/index.php?route=notfound");
                break;
        } ?>
    </title>
    <!-- boostrap include css -->
    <link rel="stylesheet" href="../public/bootstrap/css/bootstrap.min.css">
    <!-- font awesome include css -->
    <link rel="stylesheet" href="../public/fontawesome-free-6.7.2-web/css/all.css">
    <link rel="stylesheet" href="../public/bootstrap/css/dashboard.css">
</head>

<body>
    <div class="row">
        <!-- first column -->
        <div class="col-md-2 bg-light aside">

            <!--info bank  -->
            <div class="aside-header">
                <!-- bankName -->
                <a href="#" class="text-decoration-none bank-name text-secondary" id="bank-name-aside">
                </a>
                <!-- email bank -->
                <p class="navbar-brand display-small contact-info" id="email-bank-aside"></p>
            </div>

            <!-- navigation -->
            <nav class="aside-nav">
                <ul class="nav nav-pills nav-justified flex-column text-center">

                    <!-- li client -->
                    <li class="nav-item">
                        <a href="../public/index.php?route=dashboard" class="nav-link 
                        <?php if ($page === "client") echo 'active'; ?>"><i class="fas fa-user me-2"></i> Client</a>
                    </li>
                    <li class="nav-item">
                        <a href="../public/index.php?route=depot" class="nav-link 
                        <?php if ($page === "depot") echo 'active'; ?>"><i class="fas fa-arrow-down me-2"></i>
                            Dépot</a>
                    </li>
                    <li class="nav-item">
                        <a href="../public/index.php?route=virement" class="nav-link
                        <?php if ($page === "virement") echo 'active'; ?>"><i class="fas fa-exchange-alt me-2"></i>
                            Virement</a>
                    </li>
                    <li class="nav-item">
                        <a href="../public/index.php?route=pret" class="nav-link
                        <?php if ($page === "pret") echo 'active'; ?>"><i class="fas fa-hand-holding-dollar me-2"></i>
                            Prêt</a>
                    </li>
                    <li class="nav-item">
                        <a href="../public/index.php?route=rendu" class="nav-link 
                        <?php if ($page === "rendu") echo 'active'; ?>">
                            <i class="fas fa-undo me-2"></i> Remboursement
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- modifier et deconnecter  -->
            <div class="aside-actions">
                <button type="button" id="btn-update-bank" class="btn btn-outline-secondary"><i
                        class="fas fa-cog me-2"></i> Modifier</button>
                <a href="../public/index.php?route=logout/controller" class="btn btn-danger"><i
                        class="fas fa-sign-out-alt me-2"></i> Déconnecter</a>
            </div>

            <!-- modal update bank -->
            <div class="modal fade" id="modal-update-bank" tabindex="-1" aria-labelledby="modal-title-update-bank"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <!-- header-->
                        <div class="modal-header bg-dark text-light" id="modal-title-update-bank">
                            <i class="fas fa-user-edit   me-2"></i>
                            <h5 class=" modal-title">Modification des informations d'admin</h5>
                        </div>

                        <!-- body-->
                        <div class="modal-body">
                            <!-- div nomBank -->
                            <div class="mb-4">
                                <label for="" class="form-label">
                                    <i class="fas fa-address-card me-2"></i>
                                    Nom de la banque</label>
                                <input type="text" placeholder="Nom de la banque" id="nom-bank-update" required
                                    class="form-control">
                            </div>
                            <!-- div emailBanque -->
                            <div class="mb-4">
                                <label for="" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    Email</label>
                                <input type="email" placeholder="Votre adresse email" pattern=".+@.+\..+"
                                    title="Veuiller entrer une adresse email valide (ex: nom@exemple.com)"
                                    id="email-bank-update" required class="form-control">
                                <p class="form-text text-danger" id="text-email-incorrect" style="display:none;">Adresse
                                    email invalide</p>
                            </div>
                            <!-- div password -->
                            <div class="mb-2">
                                <label for="" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    Mot de passe</label>
                                <input type="password" class="form-control">
                                <p class="form-text">(Laisser vide si vous ne voulez pas le modifier)</p>
                            </div>
                        </div>

                        <!-- footer  -->
                        <div class="modal-footer d-flex">
                            <button type="button" id="btn-save-update-bank" class="btn btn-primary btn-sm"><i
                                    class="fas fa-check me-2"></i>
                                Enregistrer
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- second column / page -->
        <div class="col-md-10 bg-light fluid px-4">

            <!--search -->
            <div class="row border shadow rounded ">
                <div class="col-12">

                    <!-- search title -->
                    <div class="row text-center text-secondary p-1 fw-bold mb-2" style="background-color: #e9ecef;">
                        <div class="col-12">
                            Rechercher
                        </div>
                    </div>

                    <!-- search input-->
                    <div class="row rounded mx-1 border border-light p-2 mb-2" style="background-color: #e9ecef;">
                        <!-- search view -->
                        <?php switch ($page) {
                            case 'client':
                                require_once __DIR__ . "/searchClientView.php";
                                break;
                            case 'depot':
                                require_once __DIR__ . "/searchDepotView.php";
                                break;
                            case 'virement':
                                require_once __DIR__ . "/searchVirementView.php";
                                break;
                            case 'pret':
                                require_once __DIR__ . "/searchPretView.php";
                                break;
                            case 'rendu':
                                // require_once __DIR__ . "/searchPretView.php";
                                break;
                            default:
                                header("Location: ../public/index.php?route=notfound");
                                break;
                        } ?>
                    </div>
                </div>
            </div>

            <!-- div btn add -->
            <div class="row rounded mt-5">
                <?php switch ($page):
                    case 'client':
                        echo "<button type=\"button\" id=\"btn-add-client\"
                        class=\"btn btn-sm btn-primary ms-2 w-auto\">
                        <i class=\"fas fa-user-plus me-2\"></i>Ajouter</button>";
                        break;
                    case 'depot':
                        break;
                    case 'virement':
                        break;
                    case 'pret':
                        break;
                    case 'rendu':
                        break;
                endswitch; ?>
            </div>