<?php
namespace Fi\OracleBundle\DependencyInjection;

class fiOracle {

    private $OracledbName;
    private $Oracleusername;
    private $Oraclepassword;
    private $dbOracle;

//  public function __construct()
    protected function connect($connessione,$utente,$password) {
        
        //$dbName,$username,$password
        $this->OracledbName = $connessione;
        $this->Oracleusername = $utente;
        $this->Oraclepassword = $password;
        //PutEnv("NLS_LANG=ITALIAN_ITALY.WE8ISO8859P1");
	//PutEnv("NLS_LANG=AMERICAN_AMERICA.WE8MSWIN1252");
        /* Connessione oracle */
        $this->dbOracle = oci_connect($this->Oracleusername, $this->Oraclepassword, $this->OracledbName, 'AL32UTF8');
        // test connection
        if (!$this->dbOracle) {
            $err_description = oci_error();
            echo "Impossibile stabilire una connessione con il server Oracle: " . $this->OracledbName . htmlentities($err_description['message']);
            exit;
        }
        return $this->dbOracle;
        /* Connessione oracle */
    }

    public function __destruct() {
        //echo 'Object was just destroyed <br>';
        //$this->disconnect();
    }

    protected function disconnect() {
        /* Disconnessione oracle */
        //OCILogoff($this->dbOracle);
        //oci_close($this->dbOracle);
        /* Disconnessione oracle */
    }

}
