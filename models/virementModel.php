<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/tfpdf/tfpdf.php";

class virementModel extends db
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
        $response = null;
        $list = [];

        try {
            $query = $this->mysqli->prepare("SELECT numCompte, Nom, Prenoms, solde
            FROM client WHERE code_banque = ? ORDER BY Nom ASC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result = $query->get_result();

            //list empty
            if ($result->num_rows <= 0) {
                $response = ["message" => "list empty"];
            }
            //list not empty
            else {
                while ($row  = $result->fetch_assoc()) {
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
                "error_message" => "Erreur php virement / list client all : " . $e->getMessage()
            ];
        }

        return $response;
    }

    //RECIPIENT NAME
    public function recipientInfo($num_compteE, $num_compteB)
    {

        $response = null;
        $num_compteE = (int)$num_compteE;
        $num_compteB = (int)$num_compteB;

        try {
            $query = $this->mysqli->prepare("SELECT Nom, Prenoms
             FROM client WHERE numCompte = ? AND numCompte != ?;");
            $query->bind_param(
                "ii",
                $num_compteB,
                $num_compteE
            );
            $query->execute();
            $result = $query->get_result();

            //found
            if ($row = $result->fetch_assoc()) {
                $response = [
                    "message" => "found",
                    "nomPrenoms" => $row["Nom"] . " " . $row["Prenoms"]
                ];
            }
            //not found
            else {
                $response = [
                    "message" => "not found"
                ];
            }
        } catch (Exception $e) {
            $response = [
                "message" => "error",
                "error_message" => "Erreur php virement / recipient info : " . $e->getMessage()
            ];
        };

        return $response;
    }

    //ADD VIREMENT 
    //--is recipient exist?
    public function isRecipientExist($num_compteE, $num_compteB)
    {

        $found = false;
        $num_compteE = (int)$num_compteE;
        $num_compteB = (int)$num_compteB;

        try {
            $query = $this->mysqli->prepare("SELECT numCompte FROM 
            client WHERE numCompte = ? AND numCompte != ?");
            $query->bind_param(
                "ii",
                $num_compteB,
                $num_compteE
            );
            $query->execute();
            $result = $query->get_result();

            //exist
            if ($result->num_rows > 0) {
                $found = true;
            }
        } catch (Exception $e) {
            $found = "Erreur php virement / add virement / recipient exist : " . $e->getMessage();
        }

        return $found;
    }
    //--is solde sufficient?
    public function isSoldeSufficient($num_compteE, $montantVirement)
    {

        $sufficient = false;
        $num_compteE = (int)$num_compteE;
        $montantVirement = (float)$montantVirement;
        $solde = null;

        try {
            $query = $this->mysqli->prepare("SELECT solde FROM client WHERE numCompte = ?;");
            $query->bind_param("i", $num_compteE);
            $query->execute();
            $result  = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $solde = (float)$row['solde'];
            }

            if ($solde >= $montantVirement) {
                $sufficient = true;
            }
        } catch (Exception $e) {
            $sufficient = "Erreur php virement / add virement / soldeE !sufficient : " . $e->getMessage();
        }

        return $sufficient;
    }
    //--add virement
    public function addVirement($num_compteE, $num_compteB, $montantVirement)
    {
        $response = "success";
        $num_compteE = (int)$num_compteE;
        $num_compteB = (int)$num_compteB;
        $montantVirement = (float)$montantVirement;
        $dateVirement = date("Y-m-d H:i:s", time());

        //first code virement
        $codeVirement = "VR-" . date("YmdHi") . "-" . substr(
            str_shuffle("ABCDEFGHIJKLMNSOPQRSTUVWXYZ"),
            0,
            3
        ) . strval(rand(000, 999));
        //is codeVirement exist?
        try {
            $query = $this->mysqli->prepare("SELECT codeVirement FROM virement
             WHERE codeVirement =?;");
            $query->bind_param("s", $codeVirement);
            $query->execute();
            $result = $query->get_result();

            //codeVirement exist then reshuffle
            if ($result->num_rows > 0) {
                $codeVirement = "VR-" . date("YmdHi") . "-" . substr(
                    str_shuffle("ABCDEFGHIJKLMNSOPQRSTUVWXYZ"),
                    0,
                    3
                ) . strval(rand(000, 999));
            }
        } catch (Exception $e) {
            $response = "Erreur php virement / add virement / codeVirement exist :" . $e->getMessage();
        }

        //add virement
        try {
            $query = $this->mysqli->prepare("INSERT INTO virement VALUES(?, ?, ?, ?, ?);");
            $query->bind_param(
                "siids",
                $codeVirement,
                $num_compteE,
                $num_compteB,
                $montantVirement,
                $dateVirement
            );
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php virement / add virement :" . $e->getMessage();
        }

        return $response;
    }
    //UPDATE SOLDE
    public function updateSolde($num_compteE, $num_compteB, $montantVirement)
    {
        $response = "success";
        $num_compteE = (int)$num_compteE;
        $num_compteB = (int)$num_compteB;
        $montantVirement  = (float)$montantVirement;

        //update solde
        try {
            //update solde E
            $query = $this->mysqli->prepare("UPDATE client SET solde = (solde - ?)
             WHERE numCompte = ?
             AND code_banque =?;");
            $query->bind_param(
                "dii",
                $montantVirement,
                $num_compteE,
                $this->code_banque
            );
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php virement / add virement / update soldeE :" . $e->getMessage();
        }
        try {
            //update solde B
            $query = $this->mysqli->prepare("UPDATE client SET solde = (solde + ?)
             WHERE numCompte = ?
             AND code_banque =?;");
            $query->bind_param(
                "dii",
                $montantVirement,
                $num_compteB,
                $this->code_banque
            );
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php virement / add virement / update soldeB :" . $e->getMessage();
        }

        return $response;
    }

    //LIST VIREMENT ALL
    public function listVirementAll()
    {

        $list = [];

        try {
            $query = $this->mysqli->prepare("SELECT codeVirement,
             num_compteE, num_compteB, montantVirement, dateVirement FROM virement
            JOIN client ON num_compteE = numCompte 
             JOIN banque ON codeBanque =code_banque WHERE codeBanque =? ORDER BY dateVirement DESC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result  = $query->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (Exception $e) {
            $list = "Erreur php list virement all " . $e->getMessage();
        }

        return $list;
    }

    //PRINT VIREMENT;
    public function printVirement($codeVirement)
    {
        $virementInfo = [];
        //query virementInfo
        try {
            $query = $this->mysqli->prepare("SELECT nomBanque, num_compteE,
             e.Nom AS NomE, e.Prenoms AS PrenomsE, e.solde, montantVirement,dateVirement, num_compteB, 
             b.Nom AS NomB,b.Prenoms AS PrenomsB FROM client e JOIN virement v
              ON v.num_compteE = e.numCompte JOIN client b ON v.num_compteB = b.numCompte
              JOIN banque ON e.code_banque = codeBanque WHERE codeVirement =?;");
            $query->bind_param("s", $codeVirement);
            $query->execute();
            $result = $query->get_result();
            $virementInfo = $result->fetch_assoc();
        } catch (Exception $e) {
            die("Erreur de requête : " . $e->getMessage());
        }

        //tpdf instance / font / pdf size
        $tFPDF = new tFPDF();
        $tFPDF->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);

        //add page
        $tFPDF->AddPage("P", array(210, 240));

        //pdf content

        //nomBanque
        $tFPDF->SetFont('DejaVu', '', 14);
        $tFPDF->Cell(0, 10, $virementInfo['nomBanque'], 0, 1, 'C');

        //date now
        $tFPDF->SetFont('DejaVu', '', 12);
        $tFPDF->Cell(0, 10, date("d/m/Y H:i:s", time()), 0, 1, 'C');

        //codeVirement
        $tFPDF->Cell(0, 10, "AVIS DE VIREMENT N° " . $codeVirement, 0, 1, 'C');

        $tFPDF->Ln(10);

        //compteE
        $tFPDF->Cell(0, 10, "N° du compte : " . $virementInfo['num_compteE'], 0, 1, 'L');
        $tFPDF->Cell(0, 10,  $virementInfo['NomE'] . " "  . $virementInfo['PrenomsE'], 0, 1, 'L');

        $tFPDF->Ln(5);
        $tFPDF->Cell(0, 10, "A", 0, 1, 'C');
        $tFPDF->Ln(5);

        //compteE
        $tFPDF->Cell(0, 10, "N° du compte : " . $virementInfo['num_compteB'], 0, 1, 'R');
        $tFPDF->Cell(0, 10,  $virementInfo['NomB'] . " "  . $virementInfo['PrenomsB'], 0, 1, 'R');

        $tFPDF->Ln(20);


        //montantVirement
        $tFPDF->Cell(0, 10, "Montant viré : " . $virementInfo['montantVirement'] . " Ar", 0, 1, 'L');
        //date virement
        $tFPDF->Cell(
            0,
            10,
            "Date de virement : "
                . date("d/m/Y H:i:s", strtotime($virementInfo['dateVirement'])),
            0,
            1,
            'L'
        );
        //solde actuel
        $tFPDF->Cell(0, 10, "Reste du solde actuel : " . $virementInfo['solde'] . " Ar", 0, 1, 'L');

        //pdf output
        $tFPDF->Output($codeVirement . ".pdf", "D");
    }

    //DELETE VIREMENT
    public function deleteVirement($codeVirement)
    {

        $response = "success";

        try {
            $query = $this->mysqli->prepare("DELETE FROM virement WHERE codeVirement = ?;");
            $query->bind_param("s", $codeVirement);
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php delete virement" . $e->getMessage();
        }

        return $response;
    }

    //SEARCH VIREMENT
    public function searchVirement(
        $codeVirement,
        $num_compteE,
        $num_compteB,
        $dateDu,
        $dateAu
    ) {

        $query = null;
        $list = [];
        $codeVirement = trim($codeVirement);
        $num_compteE = trim($num_compteE);
        $num_compteB = trim($num_compteB);
        $dateDu  = trim($dateDu);
        $dateAu = trim($dateAu);

        try {
            //codeVirement empty
            if ($codeVirement === "") {
                //num_compteE empty
                if ($num_compteE === "") {
                    //num_compteB empty
                    if ($num_compteB === "") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT ALL
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? ORDER BY dateVirement  DESC;");
                                $query->bind_param("i", $this->code_banque);
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? ORDER BY dateVirement DESC;");
                                $query->bind_param("is", $this->code_banque, $dateAu);
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "is",
                                    $this->code_banque,
                                    $dateDu
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu
                                );
                            }
                        }
                    }
                    //num_compteB !empty
                    else {
                        $num_compteB = "%" . $num_compteB . "%";

                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT num_compteB
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? 
                                 AND num_compteB LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param("is", $this->code_banque, $num_compteB);
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT num_compteB / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? 
                                  AND num_compteB LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateAu,
                                    $num_compteB
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT num_compteB / dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ?
                                 AND num_compteB LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compteB
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT num_compte / dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? 
                                  AND num_compteB LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $num_compteB
                                );
                            }
                        }
                    }
                }
                //num_compteE !empty
                else {
                    $num_compteE = "%" . $num_compteE . "%";

                    //num_compteB empty
                    if ($num_compteB === "") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT num_compteE
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? 
                                 AND num_compteE LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "is",
                                    $this->code_banque,
                                    $num_compteE
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT num_compteE / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? 
                                  AND num_compteE LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateAu,
                                    $num_compteE
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT num_compteE  / dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ?
                                 AND num_compteE LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compteE
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT num_compteE / dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? 
                                  AND num_compteE LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $num_compteE
                                );
                            }
                        }
                    }
                    //num_compteB !empty
                    else {
                        $num_compteB = "%" . $num_compteB . "%";

                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT num_compteE /  num_compteB
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? 
                                 AND num_compteB LIKE ? 
                                 AND num_compteE LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $num_compteB,
                                    $num_compteE
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT num_compteE / num_compteB / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? 
                                  AND num_compteB LIKE ? 
                                  AND num_compteE LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateAu,
                                    $num_compteB,
                                    $num_compteE
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT num_compteE / num_compteB / dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ?
                                 AND num_compteB LIKE ? 
                                 AND num_compteE LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compteB,
                                    $num_compteE
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT num_compteE / num_compteB / dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? 
                                  AND num_compteB LIKE ? 
                                  AND num_compteE LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "issss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $num_compteB,
                                    $num_compteE
                                );
                            }
                        }
                    }
                }
            }
            //codeVirement !empty
            else {
                $codeVirement = "%" . $codeVirement . "%";

                //num_compteE empty
                if ($num_compteE === "") {
                    //num_compteB empty
                    if ($num_compteB === "") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? 
                                 AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "is",
                                    $this->code_banque,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? 
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateAu,
                                    $codeVirement
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement / dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ?
                                  AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $dateDu,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? 
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $codeVirement
                                );
                            }
                        }
                    }
                    //num_compteB !empty
                    else {
                        $num_compteB = "%" . $num_compteB . "%";

                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement / num_compteB
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? 
                                 AND num_compteB LIKE ? 
                                 AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $num_compteB,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / num_compteB / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? 
                                  AND num_compteB LIKE ? 
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateAu,
                                    $num_compteB,
                                    $codeVirement
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement / num_compteB / dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ?
                                 AND num_compteB LIKE ? 
                                 AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compteB,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / num_compteB / dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? 
                                  AND num_compteB LIKE ? 
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "issss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $num_compteB,
                                    $codeVirement
                                );
                            }
                        }
                    }
                }
                //num_compteE !empty
                else {
                    $num_compteE = "%" . $num_compteE . "%";

                    //num_compteB empty
                    if ($num_compteB === "") {
                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement / num_compteE
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? 
                                 AND num_compteE LIKE ? 
                                 AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "iss",
                                    $this->code_banque,
                                    $num_compteE,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / num_compteE / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? 
                                  AND num_compteE LIKE ?
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateAu,
                                    $num_compteE,
                                    $codeVirement
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement / num_compteE  / dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ?
                                 AND num_compteE LIKE ? 
                                 AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compteE,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / num_compteE / dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? 
                                  AND num_compteE LIKE ? 
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "issss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $num_compteE,
                                    $codeVirement
                                );
                            }
                        }
                    }
                    //num_compteB !empty
                    else {
                        $num_compteB = "%" . $num_compteB . "%";

                        //dateDu empty
                        if ($dateDu === "") {
                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement / num_compteE /  num_compteB
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ? 
                                 AND num_compteB LIKE ? 
                                 AND num_compteE LIKE ? 
                                 AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "isss",
                                    $this->code_banque,
                                    $num_compteB,
                                    $num_compteE,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / num_compteE / num_compteB / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) <= ? 
                                  AND num_compteB LIKE ? 
                                  AND num_compteE LIKE ? 
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "issss",
                                    $this->code_banque,
                                    $dateAu,
                                    $num_compteB,
                                    $num_compteE,
                                    $codeVirement
                                );
                            }
                        }
                        //dateDu !empty
                        else {
                            $dateDu = date("Y-m-d", strtotime($dateDu));

                            //dateAu empty 
                            if ($dateAu === "") {
                                //LIST VIREMENT codeVirement / num_compteE / num_compteB / dateDu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = 
                                 num_compteE WHERE code_banque = ?
                                 AND DATE(dateVirement)  >= ?
                                 AND num_compteB LIKE ? 
                                 AND num_compteE LIKE ? 
                                 AND codeVirement LIKE ? ORDER BY dateVirement  DESC;");
                                $query->bind_param(
                                    "issss",
                                    $this->code_banque,
                                    $dateDu,
                                    $num_compteB,
                                    $num_compteE,
                                    $codeVirement
                                );
                            }
                            //dateAu !empty
                            else {
                                $dateAu = date("Y-m-d", strtotime($dateAu));
                                //LIST VIREMENT codeVirement / num_compteE / num_compteB / dateDu / dateAu
                                $query = $this->mysqli->prepare("SELECT * FROM virement
                                 JOIN client ON numCompte = num_compteE WHERE code_banque = ?
                                  AND DATE(dateVirement) BETWEEN ? AND ? 
                                  AND num_compteB LIKE ? 
                                  AND num_compteE LIKE ? 
                                  AND codeVirement LIKE ? ORDER BY dateVirement DESC;");
                                $query->bind_param(
                                    "isssss",
                                    $this->code_banque,
                                    $dateDu,
                                    $dateAu,
                                    $num_compteB,
                                    $num_compteE,
                                    $codeVirement
                                );
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
}