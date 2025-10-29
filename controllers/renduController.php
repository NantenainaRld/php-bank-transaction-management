<?php
require_once __DIR__ . "/../models/renduModel.php";

class renduController
{

    //properties
    private $renduModel;

    //construc
    public function __construct($code_banque)
    {
        $this->renduModel = new renduModel($code_banque);
    }


    //-----------------------PAGE----------------*****

    //PAGE PRET DASHBOARD
    public function showPageRenduDashBoard()
    {

        $page = "rendu";

        require_once __DIR__ . "/../views/renduDashboardView.php";
    }



    //-------------------CONTROLLER----------------****

    //CONTROLLER LIST PRET ALL
    public function listPretAllController()
    {

        header("Content-Type: application/json");

        $list = $this->renduModel->listPretAll();

        //list empty
        if (empty($list)) {
            echo json_encode("list pret empty");
        }
        //list !empty
        else {
            echo json_encode($list);
        }
    }

    //CONTROLLER ADD RENDU
    public function addRenduController()
    {

        header("Content-Type: application/json");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = json_decode(file_get_contents("php://input"), true);

            $code_pret = trim($json['code_pret']);
            $montantRendu = (float)trim($json['montantRendu']);
            //solde !sufficient
            if (!($this->renduModel->isSoldeSufficient(
                $code_pret,
                $montantRendu
            ))) {
                echo json_encode("solde !sufficient");
            }
            //solde sufficient
            else {
                //add rendu
                echo json_encode($this->renduModel->addRendu(
                    $json['code_pret'],
                    $json['montantRendu']
                ));
            }
        }
    }


    //CONTROLLER LIST RENDU ALL
    public function listRenduAllController()
    {
        header("Content-Type: application/json");

        $list = $this->renduModel->listRenduAll();

        //list empty
        if (empty($list)) {
            echo json_encode("list rendu empty");
        }
        //list !empty
        else {
            echo json_encode($list);
        }
    }

    //CONTROLLER DELETE RENDU
    public function deleteRenduController()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            header("Content-Type: application/json");

            $json = json_decode(file_get_contents("php://input"), true);

            //delete rendu
            echo json_encode($this->renduModel->deleteRendu($json['codeRendu']));
        }
    }

    public function notifyRenduController()
    {
        header("Content-Type: application/json");
        $json = json_decode(file_get_contents("php://input"), true);
        //sendMail
        require_once __DIR__ . "/../models/sendMail.php";

        $sendMail = new sendMail("");

        //internet !ok
        if (!$sendMail->verifyInternet()) {
            echo json_encode("internet error");
        }
        //internet ok
        else {

            echo json_encode($sendMail->sendMailRendu(
                $json['codeRendu'],
                $json['montantRendu'],
                $json['dateRendu'],
                $json['restePaye'],
                $json['situation']
            ));
        }
    }
}
