<?php
require_once __DIR__ . "/../config/database.php";

class banqueModel extends db
{
    //properties
    private $codeBanque;

    //construc
    public function __construct($codeBanque)
    {
        parent::__construct();
        $this->codeBanque = (int)$codeBanque;
    }

    //create bank
    //is emailBank exisyt?
    public function isEmailBankExist($emailBanque)
    {
        $exist = false;
        try {
            $query = $this->mysqli->prepare("SELECT emailBanque FROM 
            banque WHERE emailBanque =?;");
            $query->bind_param("s", $emailBanque);
            $query->execute();
            $result = $query->get_result();
            //email found
            if ($result->fetch_assoc()) {
                $exist = true;
            }
        } catch (Exception $e) {
            die("Erreur de requête find emailBanque : " . $e->getMessage());
        }

        return $exist;
    }
    //create bank
    public function addBank($nomBanque, $password, $emailBanque)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $query = $this->mysqli->prepare("INSERT INTO banque (nomBanque, 
            emailBanque, password) VALUES(?, ?, ?);");
            $query->bind_param("sss", $nomBanque, $emailBanque, $password);
            $query->execute();
        } catch (Exception $e) {
            die("Erreur de requête add bank : " . $e->getMessage());
        }
    }

    //LOGIN CORRECT?
    public function isLoginCorrect($emailBanque, $password)
    {
        $correct = false;

        try {
            $query = $this->mysqli->prepare("SELECT password
            FROM banque WHERE emailBanque = ?;");
            $query->bind_param("s", $emailBanque);
            $query->execute();
            $result = $query->get_result();
            $passwordQuery = $result->fetch_assoc()['password'];

            //login correct
            if (password_verify($password, $passwordQuery)) {
                $correct = true;
            }
        } catch (Exception $e) {
            die("Erreur de requête add bank : " . $e->getMessage());
        }

        return $correct;
    }

    //FORGOT PASSWORD
    public function forgotPassword($emailBanque)
    {
        $random = "0123456789abcdefghijklmnopqrstuvwxygABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $newPassword = substr(str_shuffle($random), 0, 6);
        //update password

        try {
            $query = $this->mysqli->prepare("UPDATE banque SET password =? 
            WHERE emailBanque =?;");
            $passHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $query->bind_param(
                "ss",
                $passHash,
                $emailBanque
            );
            $query->execute();
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }

        return $newPassword;
    }

    //RECUP CODE BANQUE
    public function recupCodeBank($emailBanque)
    {
        $codeBanque = null;

        try {
            $query = $this->mysqli->prepare("SELECT codeBanque FROM banque WHERE emailBanque =?;");
            $query->bind_param("s", $emailBanque);
            $query->execute();
            $result = $query->get_result();

            $codeBanque = $result->fetch_assoc()['codeBanque'];
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }

        return $codeBanque;
    }

    //RECUP BANK INFO
    public function recupBankInfo()
    {
        $bankInfo = [];

        try {
            $query = $this->mysqli->prepare("SELECT nomBanque, emailBanque 
            FROM banque WHERE codeBanque=?;");
            $query->bind_param("i", $this->codeBanque);
            $query->execute();
            $result = $query->get_result();

            $bankInfo = $result->fetch_assoc();
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }

        return $bankInfo;
    }

    //UPDATE BANK
    //email already exist?
    public function isEmailAlreadyExist($emailBanque)
    {
        $exist = false;

        try {
            $query = $this->mysqli->prepare("SELECT emailBanque 
            FROM banque WHERE codeBanque !=? AND emailBanque =?;");
            $query->bind_param(
                "is",
                $this->codeBanque,
                $emailBanque
            );
            $query->execute();
            $result = $query->get_result();

            //email exist
            if ($result->fetch_assoc()) {
                $exist = true;
            }
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }

        return $exist;
    }
    //UPDATE BANK
    public function updateBank(
        $nomBanque,
        $emailBanque,
        $password
    ) {
        $response = "success";

        try {
            $query = null;

            //update without password
            if ($password === "") {
                $query = $this->mysqli->prepare("UPDATE banque SET nomBanque =?
                , emailBanque =? WHERE codeBanque = ?;");
                $query->bind_param(
                    "ssi",
                    $nomBanque,
                    $emailBanque,
                    $this->codeBanque
                );
            }
            //update with password;
            else {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $query = $this->mysqli->prepare("UPDATE banque SET nomBanque =?
                , emailBanque =?, password =? WHERE codeBanque = ?;");
                $query->bind_param(
                    "sssi",
                    $nomBanque,
                    $emailBanque,
                    $password,
                    $this->codeBanque
                );
            }

            $query->execute();
        } catch (Exception $e) {
            $response =  $e->getMessage();
        }

        return $response;
    }
}
