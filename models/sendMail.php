<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../config/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../config/PHPMailer/src/Exception.php';
require __DIR__ . '/../config/PHPMailer/src/SMTP.php';
require_once __DIR__ . "/../config/database.php";

class sendMail extends db
{
    private $subject;
    private $body;
    private $from;
    private $phpmailer;
    private $numCompte;
    // private $emailClient;
    private $nomBank;
    public function __construct($numCompte)
    {
        parent::__construct();
        $this->numCompte = (int)$numCompte;

        $this->from = 'edouardorld@gmail.com';
        $this->phpmailer = new PHPMailer(true);
        $this->phpmailer->isSMTP();
        $this->phpmailer->Host = 'smtp.gmail.com';
        $this->phpmailer->SMTPAuth = true;
        $this->phpmailer->Username = $this->from;
        $this->phpmailer->Password = 'mphrpeykbaalraqb';
        $this->phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->phpmailer->Port = 587;
        $this->phpmailer->setFrom($this->from, $this->nomBank);
        $this->phpmailer->isHTML(true);
        $this->phpmailer->SMTPDebug = 0;
        $this->phpmailer->CharSet = 'UTF-8';
        $this->phpmailer->Encoding = 'base64';
    }

    //VERIFY INTERNET
    public function verifyInternet()
    {
        $fp = @fsockopen('www.google.com', 80, $errno, $errstr, 3);
        if (!@$fp) {
            return false;
        } else {
            fclose($fp);
            return true;
        }
    }

    //SENDMAIL FORGOT PASSWORD
    public function sendMailForgotPassword($emailBanque, $newPassword)
    {
        $this->phpmailer->addAddress($emailBanque, 'Admin');
        $this->phpmailer->setFrom($this->from, "BankWeb Project");
        $this->subject = "Rénitialisation du mot de passe";
        $this->body = "Votre nouveau mot de passe est : <b>" . $newPassword  . "</b>";
        $this->phpmailer->Subject = $this->subject;
        $this->phpmailer->Body = $this->body;
        $this->phpmailer->send();
    }

    //SENDMAIL PRET
    public function sendMailPret(
        $num_compte,
        $codePret,
        $montantPret,
        $datePret,
        $beneficeBanque,
        $duree
    ) {

        $response = "success";
        $num_compte = (int)$num_compte;
        $emailClient = "";
        $nomBanque = "";
        $datePret = DateTime::createFromFormat('d/m/Y H:i:s', $datePret);
        $dateP = $datePret->format('d/m/Y H:i:s');


        //recup email enad nomBank
        try {
            $query = $this->mysqli->prepare("SELECT emailClient, nomBanque
             FROM client JOIN banque ON codeBanque = code_banque WHERE numCompte =?;");
            $query->bind_param("i", $num_compte);
            $query->execute();
            $result = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $emailClient = $row['emailClient'];
                $nomBanque = $row['nomBanque'];
            }
        } catch (Exception $e) {
            $response = "Erreur php recup email, notify pret : " . $e->getMessage();
        }

        $dateLimitObj = clone $datePret;

        $dateLimitObj->add(new DateInterval('P' . (int)$duree . 'D'));
        $dateLimit = $dateLimitObj->format('d/m/Y');
        $nbDaysRest = 0;
        $dateNow = new DateTime();
        if ($dateNow < $dateLimitObj) {
            $inter = $dateNow->diff($dateLimitObj);
            $nbDaysRest = $inter->days;
        }
        $this->phpmailer->addAddress($emailClient, 'Client');
        $this->phpmailer->setFrom($this->from, $nomBanque);
        $this->subject = "PRET BANCAIRE";
        $this->body = "Vous avez fait un prêt de <b>"
            . $montantPret . " Ar</b> .<br>"
            . "Prêt N° : <b>" . $codePret . "</b><br>"
            . "Date de demande de prêt : <b>" . $dateP . " </b><br>"
            . "Bénéfice de la banque : <b>" . $beneficeBanque . " Ar</b><br>"
            . "Date d'écheance du prêt : <b>" . $dateLimit . " </b><br>"
            . "Nombre de jour restant : <b>" . $nbDaysRest . " jour(s)";
        $this->phpmailer->Subject = $this->subject;
        $this->phpmailer->Body = $this->body;
        $this->phpmailer->send();

        return $response;
    }

    public function sendMailRendu(
        $codeRendu,
        $montantRendu,
        $dateRendu,
        $restePaye,
        $situation
    ) {

        $response = "success";
        $emailClient = "";
        $nomBanque = "";
        $dateRendu = DateTime::createFromFormat('d/m/Y H:i:s', $dateRendu);
        $dateR = $dateRendu->format('d/m/Y H:i:s');


        //recup email enad nomBank
        try {
            $query = $this->mysqli->prepare("SELECT c.emailClient, b.nomBanque
             FROM client c JOIN banque b ON b.codeBanque = c.code_banque 
             JOIN pret p ON p.num_compte = c.numCompte JOIN rendu r 
             ON r.code_pret = p.codePret WHERE codeRendu = ?;");
            $query->bind_param(
                "s",
                $codeRendu
            );
            $query->execute();
            $result = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $emailClient = $row['emailClient'];
                $nomBanque = $row['nomBanque'];
            }
        } catch (Exception $e) {
            $response = "Erreur php recup email, notify pret : " . $e->getMessage();
        }

        $this->phpmailer->addAddress($emailClient, 'Client');
        $this->phpmailer->setFrom($this->from, $nomBanque);
        $this->subject = "PRET BANCAIRE";
        $this->body = "Vous avez fait un remboursement de <b>"
            . $montantRendu . " Ar</b> .<br>"
            . "Rendu N° : <b>" . $codeRendu . "</b><br>"
            . "Date Rendu : <b>" . $dateR . " </b><br>"
            . "Reste à rendre : <b>" . $restePaye . "( </b>" . $situation . ")<br>";
        $this->phpmailer->Subject = $this->subject;
        $this->phpmailer->Body = $this->body;
        $this->phpmailer->send();

        return $response;
    }
}
