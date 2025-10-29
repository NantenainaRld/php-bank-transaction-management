<?php
require_once __DIR__ . "/../models/pretModel.php";

class pretController
{

    //properties
    private $pretModel;

    //construc
    public function __construct($code_banque)
    {
        $this->pretModel = new pretModel($code_banque);
    }


    //-----------------------PAGE----------------*****

    //PAGE PRET DASHBOARD
    public function showPagePretDashBoard()
    {

        $page = "pret";

        require_once __DIR__ . "/../views/pretDashboardView.php";
    }


    //---------------------CONTROLLER------------------

    //CONTROLLER ADD PRET
    public function addPretController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            header("Content-Type: application/json");
            $json = json_decode(file_get_contents("php://input"), true);

            //add pret
            echo json_encode(
                $this->pretModel->addPret(
                    $json['num_compte'],
                    $json['montantPret'],
                    $json['duree']
                ) . " " .  $this->pretModel->updatSolde($json['montantPret'], $json['num_compte'])
            );
        }
    }

    //CONTROLLER LIST PRET ALL
    public function listPretAllController()
    {

        header("Content-Type: application/json");

        $list  = $this->pretModel->listPretAll();

        //list pret empty
        if (empty($list)) {
            echo json_encode("list empty");
        }
        //list pret !empty
        else {
            echo json_encode($list);
        }
    }

    //CONTROLLER DELETE PRET
    public function deletePretController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
            header("Content-Type: application/json");
            $json = json_decode(file_get_contents("php://input"), true);

            echo json_encode($this->pretModel->deletePret($json['codePret']));
        }
    }

    //CONTROLLER NOTIFY PRET
    public function notifyPretController()
    {
        header("Content-Type: application/json");
        $json = json_decode(file_get_contents("php://input"), true);
        //sendMail
        require_once __DIR__ . "/../models/sendMail.php";

        $sendMail = new sendMail($json['num_compte']);

        //internet !ok
        if (!$sendMail->verifyInternet()) {
            echo json_encode("internet error");
        }
        //internet ok
        else {

            echo json_encode($sendMail->sendMailPret(
                $json['num_compte'],
                $json['codePret'],
                $json['montantPret'],
                $json['datePret'],
                $json['benefice_banque'],
                $json['duree']
            ));
        }
    }

    //CONTROLLER SEARC PRET
    public function searchPretController()
    {
        header("Content-Type: application/json");
        $json = json_decode(file_get_contents("php://input"), true);

        $list = $this->pretModel->searchPret(
            $json['codePret'],
            $json['num_compte'],
            $json['situation'],
            $json['dateDu'],
            $json['dateAu']
        );
        if (empty($list)) {
            echo json_encode("not found");
        } else {
            echo json_encode($list);
        }
    }
    public function total()
    {
        header("Content-Type: application/json");
        echo json_encode($this->pretModel->totalBenefice());
    }
}
