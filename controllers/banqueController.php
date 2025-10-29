<?php
require_once __DIR__ . "/../models/banqueModel.php";
require_once __DIR__ . "/../models/sendMail.php";

class banqueController
{
    //properties
    private $banqueModel;
    private $codeBanque;
    private $sendMail;

    //construct
    public function __construct($codeBanque)
    {
        $this->codeBanque = (int) $codeBanque;
        $this->banqueModel = new banqueModel($codeBanque);
        $this->sendMail = new sendMail('');
    }

    //--------------------PAGE------------------------

    //PAGE SHOW CREATE AACCOUNT BANQUE
    public function showPageCreateAccount()
    {
        require_once __DIR__ . "/../views/addBankView.php";
    }

    //PAGE LOGIN
    public function showPageLogin()
    {
        require_once __DIR__ . "/../views/loginView.php";
    }

    //PAGE DASHBOARD
    public function showPageDashboard()
    {
        $page = "client";
        require_once __DIR__ . "/../views/dashboardView.php";
    }


    //--------------------CONTROLLER------------------

    //CONTROLLERS ADD BANK
    public function addBankController()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nomBanque = trim($_POST['nom-banque']);
            $emailBanque = trim($_POST['email-banque']);
            $password = $_POST['password'];
            $repeatPassword = $_POST['repeat-password'];
            //session repeat value form
            $_SESSION['value-add-bank-nom-banque'] = $nomBanque;
            $_SESSION['value-add-bank-email-banque'] = $emailBanque;

            //input empty
            if ($nomBanque === "" || $emailBanque === "" || $password === "") {
                $_SESSION['message-add-bank'] = "Veuiller compléter tout les champs";
                header("Location: ../public/index.php?route=create_account");
            }
            //input !empty
            else {
                //password != repeat password
                if ($password !== $repeatPassword) {
                    $_SESSION['message-add-bank'] = "Deuxième mot de passe ne correspond pas !";
                    header("Location: ../public/index.php?route=create_account");
                }
                // password = repeat password
                else {
                    //emailBanque exist
                    if ($this->banqueModel->isEmailBankExist($emailBanque)) {
                        $_SESSION['message-add-bank'] = "L'adresse email <b>"
                            . $emailBanque . "</b> existe déjà";
                        header("Location: ../public/index.php?route=create_account");
                    }
                    //emailBanque !exist
                    else {
                        //addBank 
                        $this->banqueModel->addBank(
                            $nomBanque,
                            $password,
                            $emailBanque
                        );
                        $_SESSION['message-add-bank-success'] = "Compte crée avec succès .";
                        header("Location: ../public/index.php?route=create_account");
                    }
                }
            }
        }
    }

    //CONTROLLER LOGIN
    public function isLoginCorrectController()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailBanque = $_POST['email-banque'];
            $passsword = $_POST['password'];

            //session for restore  value
            $_SESSION['email-banque-login'] = $emailBanque;

            //input empty
            if ($emailBanque === "" || $passsword === "") {
                $_SESSION['message-login'] = "Les deux champs sont obligatoires !";
                header("Location: ../public/index.php?route=login");
            }
            //input !empty
            else {
                //email exist
                if ($this->banqueModel->isEmailBankExist($emailBanque)) {
                    //login correct
                    if ($this->banqueModel->isLoginCorrect($emailBanque, $passsword)) {
                        $_SESSION['code-banque'] = $this->banqueModel->recupCodeBank($emailBanque);
                        header("Location: ../public/index.php?route=dashboard");
                    }
                    //login !correct
                    else {
                        $_SESSION['message-login'] = "Email ou mot de passe incorrect !";
                        header("Location: ../public/index.php?route=login");
                    }
                }
                //email !exist
                else {
                    $_SESSION['message-login'] = "Ce compte n'existe pas !";
                    header("Location: ../public/index.php?route=login");
                }
            }
        }
    }

    //CONTROLLER FORGOT PASSWORD
    public function forgotPasswordController()
    {
        $emailBanque = trim($_GET['email']);

        //email exist
        if ($this->banqueModel->isEmailBankExist($emailBanque)) {
            // internet ok
            if ($this->sendMail->verifyInternet()) {

                //update random password
                $newPassword = $this->banqueModel->forgotPassword($emailBanque);
                //send password email
                $this->sendMail->sendMailForgotPassword($emailBanque, $newPassword);
                $_SESSION['message-login-forgot-password-success'] = "Un nouveau mot de passe est 
                envoyé à l'adresse email <b>" . $emailBanque . "</b>";
                header("Location: ../public/index.php?route=login");
            }
            //internet !ok
            else {
                header("Location: ../public/index.php?route=internet_error");
            }
        }
        //email !exist
        else {
            $_SESSION['message-login'] = "Ce compte n'existe pas !";
            header("Location: ../public/index.php?route=login");
        }
    }

    //CONTROLLER BANK INFO
    public function bankInfoController()
    {
        header("Content-Type: application/json");
        echo json_encode($this->banqueModel->recupBankInfo());
    }

    //CONTROLLER LOGOUT
    public function logoutController()
    {
        session_destroy();
        header("Location: ../public/index.php?route=login");
    }

    //CONTROLLER UPDATE BANK
    public function updateBankController()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $json = json_decode(file_get_contents("php://input"), true);

            header("Content-Type: application/json");

            //nomBanque and emailBanque empty
            if (trim($json['nomBanque']) === "" || trim($json['emailBanque']) === "") {
                echo json_encode("input empty");
            }
            //nomBanque and emailBanque !empty
            else {
                //email exist
                if ($this->banqueModel->isEmailAlreadyExist($json['emailBanque'])) {
                    echo json_encode("email exist");
                }
                //email !exist
                else {
                    //update bank
                    echo json_encode($this->banqueModel->updateBank(
                        $json['nomBanque'],
                        $json['emailBanque'],
                        $json['password']
                    ));
                }
            }
        }
    }
}
