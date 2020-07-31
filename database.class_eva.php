<?php
class Database extends PDO
{
    //nombre base de datos
    private $dbname = "ClintosEva";
    //nombre servidor
    private $host = "10.10.10.20";
    //nombre usuarios base de datos
    private $user = "clintos";
    //password usuario
    private $pass = "Cl1nt0s2015";

    //creamos la conexión a la base de datos prueba
    public function __construct()
    {
        try {
            $this->dbh = parent::__construct("sqlsrv:Server=$this->host;Database=$this->dbname","$this->user","$this->pass");
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch(PDOException $e) {
            echo  $e->getMessage();
        }
    }

    //función para cerrar una conexión pdo
    public function close_con()
    {
        $this->dbh = null;
    }
}