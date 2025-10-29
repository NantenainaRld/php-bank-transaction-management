<?php
require_once __DIR__ . "/../config/database.php";

class clientModel extends db
{
    //properties
    private $code_banque;
    //construc
    public function __construct($code_banque)
    {
        parent::__construct();
        $this->code_banque = (int) $code_banque;
    }

    //ADD CLIENT
    ///is email exist?
    public function isEmailExist($emailClient)
    {
        $find = false;
        try {
            $query = $this->mysqli->prepare("SELECT emailClient FROM client
             WHERE emailClient  = ? AND code_banque = ?;");
            $query->bind_param("si", $emailClient, $this->code_banque);
            $query->execute();
            $result = $query->get_result();

            //email exist
            if ($result->num_rows > 0) {
                $find = true;
            }
        } catch (Exception $e) {
            $find  = "Erreur php add client / email exist : " .  $e->getMessage();
        }

        return $find;
    }
    ///add client
    public function addClient($Nom, $Prenoms, $Tel, $emailClient)
    {
        $response = "success";

        //numCompte
        $numCompte = rand(100000, 999999);
        //is numCompte already exist?
        try {
            $query = $this->mysqli->prepare("SELECT numCompte
             FROM client WHERE numCompte =?;");
            $query->bind_param("i", $numCompte);
            $query->execute();
            $result = $query->get_result();

            //numCompte exist / reshuffle numCompte
            while ($result->fetch_assoc()) {
                $numCompte = rand(100000, 999999);
            }
        } catch (Exception $e) {
            $response  = "Erreur php add client / numCompte exist : " .  $e->getMessage();
        }
        //add client
        try {
            $solde = 0;
            $query = $this->mysqli->prepare("INSERT INTO 
            client VALUES(?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param(
                "issssid",
                $numCompte,
                $Nom,
                $Prenoms,
                $Tel,
                $emailClient,
                $this->code_banque,
                $solde
            );
            $query->execute();
        } catch (Exception $e) {
            $response  = "Erreur php add client : " .  $e->getMessage();
        }

        return $response;
    }

    //LIST CLIENT ALL
    public function listClientAll()
    {
        $response = null;
        $list = [];
        try {
            $query = $this->mysqli->prepare("SELECT * 
             FROM client WHERE code_banque = ? ORDER BY Nom ASC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result = $query->get_result();

            //list empty
            if ($result->num_rows <= 0) {
                $response = ["message" => "list empty"];
            }
            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
            $response = ["message" => "list not empty", "list" => $list];
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php list client all : " .  $e->getMessage()
            ];
        }

        return $response;
    }

    //UPDATE CLIENT
    ///is email exist - updaet client?
    public function isEmailExistUC($emailClient, $numCompte)
    {
        $find = false;
        $numCompte = (int)$numCompte;
        try {
            $query = $this->mysqli->prepare("SELECT emailClient FROM client
             WHERE emailClient  = ? AND numCompte != ?;");
            $query->bind_param(
                "si",
                $emailClient,
                $numCompte
            );
            $query->execute();
            $result = $query->get_result();

            //email exist
            if ($result->num_rows > 0) {
                $find = true;
            }
        } catch (Exception $e) {
            $find  = "Erreur php updateClient / email exist : " .  $e->getMessage();
        }

        return $find;
    }
    //# update client
    public function updateClient(
        $numCompte,
        $Nom,
        $Prenoms,
        $Tel,
        $emailClient,
        $solde
    ) {

        $response = "success";
        $numCompte = (int)$numCompte;
        $solde = (float)$solde;

        try {
            $query = $this->mysqli->prepare("UPDATE client SET 
            Nom = ?, Prenoms = ?, Tel = ?, emailClient=? , solde =?
            WHERE numCompte = ?;");
            $query->bind_param(
                "ssssid",
                $Nom,
                $Prenoms,
                $Tel,
                $emailClient,
                $solde,
                $numCompte
            );
            $query->execute();
        } catch (Exception $e) {
            $response  = "Erreur php update client : " .  $e->getMessage();
        }

        return $response;
    }

    //DELETE CLIENT
    public function deleteClient($numCompte)
    {
        $numCompte = (int)$numCompte;
        $response = "success";

        try {
            $query = $this->mysqli->prepare("DELETE FROM client WHERE numCompte =?;");
            $query->bind_param("i", $numCompte);
            $query->execute();
        } catch (Exception $e) {
            $response  = "Erreur php delete client : " .  $e->getMessage();
        }

        return $response;
    }

    //SEARCH CLIENT
    public function searchClient($search)
    {
        $response = null;
        $search = "%" . $search . "%";
        $list = [];

        try {
            $query = $this->mysqli->prepare("SELECT * FROM client
             WHERE numCompte LIKE ? OR Nom LIKE ? OR Prenoms LIKE ?;");
            $query->bind_param("sss", $search, $search, $search);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $list[] = $row;
                }

                $response = [
                    "message" => "found",
                    "list" => $list
                ];
            }
            //not found
            else {
                $response = ["message" => "not found"];
            }
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php search client : " . $e->getMessage()
            ];
        }

        return $response;
    }
    public function totalClient()
    {
        $query = $this->mysqli->prepare("SELECT COUNT(Nom) AS total FROM client c JOIN banque ON codeBanque = code_banque WHERE codeBanque =?; ");
        $query->bind_param("i", $this->code_banque);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        // $row["total"] = 10;
        return $row["total"];
    }
}
