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
class fiUtentiIris {

    private $utente;
    private $container;
    private $gruppoUtente;
    private $password;
    private $profilo;
    private $filtroanagrafe;

    public function __construct($container, $utente) {
        $this->utente = $utente;
        $this->container = $container;

        $query = $this->getSqlUtenti();
        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;

        $dataqueryutente = array("query_parameters" => array(":utente" => $this->utente));
        $connessione->executeSelectQuery($query,$dataqueryutente);
        $resultset = $connessione->getResultset();
        foreach ($resultset as $row) {
            $this->utente = $row["UTENTE"];
            $this->password = $row["PASSWD"];
            $this->profilo = $row["FILTRO_ANAGRAFE"]; //"TUTTI_DIPENDENTI"
        }

        $query = $this->getSqlFiltriUtenti();
        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;
        
        $dataqueryprofilo = array("query_parameters" => array(":profilo" => $this->profilo));
        $connessione->executeSelectQuery($query,$dataqueryprofilo);
        $resultset = $connessione->getResultset();
        $filtro = "";
        foreach ($resultset as $row) {
            if (substr((trim($row["FILTRO"])), 0, 2) == '--') {
                continue;
            }
            $filtro = $filtro . $row["FILTRO"];
        }
        if (strlen($filtro) > 0) {
            $filtro = " AND " . $filtro;
        }
        $this->filtroanagrafe = $filtro;
    }

    public function getDipendentiGestiti($data = null) {
        if (!$data) {
            $data = date('Y-m-d');
        }
        $query = $this->getSqlDipendentiGestiti($data);
        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;
        $dataquery = array("query_parameters" => array(":data" => $data));
        $connessione->executeSelectQuery($query, $dataquery);
        $dipendenti = array();
        try {
            $resultset = $connessione->getResultset();
            foreach ($resultset as $row) {
                $dipendenti[] = array("matricola" => $row["MATRICOLA"], "nominativo" => $row["COGNOME"] . " " . $row["NOME"]);
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $dipendenti;
    }

    public function getIrisPassword() {
        return $this->password;
    }

    public function getPasswordCracked() {

        $vettorelettere = array();
        $pwd = $this->getIrisPassword();

        for ($i = 0, $j = 0; $i < strlen($pwd); $i+=2, $j++) {

            $vettorelettere[$j] = "0x" . $pwd[$i] . $pwd[$i + 1];
        }

        $lunghezza = count($pwd);
        $i = 0;
        foreach ($vettorelettere as $lettera) {
            $risposta[($lunghezza - $i++)] = strtoupper(chr(158 - $lettera));
        }

        ksort($risposta);

        return implode($risposta);
    }

    public function checkPassword($passwd) {
        $vettorelettere = str_split($passwd);
        $lunghezza = strlen($passwd);
        $i = 0;
        foreach ($vettorelettere as $lettera) {
            $risposta[($lunghezza - $i++)] = strtoupper(dechex(158 - ord($lettera)));
        }

        ksort($risposta);

        return (implode($risposta) === $this->getIrisPassword());
    }

    private function getSqlUtenti() {
        $sql = "SELECT UTE.* "
                . " FROM I070_UTENTI UTE"
                . " WHERE 1 = 1 ";
        $sql = $sql . " AND UTE.UTENTE = :utente AND PERMESSI = 'OPERATORE'";
        return $sql;
    }

    private function getSqlFiltriUtenti() {
        $sql = "SELECT FILTRI.* "
                . " FROM I072_FILTROANAGRAFE FILTRI"
                . " WHERE 1 = 1 ";
        $sql = $sql . " AND FILTRI.PROFILO = :profilo";
        $sql = $sql . " ORDER BY FILTRI.PROFILO, FILTRI.PROGRESSIVO";
        return $sql;
    }

    public function getUtente() {
        return $this->utente;
    }

    public function getGruppoUtente() {
        return $this->gruppoUtente;
    }

    public function getGruppi() {
        return $this->gruppoUtente;
    }

    private function getSqlDipendentiGestiti($data) {
        $sql = "SELECT T030.COGNOME COGNOME, T030.NOME NOME, STO.T430BADGE MATRICOLA
                FROM V430_STORICO STO, T030_ANAGRAFICO T030 
                WHERE 1 = 1
                AND T030.PROGRESSIVO = STO.T430PROGRESSIVO 
                AND TO_DATE(:data,'YYYY-MM-DD') BETWEEN STO.T430DATADECORRENZA AND NVL(STO.T430DATAFINE,TO_DATE('31/12/9999','DD/MM/YYYY'))
                AND TO_DATE(:data,'YYYY-MM-DD') BETWEEN STO.T430INIZIO AND NVL(STO.T430FINE,TO_DATE('31/12/9999','DD/MM/YYYY')) ";

        $sql = $sql . $this->filtroanagrafe;
        $sql = $sql . " ORDER BY T030.COGNOME ASC,T030.NOME ASC";
        return $sql;
    }

    public function getResponsabiliGestiti($data = null) {
        if (!$data) {
            $data = date('Y-m-d');
        }
        $query = $this->getSqlDipendentiGestiti($data);
        $this->OracleConnection = $this->container->get("oracle_iris");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $dipendenti = array();
        try {
            $resultset = $connessione->getResultset();
            foreach ($resultset as $row) {
                $fiOrganigramma = new fiOrganigramma($this->container, $row["MATRICOLA"], $data);
                $fipersonale = new fiPersonale($this->container, $row["MATRICOLA"], $data);
                $uos = $fipersonale->getUnitaOrganizzativa();
                foreach ($uos as $uo) {
                    if ($fiOrganigramma->isResponsabile($uo["progr_unita_organizzativa"])) {
                        $dipendenti[] = array("matricola" => $row["MATRICOLA"], "nominativo" => $row["COGNOME"] . " " . $row["NOME"]);
                        break 1;
                    }
                }
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $dipendenti;
    }

}
