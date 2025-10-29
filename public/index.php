<?php
// start session
session_start();

// database open
require_once __DIR__ . "/../config/database.php";
$db = new db();

//recup route/action
$route = $_GET['route'] ?? "not found";

//-------------------------CONTROLLERS INSTANCE----------

//banque
require_once __DIR__ . "/../controllers/banqueController.php";
//client
require_once __DIR__ . "/../controllers/clientController.php";
//depot
require_once __DIR__ . "/../controllers/depotController.php";
//virement
require_once __DIR__ . "/../controllers/virementController.php";
//pret
require_once __DIR__ . "/../controllers/pretController.php";
//rendu
require_once __DIR__ . "/../controllers/renduController.php";

$banqueController = null;
$clientController = null;
$depotController = null;
$virementController = null;
$pretController = null;
$renduController = null;
if (!isset($_SESSION['code-banque'])) {
    $banqueController = new banqueController("");
} else {
    $banqueController = new banqueController($_SESSION['code-banque']);
    $clientController = new clientController($_SESSION['code-banque']);
    $depotController = new depotController($_SESSION['code-banque']);
    $virementController = new virementController($_SESSION['code-banque']);
    $pretController = new pretController($_SESSION['code-banque']);
    $renduController = new renduController($_SESSION['code-banque']);
}




//switch route
switch ($route) {


    //------------------------------PAGE---------s----------

    //page dashboard / client
    case 'dashboard':
        # loged
        if (isset($_SESSION['code-banque'])) {
            $banqueController->showPageDashboard();
        }
        # !loged
        else {
            header("Location: ../public/index.php?route=login");
        }
        break;
    //page dashboard / not found
    case 'not found':
        header("Location: ../public/index.php?route=dashboard");
        break;
    //page create account admin
    case 'create_account':
        $banqueController->showPageCreateAccount();
        break;
    // page login
    case 'login':
        # loged
        if (isset($_SESSION['code-banque'])) {
            header("Location: ../public/index.php?route=dashboard");
        }
        # !loged
        else {
            $banqueController->showPageLogin();
        }
        break;
    //page depot dashboard
    case 'depot':
        # loged
        if (isset($_SESSION['code-banque'])) {
            $depotController->showPageDepotDashboard();
        }
        # !loged
        else {
            header("Location: ../public/index.php?route=login");
        }
        break;
    //page virement
    case 'virement':
        # loged
        if (isset($_SESSION['code-banque'])) {
            $virementController->showPageVirementDashboard();
        }
        # !loged
        else {
            header("Location: ../public/index.php?route=login");
        }
        break;
    //page pret
    case 'pret':
        # loged
        if (isset($_SESSION['code-banque'])) {
            $pretController->showPagePretDashboard();
        }
        # !loged
        else {
            header("Location: ../public/index.php?route=login");
        }
        break;
    //page rendu
    case 'rendu':
        # loged
        if (isset($_SESSION['code-banque'])) {
            $renduController->showPageRenduDashboard();
        }
        # !loged
        else {
            header("Location: ../public/index.php?route=login");
        }
        break;
    //page internet error
    case 'internet_error':
        require_once __DIR__ . "/../views/internetError.php";
        break;


    //----------------------------------CONTROLLER---------

    //controller create account admin
    case 'create_account/controller':
        $banqueController->addBankController();
        break;
    // controller login
    case 'login/controller':
        $banqueController->isLoginCorrectController();
        break;
    //controller forgot password
    case 'forgot_password/controller':
        $banqueController->forgotPasswordController();
        break;
    //controller bank info
    case 'bank_info/controller':
        $banqueController->bankInfoController();
        break;
    //controller logout
    case 'logout/controller':
        $banqueController->logoutController();
        break;
    //controller update bank
    case 'update_bank/controller':
        $banqueController->updateBankController();
        break;

    //controller add client
    case 'add_client/controller':
        $clientController->addClientController();
        break;
    //controller list client all
    case 'list_client_all/controller':
        $clientController->listClientAllController();
        break;
    //controller update client
    case 'update_client/controller':
        $clientController->updateClientController();
        break;
    //controller delete client
    case 'delete_client/controller':
        $clientController->deleteClientController();
        break;
    //controller search client
    case 'search_client/controller':
        $clientController->searchClientController();
        break;
    //controller depot / list client all
    case 'depot/list_client_all/controller':
        $depotController->listClientAllController();
        break;
    //controler depot / add depot
    case 'depot/add_depot/controller':
        $depotController->addDepotController();
        break;
    //controller depot / list depot all
    case 'depot/list_depot_all/controller':
        $depotController->listDepotAllController();
        break;
    //controller depot / update depot
    case 'depot/update_depot/controller':
        $depotController->updateDepotController();
        break;
    //controller depot / delete depot
    case 'depot/delete_depot/controller':
        $depotController->deleteDepotController();
        break;
    //controller depot / search client
    case 'depot/search_client/controller':
        $depotController->searchClientController();
        break;
    //controller depot / search depot
    case 'depot/search_depot/controller':
        $depotController->searchDepotController();
        break;
    //controller virement / list client all
    case 'virement/list_client_all/controller':
        $virementController->listClientAllController();
        break;
    //controller virement / recipient info
    case 'virement/recipient_info/controller':
        $virementController->recipientInfoController();
        break;
    case 'virement/add_virement/controller':
        $virementController->addVirementController();
        break;
    //controller list virement all
    case 'virement/list_virement_all/controller':
        $virementController->listVirementAllController();
        break;
    //controller print virement
    case 'virement/print_virement/controller':
        $virementController->printVirementController();
        break;
    //controller virement / delete virement
    case 'virement/delete_virement/controller':
        $virementController->deleteVirementController();
        break;
    //controller virement / search virement
    case 'virement/search_virement/controller':
        $virementController->searchVirementController();
        break;
    //controller pret / add pret
    case 'pret/add_pret/controller':
        $pretController->addPretController();
        break;
    //controller pret / list pret all
    case 'pret/list_pret_all/controller':
        $pretController->listPretAllController();
        break;
    //controller pret / delete pret
    case 'pret/delete_pret/controller':
        $pretController->deletePretController();
        break;
    //controller pret / notify pret
    case 'pret/notify_pret/controller':
        $pretController->notifyPretController();
        break; //controller pret / notify pret
    case 'rendu/notify_rendu/controller':
        $renduController->notifyRenduController();
        break;
    //controller pret / search pret
    case 'pret/search_pret/controller':
        $pretController->searchPretController();
        break;
    //controller rendu / list pret all
    case 'rendu/list_pret_all/controller':
        $renduController->listPretAllController();
        break;
    //controller rendu / add rendu
    case 'rendu/add_rendu/controller':
        $renduController->addRenduController();
        break;
    //controller rendu / list rendu all
    case 'rendu/list_rendu_all/controller':
        $renduController->listRenduAllController();
        break;
    //controller rendu / delete rendu
    case 'rendu/delete_rendu/controller':
        $renduController->deleteRenduController();
        break;
    case 'total':
        $pretController->total();
        break;
    case 'total_client':
        $clientController->total();
        break;


    //NOT FOUND
    default:
        require_once __DIR__ . "/../views/notFoundView.php";
        break;
}


//database close
$db->dbClose();
