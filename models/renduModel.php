<?php
require_once __DIR__ . "/../config/database.php";

class renduModel extends db
{
    //properties
    private $code_banque;

    //construct 
    public function __construct($code_banque)
    {
        parent::__construct();
        $this->code_banque = (int)$code_banque;
    }


    //LIST PRET ALL

    public function listPretAll()
    {
        $list = [];

        try {

            $query = $this->mysqli->prepare("SELECT DISTINCT
             p.codePret, p.num_compte, p.montantPret, 
             COALESCE((SELECT RR.restePaye FROM rendu RR  WHERE RR.code_pret = P.codePret ORDER
              BY dateRendu DESC LIMIT 1 ), p.montantPret) AS 
              restePaye FROM pret p JOIN client c ON c.numCompte = p.num_compte
               JOIN banque b ON b.codeBanque = c.code_banque WHERE 
               b.codeBanque = ? AND (COALESCE( (SELECT R.situation FROM
                rendu R WHERE R.code_pret = P.codePret ORDER BY R.dateRendu
                 DESC LIMIT 1), 'non remboursé' )) != 'tout payé' ORDER BY 
                 datePret DESC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result = $query->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (Exception $e) {
            $list = "Erreur php rendu / list pret all : " . $e->getMessage();
        }

        return $list;
    }

    //ADD RENDU
    //is solde sufficient?
    public function isSoldeSufficient($code_pret, $montantRendu)
    {
        $response = true;

        try {
            $query = $this->mysqli->prepare("SELECT solde FROM
            client JOIN pret ON num_compte = numCompte 
            WHERE codePret = ?;");
            $query->bind_param("s", $code_pret);
            $query->execute();
            $result = $query->get_result();

            if ((float)$result->fetch_assoc()['solde'] < $montantRendu) {
                $response = false;
            }
        } catch (Exception $e) {
            $response = "Erreur php rendu / add rendu is solde sufficient : " . $e->getMessage();
        }

        return $response;
    }
    //add rendu
    public function addRendu($code_pret, $montantRendu)
    {
        $response = "success";

        //codeRendu first
        $codeRendu = "RD-" . date("YmdHi") . "-" . substr(
            str_shuffle("ABCDEFGHIJKLMNSOPQRSTUVWXYZ"),
            0,
            3
        ) . strval(rand(000, 999));
        //codeRendu second
        try {
            $query = $this->mysqli->prepare("SELECT codeRendu FROM rendu
             WHERE codeRendu =?;");
            $query->bind_param("s", $codeRendu);
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
            $response = "Erreur php rendu / add rendu second codeRendu :" . $e->getMessage();
        }

        //restePaye
        // situation
        $situation = "";
        try {
            $query = $this->mysqli->prepare("SELECT DISTINCT
             (COALESCE( (SELECT R.situation FROM rendu R WHERE 
             R.code_pret = P.codePret ORDER BY R.dateRendu DESC LIMIT 1), 
             'non remboursé' )) AS situation FROM pret P
              WHERE codePret = ?;");
            $query->bind_param("s", $code_pret);
            $query->execute();
            $result = $query->get_result();

            $situation = $result->fetch_assoc()['situation'];
        } catch (Exception $e) {
            $response = "Erreur php rendu / add rendu situation : " . $e->getMessage();
        }
        // montantPret
        $montantPret = 0;
        try {
            $query = $this->mysqli->prepare("SELECT montantPret FROM
            pret WHERE codePret = ?;");
            $query->bind_param("s", $code_pret);
            $query->execute();
            $result = $query->get_result();

            $montantPret = (float)$result->fetch_assoc()['montantPret'];
        } catch (Exception $e) {
            $response = "Erreur php rendu /  add rendu montantPret :" . $e->getMessage();
        }
        // restePaye
        $restePaye = 0;
        if ($situation === "non remboursé") {
            $restePaye = $montantPret - $montantRendu;
        } else {
            // restePaye last
            try {
                $query = $this->mysqli->prepare("SELECT restePaye FROM 
                rendu JOIN pret ON codePret = code_pret 
                WHERE code_pret = ? ORDER BY dateRendu DESC LIMIT 1;");
                $query->bind_param("s", $code_pret);
                $query->execute();
                $result = $query->get_result();

                $restePaye = (float)$result->fetch_assoc()['restePaye'] - $montantRendu;
            } catch (Exception $e) {
                $response = "Erreur php rendu / add rendu restePaye -= :" . $e->getMessage();
            }
        }

        //situationR
        $situationR = "";
        if ($restePaye <= 0) {
            $situationR = "tout payé";
        } else {
            $situationR = "payé une part";
        }

        //add rendu
        try {
            $query = $this->mysqli->prepare("INSERT INTO rendu 
            VALUES(?, ?, ?, ?, ?, NOW());");
            $query->bind_param(
                "ssdsd",
                $codeRendu,
                $code_pret,
                $montantRendu,
                $situationR,
                $restePaye
            );
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php rendu / add rendu :" . $e->getMessage();
        }

        //update solde client
        try {
            $query = $this->mysqli->prepare("UPDATE client SET 
            solde = solde - ? WHERE numCompte = (SELECT DISTINCT numCompte FROM 
            client JOIN pret ON num_compte = numCompte
             JOIN rendu ON code_pret = codePret 
             WHERE code_pret = ?);");
            $query->bind_param(
                "ds",
                $montantRendu,
                $code_pret,
            );
            $query->execute();
        } catch (Exception $e) {
            $response = "Erreur php rendu / add rendu :" . $e->getMessage();
        }

        return $response;
    }

    //LIST RENDU
    public function listRenduAll()
    {

        $list = [];

        try {
            $query = $this->mysqli->prepare("SELECT r.*, 
            COALESCE( ( SELECT DISTINCT rr.codeRendu FROM rendu rr 
            WHERE rr.code_pret = codePret AND r.codeRendu = rr.codeRendu
             AND dateRendu = ( SELECT dateRendu FROM rendu WHERE code_pret = codePret 
             ORDER BY dateRendu DESC LIMIT 1 ) ), \"not max\" ) AS lastRendu FROM 
             rendu r JOIN pret ON codePret = code_pret JOIN CLIENT ON numCompte = 
             num_compte JOIN banque ON codeBanque = code_banque WHERE code_banque = ? 
             ORDER BY dateRendu DESC;");
            $query->bind_param("i", $this->code_banque);
            $query->execute();
            $result = $query->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (Exception $e) {
            $list = "Erreur php rendu / list rendu all :" . $e->getMessage();
        }

        return $list;
    }

    //DELETE RENDU
    public function deleteRendu($codeRendu){
        $response = "success";
        $codeRendu = trim($codeRendu);

        try{
            $query = $this->mysqli->prepare("DELETE FROM rendu WHERE codeRendu = ?;");
            $query->bind_param("s", $codeRendu);
            $query->execute();
        }
        catch (Exception $e) {
            $response = "Erreur php rendu / delete rendu :" . $e->getMessage();
        }

        return $response;
    }


}