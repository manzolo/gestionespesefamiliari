<?php

namespace Fi\PersonaleBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;

/**
 * Classe per gestire la struttura del Comune di Firenze
 *
 * @author Andrea Manzi
 */
class fiPersonale {

    /**
     *
     * @param string $matricola
     * @param string $data
     * @return string
     */
    private $matricola;
    private $data;
    private $cognome;
    private $nome;
    private $esiste;

    public function __construct($container, $matricola, $data = null) {
        $this->matricola = $matricola;
        if ($data) {
            $this->data = $data;
        } else {
            $this->data = date('Y-m-d');
        }
        $this->container = $container;
        $this->getInformations();
    }

    private function getInformations() {
        $this->esiste = false;

        $query = $this->getSql();
        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;

        
        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        foreach ($resultset as $row) {
            $this->esiste = true;
            $this->cognome = $row["COGNOME"];
            $this->nome = $row["NOME"];
        }
    }

    private function getSql() {
        $sql = "SELECT RAIN.CI MATRICOLA,
                        RAIN.COGNOME COGNOME,
                        RAIN.NOME NOME,
                        ANAG.DAL,ANAG.AL
                   FROM P00.ANAGRAFICI ANAG, P00.RAPPORTI_INDIVIDUALI RAIN
                  WHERE RAIN.NI = ANAG.NI ";

        $sql = $sql . " AND (TO_DATE('".$this->data."','YYYY-MM-DD') BETWEEN  ANAG.DAL AND NVL(ANAG.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";

        $sql = $sql . " AND RAIN.CI = $this->matricola";

        return $sql;
    }

    public function getUnitaOrganizzativa() {
        $query = $query = "SELECT CMP.ID_COMPONENTE ID_COMPONENTE,
                    P00.GP4_RAIN.GET_NOMINATIVO(CMP.CI) NOMINATIVO,
                    CMP.PROGR_UNITA_ORGANIZZATIVA PROGR_UNITA_ORGANIZZATIVA,
                    (SELECT ANG.DESCRIZIONE FROM SO4.ANAGRAFE_UNITA_ORGANIZZATIVE ANG WHERE ANG.PROGR_UNITA_ORGANIZZATIVA = CMP.PROGR_UNITA_ORGANIZZATIVA AND (TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN ANG.DAL AND NVL(ANG.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))) DESCRIZIONE,
                    TO_CHAR(CMP.DAL,'DD/MM/YYYY') DAL,
                    TO_CHAR(CMP.AL,'DD/MM/YYYY') AL
                FROM SO4.COMPONENTI CMP/*,
                     SO4.ATTRIBUTI_COMPONENTE  ATT*/
               WHERE CMP.CI = $this->matricola
                     /*AND ATT.ASSEGNAZIONE_PREVALENTE = 1
                     AND ATT.ID_COMPONENTE = CMP.ID_COMPONENTE*/
                     AND TO_DATE('$this->data','YYYY-MM-DD') BETWEEN CMP.DAL AND NVL(CMP.AL,TO_DATE('31/12/9999','DD/MM/YYYY'))";
        
        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;
        
        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        $unita_organizzativa = array();
        foreach ($resultset as $row) {
            $unita_organizzativa[] = array("progr_unita_organizzativa"=>$row["PROGR_UNITA_ORGANIZZATIVA"],"descrizione"=>$row["DESCRIZIONE"],"id_componente"=>$row["ID_COMPONENTE"],"dal"=>$row["DAL"],"al"=>$row["AL"]);
        }
        
        return $unita_organizzativa;
    }

    public function getMatricola() {
        return $this->matricola;
    }

    public function getData() {
        return $this->data;
    }

    function getCognome() {
        return $this->cognome;
    }

    function getNome() {
        return $this->nome;
    }

    function getNominativo() {
        return $this->getCognome() . " " . $this->getNome();
    }

    function esiste() {
        return $this->esiste;
    }

}
