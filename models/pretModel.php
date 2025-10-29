<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/tfpdf/tfpdf.php";

class pretModel extends db
{
    //properties
    private $code_banque;

    //construct 
    public function __construct($code_banque)
    {
        parent::__construct();
        $this->code_banque = (int)$code_banque;
    }


    //ADD PRET
    public function addPret(
        $num_compte,
        $montantPret,
        $duree
    ) {

        $response = "success";
        $num_compte = (int)$num_compte;
        $montantPret  = (float)$montantPret;
        $duree = (int)$duree;

        //first code pret
        $codePret = "PR-" . date("YmdHi") . "-" . substr(
            str_shuffle("ABCDEFGHIJKLMNSOPQRSTUVWXYZ"),
            0,
            3
        ) . strval(rand(000, 999));
        //is codePret exist?
        try {
            $query = $this->mysqli->prepare("SELECT codePret FROM pret
             WHERE codePret =?;");
            $query->bind_param("s", $codePret);
            $query->execute();
            $result = $query->get_result();

            //codePret exist then reshuffle
            if ($result->num_rows > 0) {
                $codePret = "PR-" . date("YmdHi") . "-" . substr(
                    str_shuffle("ABCDEFGHIJKLMNSOPQRSTUVWXYZ"),
                    0,
                    3
                ) . strval(rand(000, 999));
            }
        } catch (Exception $e) {
            $response = "Erreur php/  pret codePret exist :" . $e->getMessage();
        }

        //add pret
        try {
            $datePret = date("Y-m-d H:i:s", time());
            $query = $this->mysqli->prepare("INSERT INTO
            pret VALUES(?, ?, ?, ?, ?);");
            $query->bind_param(
                "sidsi",
                $codePret,
                $num_compte,
                $montantPret,
                $datePret,
                $duree
            );
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php/  pret add pret :" . $e->getMessage();
        }
    }
    //UPDATE SOLDE 
    public function updatSolde($montantPret, $num_compte)
    {

        $montantPret = (float)$montantPret;
        $num_compte = (int)$num_compte;
        $response = "";
        //update solde
        try {
            $query = $this->mysqli->prepare("UPDATE client SET solde = (solde + ?) 
            WHERE numCompte =?;");
            $query->bind_param("di", $montantPret, $num_compte);
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php / pret update solde :" . $e->getMessage();
        }

        return $response;
    }

    //LIST PRET ALL
    public function listPretAll()
    {

        $list = [];

        try {
            $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
            P.num_compte, P.montantPret, P.datePret, P.duree, 
       COALESCE(
           (SELECT R.situation FROM rendu R 
            WHERE R.code_pret = P.codePret 
            ORDER BY R.dateRendu DESC 
            LIMIT 1), 'non remboursé'
       ) AS situation, 
       (P.montantPret * 0.10) AS benefice_banque
    FROM pret P 
    JOIN client C ON C.numCompte = P.num_compte
    JOIN banque B ON B.codeBanque = C.code_banque 
    WHERE B.codeBanque = ? ORDER BY datePret DESC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result  = $query->get_result();

            while ($row = $result->fetch_assoc()) {

                $list[] = $row;
            }
        } catch (Exception $e) {
            $list = "Erreur php / pret list pret all:" . $e->getMessage();
        }

        return $list;
    }

    //DELETE PRET
    public function deletePret($codePret)
    {
        $codePret = trim($codePret);
        $response = "success";

        try {
            $query = $this->mysqli->prepare("DELETE FROM pret WHERE codePret =?;");
            $query->bind_param("s", $codePret);
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php / pret delete pret:" . $e->getMessage();
        }

        return $response;
    }

    //SEARCH PRET
    public function searchPret(
        $codePret,
        $num_compte,
        $situation,
        $dateDu,
        $dateAu
    ) {

        $query = null;
        $list = [];
        $codePret = trim($codePret);
        $num_compte = trim($num_compte);
        $situation = trim($situation);
        $dateDu  = trim($dateDu);
        $dateAu = trim($dateAu);

        try {
            //codePret empty
            if ($codePret === "") {
                //num_compte empty
                if ($num_compte === "") {
                    //situation tout
                    if ($situation === "tout") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET ALL
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? ORDER BY datePret DESC;");
                                $query->bind_param("i", $this->code_banque);
                            }
                            //dateAu !empty
                            else {
                                //LIST PRET dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) <= ? ORDER BY datePret DESC;");
                                $query->bind_param("is", $this->code_banque, $dateAu);
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET dateDu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) >= ? ORDER BY datePret DESC;");
                                $query->bind_param("is", $this->code_banque, $dateDu);
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST PRET dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) BETWEEN ? AND ? ORDER BY datePret DESC;");
                                $query->bind_param("iss", $this->code_banque, $dateDu, $dateAu);
                            }
                        }
                    }
                    //situation !tout
                    else {

                        //situation non remboursé
                        if ($situation === 'non remboursé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET non remboursé 
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' ORDER BY datePret DESC;");
                                    $query->bind_param("i", $this->code_banque);
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé'  AND DATE(datePret) <= ? ORDER BY datePret DESC;");
                                    $query->bind_param("is", $this->code_banque, $dateAu);
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) >= ? ORDER BY datePret DESC;");
                                    $query->bind_param("is", $this->code_banque, $dateDu);
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) BETWEEN ? AND ? 
                                            ORDER BY datePret DESC;");
                                    $query->bind_param("iss", $this->code_banque, $dateDu, $dateAu);
                                }
                            }
                        }
                        //situation tout payé
                        elseif ($situation === 'tout payé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET tout payé
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' ORDER BY datePret DESC;");
                                    $query->bind_param("i", $this->code_banque);
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé'  AND DATE(datePret) <= ? ORDER BY datePret DESC;");
                                    $query->bind_param("is", $this->code_banque, $dateAu);
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) >= ? ORDER BY datePret DESC;");
                                    $query->bind_param("is", $this->code_banque, $dateDu);
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) BETWEEN ? AND ? 
                                            ORDER BY datePret DESC;");
                                    $query->bind_param("iss", $this->code_banque, $dateDu, $dateAu);
                                }
                            }
                        }
                        //situation payé une part
                        elseif ($situation === 'payé une part') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET payé une part
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' ORDER BY datePret DESC;");
                                    $query->bind_param("i", $this->code_banque);
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part'  AND DATE(datePret) <= ? ORDER BY datePret DESC;");
                                    $query->bind_param("is", $this->code_banque, $dateAu);
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) >= ? ORDER BY datePret DESC;");
                                    $query->bind_param("is", $this->code_banque, $dateDu);
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) BETWEEN ? AND ? 
                                            ORDER BY datePret DESC;");
                                    $query->bind_param("iss", $this->code_banque, $dateDu, $dateAu);
                                }
                            }
                        }
                    }
                }
                //num_compte !empty
                else {
                    $num_compte = "%" . $num_compte . "%";

                    //situation tout
                    if ($situation === "tout") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET num_compte
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND num_compte LIKE ?
                                    ORDER BY datePret DESC;");
                                $query->bind_param("is", $this->code_banque, $num_compte);
                            }
                            //dateAu !empty
                            else {
                                //LIST PRET dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) <= ? 
                                    AND num_compte LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param("iss", $this->code_banque, $dateAu, $num_compte);
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET dateDu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) >= ?
                                     AND num_compte LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compte
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST PRET dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) BETWEEN ? AND ?
                                     AND num_compte LIKE ?  ORDER BY datePret DESC;");
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
                    //situation !tout
                    else {

                        //situation non remboursé
                        if ($situation === 'non remboursé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET non remboursé 
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' 
                                            AND num_compte LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "is",
                                        $this->code_banque,
                                        $num_compte
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé'  AND DATE(datePret) <= ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
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
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) >= ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateDu,
                                        $num_compte
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) BETWEEN ? AND ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
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
                        //situation tout payé
                        elseif ($situation === 'tout payé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET tout payé
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "is",
                                        $this->code_banque,
                                        $num_compte
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé'  AND DATE(datePret) <= ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
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
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) >= ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateDu,
                                        $num_compte
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) BETWEEN ? AND ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
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
                        //situation payé une part
                        elseif ($situation === 'payé une part') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET payé une part
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part'  AND num_compte LIKE ? 
                                            ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "is",
                                        $this->code_banque,
                                        $num_compte
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part'  AND DATE(datePret) <= ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
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
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) >= ? 
                                             AND num_compte LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateDu,
                                        $num_compte
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) BETWEEN ? AND ? 
                                            AND num_compte LIKE ?  ORDER BY datePret DESC;");
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
                }
            }
            //codePret !empty
            else {
                $codePret = "%" . $codePret . "%";

                //num_compte empty
                if ($num_compte === "") {
                    //situation tout
                    if ($situation === "tout") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET codePret
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? 
                                    AND codePret LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "is",
                                    $this->code_banque,
                                    $codePret
                                );
                            }
                            //dateAu !empty
                            else {
                                //LIST PRET dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) <= ? 
                                    AND codePret LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateAu,
                                    $codePret
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET dateDu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) >= ? 
                                    AND codePret LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateDu,
                                    $codePret
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST PRET dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) BETWEEN ? AND ? 
                                    AND codePret LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $codePret
                                );
                            }
                        }
                    }
                    //situation !tout
                    else {

                        //situation non remboursé
                        if ($situation === 'non remboursé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET non remboursé 
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "is",
                                        $this->code_banque,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé'  AND DATE(datePret) <= ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateAu,
                                        $codePret
                                    );
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) >= ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateDu,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) BETWEEN ? AND ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateDu,
                                        $dateAu,
                                        $codePret
                                    );
                                }
                            }
                        }
                        //situation tout payé
                        elseif ($situation === 'tout payé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET tout payé
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "is",
                                        $this->code_banque,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé'  AND DATE(datePret) <= ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateAu,
                                        $codePret
                                    );
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) >= ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateDu,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) BETWEEN ? AND ? 
                                           AND codePret LIKE ?  ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateDu,
                                        $dateAu,
                                        $codePret
                                    );
                                }
                            }
                        }
                        //situation payé une part
                        elseif ($situation === 'payé une part') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET payé une part
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "is",
                                        $this->code_banque,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part'  AND DATE(datePret) <= ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateAu,
                                        $codePret
                                    );
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) >= ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $dateDu,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) BETWEEN ? AND ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateDu,
                                        $dateAu,
                                        $codepret
                                    );
                                }
                            }
                        }
                    }
                }
                //num_compte !empty
                else {
                    $num_compte = "%" . $num_compte . "%";

                    //situation tout
                    if ($situation === "tout") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET num_compte
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND num_compte LIKE ?
                                    AND codePret LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $num_compte,
                                    $codePret
                                );
                            }
                            //dateAu !empty
                            else {
                                //LIST PRET dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) <= ? 
                                    AND num_compte LIKE ?
                                    AND codePret LIKE ?  ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateAu,
                                    $num_compte,
                                    $codePret
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST PRET dateDu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation, 
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) >= ?
                                     AND num_compte LIKE ? 
                                     AND codePret LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compte,
                                    $codePret
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST PRET dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                P.num_compte, P.montantPret, P.datePret, P.duree, 
                                        COALESCE(
                                            (SELECT R.situation FROM rendu R 
                                                WHERE R.code_pret = P.codePret 
                                                ORDER BY R.dateRendu DESC 
                                                LIMIT 1), 'non remboursé'
                                        ) AS situation,
                                        (P.montantPret * 0.10) AS benefice_banque
                                    FROM pret P 
                                    JOIN client C ON C.numCompte = P.num_compte
                                    JOIN banque B ON B.codeBanque = C.code_banque 
                                    WHERE B.codeBanque = ? AND DATE(datePret) BETWEEN ? AND ?
                                     AND num_compte LIKE ?  
                                     AND codePret LIKE ? ORDER BY datePret DESC;");
                                $query->bind_param(
                                    "issss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $num_compte,
                                    $codePret
                                );
                            }
                        }
                    }
                    //situation !tout
                    else {

                        //situation non remboursé
                        if ($situation === 'non remboursé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET non remboursé 
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' 
                                            AND num_compte LIKE ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé'  AND DATE(datePret) <= ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateAu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) >= ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateDu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'non remboursé' AND DATE(datePret) BETWEEN ? AND ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "issss",
                                        $this->code_banque,
                                        $dateDu,
                                        $dateAu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                            }
                        }
                        //situation tout payé
                        elseif ($situation === 'tout payé') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET tout payé
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé'  AND DATE(datePret) <= ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateAu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) >= ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateDu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'tout payé' AND DATE(datePret) BETWEEN ? AND ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "issss",
                                        $this->code_banque,
                                        $dateDu,
                                        $dateAu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                            }
                        }
                        //situation payé une part
                        elseif ($situation === 'payé une part') {
                            //dateDu empty
                            if ($dateDu === "") {
                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET payé une part
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part'  AND num_compte LIKE ? 
                                            AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "iss",
                                        $this->code_banque,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    //LIST PRET dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part'  AND DATE(datePret) <= ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "isss",
                                        $this->code_banque,
                                        $dateAu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                            }
                            //dateDu !empty
                            else {
                                $dateDu = date("Y-m-d", strtotime($dateDu));

                                //dateAu empty 
                                if ($dateAu === "") {
                                    //LIST PRET dateDu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) >= ? 
                                             AND num_compte LIKE ? 
                                             AND codePret LIKE ? ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "issss",
                                        $this->code_banque,
                                        $dateDu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                                //dateAu !empty
                                else {
                                    $dateAu = date("Y-m-d", strtotime($dateAu));
                                    //LIST PRET dateDu / dateAu
                                    $query = $this->mysqli->prepare("SELECT DISTINCT P.codePret, 
                                                    P.num_compte, P.montantPret, P.datePret, P.duree, 
                                            COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            ) AS situation,
                                            (P.montantPret * 0.10) AS benefice_banque
                                        FROM pret P 
                                        JOIN client C ON C.numCompte = P.num_compte
                                        JOIN banque B ON B.codeBanque = C.code_banque 
                                        WHERE B.codeBanque = ?  AND (COALESCE(
                                                (SELECT R.situation FROM rendu R 
                                                    WHERE R.code_pret = P.codePret 
                                                    ORDER BY R.dateRendu DESC 
                                                    LIMIT 1), 'non remboursé'
                                            )) = 'payé une part' AND DATE(datePret) BETWEEN ? AND ? 
                                            AND num_compte LIKE ? 
                                            AND codePret LIKE ?  ORDER BY datePret DESC;");
                                    $query->bind_param(
                                        "issss",
                                        $this->code_banque,
                                        $dateDu,
                                        $dateAu,
                                        $num_compte,
                                        $codePret
                                    );
                                }
                            }
                        }
                    }
                }
            }

            //execute
            $query->execute();
            $result  = $query->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (Exception $e) {
            $list = "Erreur php / search virement : " . $e;
        }


        return $list;
    }

    public function totalBenefice()
    {
        $query = $this->mysqli->prepare("SELECT SUM(montantPret / 10) AS total FROM pret JOIN 
        client ON numCompte = num_compte JOIN banque ON codeBanque = code_banque WHERE codeBanque = ?; ");
        $query->bind_param("i", $this->code_banque);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        // $row["total"] = 10;
        return $row["total"];
    }
}
