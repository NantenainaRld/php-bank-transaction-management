<?php
require_once __DIR__ . "/../models/virementModel.php";

class virementController
{

    //properties
    private $virementModel;

    //construc
    public function __construct($code_banque)
    {
        $this->virementModel = new virementModel($code_banque);
    }


    //-------------PAGE------------

    //PAGE VIREMENT DASHBOARD
    public function showPageVirementDashboard()
    {

        $page = "virement";
        require_once __DIR__ . "/../views/virementDashboardView.php";
    }


    //---------------CONTROLLER------------------

    //CONTROLLER LIST CLIENT ALL
    public function listClientAllController()
    {
        header("Content-Type: application/json");

        echo json_encode($this->virementModel->listClientAll());
    }

    //CONTROLLER RECIPIENT INFO
    public function recipientInfoController()
    {

        header("Content-Type: application/json");
        $json = json_decode(file_get_contents("php://input"), true);

        echo json_encode($this->virementModel->recipientInfo(
            $json["num_compteE"],
            $json["num_compteB"]
        ));
    }

    //CONTROLLER ADD VIREMENT
    public function addVirementController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            header("Content-Type: application/json");
            $json = json_decode(file_get_contents("php://input"), true);
            $response = null;

            //input empty
            if (
                $json["num_compteE"] === "" ||
                $json["num_compteB"] === "" ||
                $json["montantVirement"] === ""
            ) {
                $response = ["message" => "input empty"];
            }
            //input !empty
            else {
                //is recipient exist ?
                $found = $this->virementModel->isRecipientExist(
                    $json["num_compteE"],
                    $json["num_compteB"]
                );

                //recipient !exist
                if ($found === false) {
                    $response = [
                        "message" => "recipient not found",
                        "message_value" => "Le destinataire numéro <b>"
                            . $json["num_compteB"] . "</b> n'existe pas ."
                    ];
                }
                //recipient exist
                elseif ($found === true) {

                    //montantVirement invalid
                    if ((float)$json["montantVirement"] < 50) {
                        $response = [
                            "message" => "montant invalid",
                            "message_value" => "Le montant minimum est <b>50 Ar</b> ."
                        ];
                    }
                    //montant valid
                    else {
                        //is solde sufficient ?
                        $sufficient = $this->virementModel->isSoldeSufficient(
                            $json["num_compteE"],
                            $json["montantVirement"]
                        );

                        //solde !sufficient
                        if ($sufficient === false) {
                            $response = [
                                "message" => "solde !sufficient",
                                "message_value" => "<b>Solde</b> insuffisant ."
                            ];
                        }
                        //solde sufficient
                        elseif ($sufficient === true) {
                            $result = $this->virementModel->addVirement(
                                $json["num_compteE"],
                                $json["num_compteB"],
                                $json["montantVirement"]
                            );

                            //success
                            if ($result === "success") {
                                $response = [
                                    "message" => "success",
                                    "message_value" => "Virement effectué avec succès .",
                                    "update_solde" => $this->virementModel->updateSolde(
                                        $json["num_compteE"],
                                        $json["num_compteB"],
                                        $json["montantVirement"]
                                    )
                                ];
                            }
                            //error
                            else {
                                $response = [
                                    "message" => "error add",
                                    "error_message" => $result
                                ];
                            }
                        }
                        //error
                        else {
                            $response = [
                                "message" => "error",
                                "error_message" => $sufficient
                            ];
                        }
                    }
                }
                //error
                else {
                    $response = [
                        "message" => "error",
                        "error_message" => $found
                    ];
                }
            }

            echo json_encode($response);
        }
    }

    //CONTROLLER LIST VIREMENT ALL
    public function listVirementAllController()
    {

        header("Content-Type: application/json");

        $list = $this->virementModel->listVirementAll();
        //list empty
        if (empty($list) || $list == null) {
            echo json_encode("list empty");
        }
        //list !empty
        else {
            echo json_encode($list);
        }
    }

    //CONTROLLER PRINT VIREMENT
    public function printVirementController()
    {

        $codeVirement = $_GET['code_virement'];
        //print virement
        $this->virementModel->printVirement($codeVirement);
    }

    //CONTROLLER DELETE VIREMENT
    public function deleteVirementController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "DELETE") {

            header("Content-Type: application/json");

            $json = json_decode(file_get_contents("php://input"), true);

            //delete virement
            echo json_encode($this->virementModel->deleteVirement($json['codeVirement']));
        }
    }

    //CONTROLLER SEARCH VIREMENT
    public function searchVirementController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            header("Content-Type: application/json");
            $json  = json_decode(file_get_contents("php://input"), true);

            $response = $this->virementModel->searchVirement(
                $json['codeVirement'],
                $json['num_compteE'],
                $json['num_compteB'],
                $json['dateDu'],
                $json['dateAu']
            );
            //not found
            if (empty($response) || $response == null) {
                echo json_encode("not found");
            }
            //found
            else {
                echo json_encode($response);
            }
        }
    }
}