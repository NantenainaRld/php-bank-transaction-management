<?php
require_once __DIR__ . "/../config/database.php";

class depotModel extends db
{
    //properties
    private $code_banque;

    //construct 
    public function __construct($code_banque)
    {
        parent::__construct();
        $this->code_banque = (int)$code_banque;
    }

    //LIST CLIENT ALL
    public function listClientAll()
    {
        $list = [];
        $response = null;

        try {
            $query = $this->mysqli->prepare("SELECT numCompte, Nom, Prenoms, 
            solde FROM client WHERE code_banque = ? ORDER BY Nom ASC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result = $query->get_result();

            //list empty
            if ($result->num_rows <= 0) {
                $response = ["message" => "list empty"];
            }
            //list not empty
            else {
                while ($row = $result->fetch_assoc()) {
                    $list[] = $row;
                }
                $response = ["message" => "list not empty",  "list" => $list];
            }
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php depot / list client all : " . $e->getMessage()
            ];
        }

        return $response;
    }

    //SEARCH CLIENT
    public function searchClient($search)
    {

        $search = "%" .  trim($search)  . "%";
        $list = [];
        $response = null;

        try {
            $query = $this->mysqli->prepare("SELECT numCompte, Nom, Prenoms, 
            solde FROM client WHERE code_banque = ? AND (Nom LIKE ? 
            OR Prenoms LIKE ? OR numCompte LIKE ? ) ORDER BY Nom ASC;");
            $query->bind_param(
                "isss",
                $this->code_banque,
                $search,
                $search,
                $search,
            );
            $query->execute();
            $result = $query->get_result();

            //not found
            if ($result->num_rows <= 0) {
                $response = ["message" => "not found"];
            }
            //found
            else {
                while ($row = $result->fetch_assoc()) {
                    $list[] = $row;
                }
                $response = ["message" => "found", "list" => $list];
            }
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php depot / search client : " . $e->getMessage()
            ];
        }

        // $response["message"] = "not found";
        return $response;
    }

