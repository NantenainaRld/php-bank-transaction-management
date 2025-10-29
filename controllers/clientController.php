<?php
require_once __DIR__ . "/../models/clientModel.php";
require_once __DIR__ . "/../models/sendMail.php";

class clientController
{
    //properties
    private $clientModel;

    //construct
    public function __construct($code_banque)
    {
        $this->clientModel = new clientModel($code_banque);
    }

    //--------------CONTROLLER-------------------

    //CONTROLLER ADD CLIENT
    public function addClientController()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header("Content-Type: application/json");
            $response = null;
            $json = json_decode(file_get_contents("php://input"), true);

            //input empty
            if (
                trim($json['Nom']) === "" || trim($json['Tel']) === ""
                || trim($json['emailClient'] === "")
            ) {
                $response = "Les champs <b>Nom, Téléphone, email</b> sont obligatoires .";
            }
            //input !empty
            else {
                //is emailclient exist? 
                $response = $this->clientModel->isEmailExist($json['emailClient']);

                //email exist
                if ($response === true) {
                    $response = "Cette adresse <b>email</b> existe déjà .";
                }
                //add client
                else {
                    $response = $this->clientModel->addClient(
                        $json['Nom'],
                        $json['Prenoms'],
                        $json['Tel'],
                        $json['emailClient']
                    );
                }
            }

            echo json_encode($response);
        }
    }

    //CONTROLLER LIST CLIENT ALL
    public function listClientAllController()
    {
        header("Content-Type: application/json");

        $list = $this->clientModel->listClientAll();
        echo json_encode($list);
    }

    //CONTROLLER UPDATE CLIENT CLIENT
    public function updateClientController()
    {

        header("Content-Type: application/json");
        $response = null;
        $json = json_decode(file_get_contents("php://input"), true);

        //input empty
        if (
            trim($json['Nom']) === "" || trim($json['Tel']) === ""
            || trim($json['emailClient'] === "")
        ) {
            $response = "Les champs <b>Nom, Téléphone, email</b> sont obligatoires .";
        }
        //input !empty
        else {

            //solde !valid
            if ((int)$json['solde'] < 0) {
                $response = "Le <b>solde</b> saisie est invalide .";
            }
            //solde valid
            else {

                // //is emailclient exist? 
                $response = $this->clientModel->isEmailExistUC(
                    $json['emailClient'],
                    $json['numCompte']
                );

                //email exist
                if ($response === true) {
                    $response = "Cette adresse <b>email</b> existe déjà .";
                }
                //update client
                else {
                    $response = $this->clientModel->updateClient(
                        $json['numCompte'],
                        $json['Nom'],
                        $json['Prenoms'],
                        $json['Tel'],
                        $json['emailClient'],
                        $json['solde']
                    );
                }
            }
        }

        echo json_encode($response);
        // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //     header("Content-Type: application/json");

        //     $json = json_decode(file_get_contents("php://input"), true);

        //     //input empty
        //     if (
        //         trim($json['Nom']) === "" || trim($json['Tel']) === ""
        //         || trim($json['emailClient'] === "")
        //     ) {
        //         echo json_encode("input empty");
        //     }
        //     //input !empty

        //     //email exist
        //     elseif ($this->clientModel->isEmailExistUpdate(
        //         $json['numCompte'],
        //         $json['emailClient']
        //     )) {
        //         echo json_encode("email exist");
        //     }
        //     //update client
        //     else {
        //         echo json_encode(value: $this->clientModel->updateClient(
        //             $json['numCompte'],
        //             $json['Nom'],
        //             $json['Prenoms'],
        //             $json['Tel'],
        //             $json['emailClient'],
        //             $json['solde']
        //         ));
        //     }
        // }
    }

    //CONTROLLER DELETE CLIENT
    public function deleteClientController()
    {

        if ($_SERVER['REQUEST_METHOD'] === "DELETE") {

            header("Content-Type: application/json");
            $json = json_decode(file_get_contents("php://input"), true);

            echo json_encode($this->clientModel->deleteClient($json['numCompte']));
        }
    }

    //CONTROLLE SEARCH CLIETN
    public function searchClientController()
    {
        header("Content-Type: application/json");
        $json = json_decode(file_get_contents("php://input"), true);

        echo json_encode($this->clientModel->searchClient($json['searchValue']));
    }
    public function total()
    {
        header("Content-Type: application/json");
        echo json_encode($this->clientModel->totalClient());
    }
}
