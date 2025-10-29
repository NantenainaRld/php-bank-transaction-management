<?php
class db
{
    private $host;
    private $user;
    private $password;
    private $dbName;
    protected $mysqli;

    public function __construct()
    {
        $this->host = "localhost";
        $this->user = "root";
        $this->password = "";
        $this->dbName = "transaction_bancaire";
        try {
            $this->mysqli = new Mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->dbName,
            );
        } catch (Exception $e) {
            die("Erreur de connection à la base de données :" . $e->getMessage());
        }
    }


    //close database connection
    public function dbClose()
    {
        $this->mysqli->close();
    }
}