    //ADD DEPOT
    public function addDepot($num_compte, $montantDepot)
    {
        $response = ["message" => "success"];
        // $montantDepot = (float)$montantDepot;
        $montantDepot = (float)$montantDepot;
        $num_compte = (int)$num_compte;

        //montant < 50
        if ($montantDepot < 50) {
            $response = ["message" => "montant invalid"];
        }
        //montant > 50
        else {

            //first code depot
            $codeDepot = "DP-" . date("YmdHi") . "-" . substr(
                str_shuffle("ABCDEFGHIJKLMNSOPQRSTUVWXYZ"),
                0,
                3
            ) . strval(rand(000, 999));
            //is codeDepot exist?
            try {
                $query = $this->mysqli->prepare("SELECT codeDepot FROM depot
                WHERE codeDepot =?;");
                $query->bind_param("s", $codeDepot);
                $query->execute();
                $result = $query->get_result();

                //codeDepot exist then reshuffle
                if ($result->num_rows > 0) {
                    $codeDepot = "DP-" . date("YmdHi") . "-" . substr(
                        str_shuffle("ABCDEFGHIJKLMNSOPQRSTUVWXYZ"),
                        0,
                        3
                    ) . strval(rand(000, 999));
                }
            } catch (Exception $e) {
                $response = [
                    "message" => "error",
                    "error_message" => "Erreur php depot / codeDepot exist :" . $e->getMessage()
                ];
            }

            //add depot
            try {
                $query = $this->mysqli->prepare("INSERT INTO depot
             VALUES(?, ?, NOW(), ?);");
                $query->bind_param(
                    "sdi",
                    $codeDepot,
                    $montantDepot,
                    $num_compte
                );
                $query->execute();
            } catch (Exception $e) {
                $response = [
                    "message" => "error",
                    "error_message" => "Erreur php depot / add depot : " . $e->getMessage()
                ];
            }

            // update client solde
            try {
                $query = $this->mysqli->prepare("UPDATE client SET solde = solde + ?
             WHERE numCompte =?;");
                $query->bind_param("di", $montantDepot, $num_compte);
                $query->execute();
            } catch (Exception $e) {
                $response = [
                    "message" => "error",
                    "error_message" => "Erreur php depot / update client solde : " . $e->getMessage()
                ];
            }
        }

        return $response;
    }

    //LIST DEPOT ALL
    public function listDepotAll()
    {
        $list = [];
        $response = null;

        try {
            $query = $this->mysqli->prepare("SELECT * FROM depot 
            JOIN client ON numCompte = num_compte WHERE code_banque =?
             ORDER BY dateDepot DESC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result = $query->get_result();

            //list empty
            if ($result->num_rows <= 0) {
                $response = ["message" => "list empty"];
            }
            //list not empty
            else {
                while ($row = $result->fetch_assoc()) {
                    $list[] = $row;
                }
                $response = [
                    "message" => "list not empty",
                    "list" => $list
                ];
            }
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php depot / list depot all : " . $e->getMessage()
            ];
        }
        return $response;
    }

    //SEARCH DEPOT
    public function searchDepot(
        $codeDepot,
        $num_compte,
        $dateDu,
        $dateAu
    ) {
        $codeDepot = trim($codeDepot);
        $num_compte = trim($num_compte);
        $dateDu = trim($dateDu);
        $dateAu = trim($dateAu);

        $list = [];
        $query = null;
        $response = null;

        try {
            //codeDepot empty
            if ($codeDepot === "") {
                //num_compte empty
                if ($num_compte === "") {
                    //dateDu empty
                    if ($dateDu === "") {
                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST ALL
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                ORDER BY dateDepot DESC;");
                            $query->bind_param("i", $this->code_banque);
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) <= ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "is",
                                $this->code_banque,
                                $dateAu
                            );
                        }
                    }
                    //dateDu !empty
                    else {
                        $dateDu  = date("Y-m-d", strtotime($dateDu));

                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST dateDu
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) >= ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "is",
                                $this->code_banque,
                                $dateDu
                            );
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu - dateDu
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) BETWEEN ? AND ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "iss",
                                $this->code_banque,
                                $dateDu,
                                $dateAu
                            );
                        }
                    }
                }
                //num_compte !empty
                else {
                    $num_compte = "%" . $num_compte . "%";
                    //dateDu empty
                    if ($dateDu === "") {
                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST num_compte
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND num_compte LIKE ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "is",
                                $this->code_banque,
                                $num_compte
                            );
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu - num_compte
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) <= ? AND num_compte LIKE ?
                                ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "iss",
                                $this->code_banque,
                                $dateAu,
                                $num_compte
                            );
                        }
                    }
                    //dateDu !empty
                    else {
                        $dateDu  = date("Y-m-d", strtotime($dateDu));

                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST dateDu - num_compte
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) >= ? AND num_compte LIKE ?
                                ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "iss",
                                $this->code_banque,
                                $dateDu,
                                $num_compte
                            );
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu - dateDu - num_compte
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) BETWEEN ? AND ?
                                AND num_compte LIKE ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "isss",
                                $this->code_banque,
                                $dateDu,
                                $dateAu,
                                $num_compte
                            );
                        }
                    }
                }
            }
            //codeDepot !empty
            else {
                $codeDepot = "%" . $codeDepot . "%";
                //num_compte empty
                if ($num_compte === "") {
                    //dateDu empty
                    if ($dateDu === "") {
                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST codeDepot
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND codeDepot LIKE ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "is",
                                $this->code_banque,
                                $codeDepot
                            );
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu - codeDepot
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) <= ? AND codeDepot LIKE ?
                                ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "iss",
                                $this->code_banque,
                                $dateAu,
                                $codeDepot
                            );
                        }
                    }
                    //dateDu !empty
                    else {
                        $dateDu  = date("Y-m-d", strtotime($dateDu));

                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST dateDu - codeDepot
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) >= ? AND codeDepot LIKE ?
                                ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "iss",
                                $this->code_banque,
                                $dateDu,
                                $codeDepot
                            );
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu - dateDu
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) BETWEEN ? AND ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "iss",
                                $this->code_banque,
                                $dateDu,
                                $dateAu
                            );
                        }
                    }
                }
                //num_compte !empty
                else {
                    $num_compte = "%" . $num_compte . "%";
                    //dateDu empty
                    if ($dateDu === "") {
                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST num_compte - codeDepot
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND num_compte LIKE ? AND codeDepot LIKE ?
                                ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "iss",
                                $this->code_banque,
                                $num_compte,
                                $codeDepot
                            );
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu - num_compte - codeDepot
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) <= ? AND num_compte LIKE ?
                                AND codeDepot LIKE ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "isss",
                                $this->code_banque,
                                $dateAu,
                                $num_compte,
                                $codeDepot
                            );
                        }
                    }
                    //dateDu !empty
                    else {
                        $dateDu  = date("Y-m-d", strtotime($dateDu));

                        //dateAu empty
                        if ($dateAu === "") {
                            //--LIST dateDu - num_compte - codeDepot
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) >= ? AND num_compte LIKE ?
                                AND codeDepot LIKE ? ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "isss",
                                $this->code_banque,
                                $dateDu,
                                $num_compte,
                                $codeDepot
                            );
                        }
                        //dateAu !empty
                        else {
                            $dateAu  = date("Y-m-d", strtotime($dateAu));
                            //--LIST dateAu - dateDu - num_compte - codeDepot
                            $query = $this->mysqli->prepare("SELECT * FROM depot 
                                JOIN client ON numCompte = num_compte WHERE code_banque =?
                                AND DATE(dateDepot) BETWEEN ? AND ?
                                AND num_compte LIKE ? AND codeDepot LIKE ?
                                ORDER BY dateDepot DESC;");
                            $query->bind_param(
                                "issss",
                                $this->code_banque,
                                $dateDu,
                                $dateAu,
                                $num_compte,
                                $codeDepot
                            );
                        }
                    }
                }
            }

            $query->execute();
            $result = $query->get_result();

            //not found
            if ($result->num_rows <= 0) {
                $response = ["message" => "not found"];
            }
            //found
            else {
                while ($row = $result->fetch_assoc()) {
                    $list[] = $row;
                }
                $response = [
                    "message" => "found",
                    "list" => $list
                ];
            }
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php depot / search depot : " . $e->getMessage()
            ];
        }

        return $response;
    }

    //UPDATE DEPOT
    public function updateDepot(
        $codeDepot,
        $montantDepot,
        $dateDepot,
    ) {
        $response = null;

        try {
            $codeDepot = trim($codeDepot);
            $montantDepot = (float)trim($montantDepot);
            $dateDepot = date("Y-m-d", strtotime(trim($dateDepot)));

            $query = $this->mysqli->prepare("UPDATE depot SET montantDepot = ?,
            dateDepot = CONCAT(?,' ', TIME(dateDepot)) WHERE codeDepot =?;");
            $query->bind_param(
                "dss",
                $montantDepot,
                $dateDepot,
                $codeDepot
            );
            $query->execute();
            $response = ["message" => "success"];
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php depot / update depot : " . $e->getMessage()
            ];
        }

        return $response;
    }

    //DELETED DEPOT
    public function deleteDepot($codeDepot)
    {

        $codeDepot = trim($codeDepot);
        $response = null;

        try {
            $query = $this->mysqli->prepare("DELETE FROM depot WHERE codeDepot =?;");
            $query->bind_param("s", $codeDepot);
            $query->execute();
            $response = ["message" => "success"];
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php depot / delete depot : " . $e->getMessage()
            ];
        }

        return $response;
    }
}