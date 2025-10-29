<?php
require_once __DIR__ . "/../models/depotModel.php";

class depotController
{

    //properties
    private $depotModel;

    //construc
    public function __construct($code_banque)
    {
        $this->depotModel = new depotModel($code_banque);
    }


    //-----------------PAGE------------------

    //PAGE SHOW DEPOT DASHBOARD
    public function showPageDepotDashboard()
    {
        $page = "depot";
        require_once __DIR__ . "/../views/depotDashboardView.php";
    }


    //----------------CONTROLLER----------------

    //CONTROLLER LIST CLIENT ALL
    public function listClientAllController()
    {

        header("Content-Type: application/json");
        echo json_encode($this->depotModel->listClientAll());
    }

    //CONTROLLER SEARCH CLIENT
    public function searchClientController()
    {
        header("Content-Type: application/json");
        $json = json_decode(file_get_contents("php://input"), true);

        echo json_encode($this->depotModel->searchClient($json['search']));
    }

    //CONTROLLER ADD DEPOT
    public function addDepotController()
    {

        header("Content-Type: application/json");

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $json = json_decode(file_get_contents("php://input"), true);

            //add depot
            echo json_encode($this->depotModel->addDepot(
                $json['num_compte'],
                $json['montantDepot']
            ));
        }
    }

    //CONTROLLER LIST DEPOT ALL
    public function listDepotAllController()
    {

        header("Content-Type: application/json");
        echo json_encode($this->depotModel->listDepotAll());
    }

    //CONTROLLER SEARCH DEPOT
    public function searchDepotController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            header("Content-Type: application/json");

            $json = json_decode(file_get_contents("php://input"), true);

            echo json_encode($this->depotModel->searchDepot(
                $json['codeDepot'],
                $json['num_compte'],
                $json['dateDu'],
                $json['dateAu']
            ));
        }
    }

    //CONTROLLER UPDATE DEPOT
    public function updateDepotController()
    {
        if ($_SERVER["REQUEST_METHOD"] === "PUT") {
            header("Content-Type: application/json");
            $json = json_decode(file_get_contents("php://input"), true);
            $response = null;

            //montant depot < 50
            if ((float)$json['montantDepot'] < 50) {
                $response = [
                    "message" => "input invalid",
                    "message_value" => "Le montant minimum est <b>50 Ar</b> ."
                ];
            }
            //montantDepot > 50
            else {
                //dateDepot empty
                if ($json["dateDepot"] === "") {
                    $response = [
                        "message" => "input invalid",
                        "message_value" => "La <b>date de dépôt</b> est obligatoire ."
                    ];
                }
                //dateDepot !empty
                else {
                    //update depot
                    $response = $this->depotModel->updateDepot(
                        $json["codeDepot"],
                        $json["montantDepot"],
                        $json["dateDepot"]
                    );
                }
            }

            echo json_encode($response);
        }
    }

    //CONTROLLER DELETE DEPOT
    public function deleteDepotController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
            header("Content-Type: application/json");

            $json = json_decode(file_get_contents("php://input"), true);

            //delete depot
            echo json_encode($this->depotModel->deleteDepot($json['codeDepot']));
        }
    }
}