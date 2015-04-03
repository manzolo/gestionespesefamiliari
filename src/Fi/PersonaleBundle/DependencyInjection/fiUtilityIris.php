<?php

namespace Fi\PersonaleBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;

/**
 * Classe per gestire i dipendenti del Comune di Firenze
 *
 * @author Angela Bianchi
 * @author Andrea Manzi
 * @author Lisa Pescini
 */
class fiUtilityIris {

    private $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function getProgressivoByMatricola($matricola) {

        $query = "SELECT PROGRESSIVO FROM MONDOEDP.T030_ANAGRAFICO ANAG WHERE ANAG.MATRICOLA = $matricola";
        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $progressivo = 0;
        try {
            $resultset = $connessione->getResultset();
            foreach ($resultset as $row) {
                $progressivo = $row["PROGRESSIVO"];
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return 0;
        }
        return (int) $progressivo;
    }

    public function getMatricolaByProgressivo($progressivo) {

        $query = "SELECT MATRICOLA FROM MONDOEDP.T030_ANAGRAFICO ANAG WHERE ANAG.PROGRESSIVO = $progressivo";
        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $matricola = 0;
        try {
            $resultset = $connessione->getResultset();
            foreach ($resultset as $row) {
                $matricola = $row["MATRICOLA"];
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return 0;
        }
        return (int) $matricola;
    }

    public function getInfo($progressivo, $data = null) {
        if (!$data) {
            $data = date('Y-m-d');
        }

        //$query = SELECT * FROM MONDOEDP.T221_PROFILISETTIMANA where codice = '';
        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);

        try {
            $resultset = $connessione->getResultset();
            foreach ($resultset as $row) {
                $ritorno = array("datadecorrenza" => $row["DATADECORRENZA"], "datafine" => $row["DATAFINE"], "badge" => $row["BADGE"], "indirizzo" => $row["INDIRIZZO"], "inizioservizio" => $row["INIZIO"], "fineservizio" => $row["FINE"], "orario" => $row["orario"], "calendario" => $row["CALENDARIO"], "porario" => $row["PORARIO"], "passenze" => $row["PASSENZE"], "parttime" => $row["PARTTIME"],);
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return array();
        }
        return $ritorno;
    }

    public function getUtentiGruppo($codicegruppo, $data = null) {
        if (!$data) {
            $data = date('Y-m-d');
        }

        $query = "select ang.cognome, ang.nome, sto.* 
        from t430_storico sto, t030_anagrafico ang 
        where sto.gruppo = '$codicegruppo' 
        and ang.progressivo = sto.progressivo 
        and TO_DATE('$data','YYYY-MM-DD') between datadecorrenza and nvl(datafine,to_date('31/12/9999','DD/MM/YYYY'))
        and TO_DATE('$data','YYYY-MM-DD') between inizio and nvl(fine,to_date('31/12/9999','DD/MM/YYYY'))";

        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $ritorno = array();
        try {
            $resultset = $connessione->getResultset();
            foreach ($resultset as $row) {
                $ritorno[] = array("matricola"=>$row["BADGE"],"cognome"=>$row["COGNOME"],"nome"=>$row["NOME"]);
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return array();
        }
        return $ritorno;
    }

    /*
     * 
     * select ang.cognome, ang.nome, sto.* 
      from t430_storico sto, t030_anagrafico ang
      where sto.gruppo = '006'
      and ang.progressivo = sto.progressivo
      and trunc(sysdate) between datadecorrenza and nvl(datafine,to_date('31/12/9999','DD/MM/YYYY'))
      and trunc(sysdate) between inizio and nvl(fine,to_date('31/12/9999','DD/MM/YYYY'))
     */
}
