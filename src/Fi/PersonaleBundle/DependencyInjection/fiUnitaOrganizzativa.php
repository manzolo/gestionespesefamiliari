<?php

namespace Fi\PersonaleBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;

/**
 * Classe per gestire la struttura del Comune di Firenze
 *
 * @author Andrea Manzi
 */
class fiUnitaOrganizzativa {

    /**
     *
     * @param string $matricola
     * @param string $data
     * @return string
     */
    private $progr_unita_organizzativa;
    private $matricola_responsabile;
    private $nominativo_responsabile;
    private $id_unita_padre;
    private $id_elemento;
    private $data;
    private $descrizione;
    private $esiste;
    protected $container;

    public function __construct($container, $progr_unita_organizzativa, $data = null) {
        $this->container = $container;
        if (!$data) {
            $this->data = date('Y-m-d');
        } else {
            $this->data = $data;
        }
        $this->progr_unita_organizzativa = $progr_unita_organizzativa;

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
            $this->descrizione = $row["DESCRIZIONE"];
            $this->id_elemento = $row["ID_ELEMENTO"];
            $this->id_unita_padre = $row["ID_UNITA_PADRE"];
            $this->nominativo_responsabile = "";
            $this->matricola_responsabile = "";
            if ($row["MATRICOLA_RESPONSABILE"]) {
                $resp = new fiPersonale($this->container, $row["MATRICOLA_RESPONSABILE"]);
                $this->matricola_responsabile = $resp->getMatricola();
                $this->nominativo_responsabile = $resp->getNominativo();
            }
        }
    }

    private function getSql() {
        $sql = "SELECT UO.PROGR_UNITA_ORGANIZZATIVA, UO.ID_UNITA_PADRE, UO.ID_ELEMENTO,"
                . " (SELECT ANG.DESCRIZIONE FROM SO4.ANAGRAFE_UNITA_ORGANIZZATIVE ANG WHERE ANG.PROGR_UNITA_ORGANIZZATIVA = UO.PROGR_UNITA_ORGANIZZATIVA AND (TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN ANG.DAL AND NVL(ANG.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))) DESCRIZIONE, "
                . " (SELECT DISTINCT COMPONENTE.CI 
                            FROM SO4.COMPONENTI COMPONENTE, SO4.ATTRIBUTI_COMPONENTE ATTRIBUTI, SO4.TIPI_INCARICO TIPIINCARICO
                            WHERE COMPONENTE.ID_COMPONENTE = ATTRIBUTI.ID_COMPONENTE
                            AND ATTRIBUTI.INCARICO = TIPIINCARICO.INCARICO
                            AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN ATTRIBUTI.DAL AND NVL(ATTRIBUTI.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                            AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN COMPONENTE.DAL AND NVL(COMPONENTE.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                            AND TIPIINCARICO.RESPONSABILE = 'SI'
                            AND COMPONENTE.PROGR_UNITA_ORGANIZZATIVA = UO.PROGR_UNITA_ORGANIZZATIVA
                            AND ROWNUM < 2) MATRICOLA_RESPONSABILE "
                . " FROM  SO4.UNITA_ORGANIZZATIVE UO "
                . " WHERE  1 = 1 ";
        $sql = $sql . " AND UO.PROGR_UNITA_ORGANIZZATIVA = " . $this->progr_unita_organizzativa;
        $sql = $sql . " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN  UO.DAL AND NVL(UO.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";
        //echo $sql;exit;
        return $sql;
    }

    /**
     * Restituisce il responsabile della UO passata alla data richiesta<br/>
     * Se la UO non ha responsabile diretto ritorna un array vuoto<br/>
     *
     * @param $param Array $param<br/>$param["uo"]<br/>$param["datariferimento"]
     * @return Array $ret["matricola"]<br/>$ret["nominativo"]
     */
    function getResponsabileDiretto() {

        $query = "select co.ci matricola, p00.gp4_rain.GET_NOMINATIVO(co.ci) nominativo
                    from so4.componenti           co,
                         so4.attributi_componente attr,
                         so4.tipi_incarico        ti
                   where co.id_componente = attr.id_componente
                     and attr.incarico = ti.incarico
                     and (to_date('$this->data', 'YYYY-MM-DD') BETWEEN attr.DAL AND NVL(attr.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                     and (to_date('$this->data', 'YYYY-MM-DD') BETWEEN co.DAL AND NVL(co.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                     /*and co.stato = 'D'*/
                     and ti.responsabile = 'SI'
                     and co.progr_unita_organizzativa = $this->progr_unita_organizzativa
            ";
        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        foreach ($resultset as $row) {
            $responsabile = array("matricola" => $row["MATRICOLA"], "nominativo" => $row["NOMINATIVO"]);
        }

        return $responsabile;
    }
    
    /**
     * Restituisce il responsabile della UO passata alla data richiesta<br/>
     * (cerca fino a 5 livelli sopra per cercare il responsabile)
     *
     * @param $param Array $param<br/>$param["uo"]<br/>$param["datariferimento"]
     * @return Array $ret["matricola"]<br/>$ret["nominativo"]
     */
    function getResponsabile() {
        $idx = 0;
        do {
            $idx++;
            $info = $this->getResponsabileDiretto();
            if (!($info["matricola"])) {
                $parmpadre = new fiUnitaOrganizzativa($this->container, $this->getIdUnitaPadre());                
                $info = $parmpadre->getResponsabileDiretto();
            } else {
                break;
            }
        } while ($idx < 5);

        return $info;
    }

    public function getProgrUnitaOrganizzativa() {
        return $this->progr_unita_organizzativa;
    }

    public function getData() {
        return $this->data;
    }

    public function getDescrizione() {
        return $this->descrizione;
    }

    public function getIdElemento() {
        return $this->id_elemento;
    }

    public function getIdUnitaPadre() {
        return $this->id_unita_padre;
    }

    public function getMatricolaResponsabile() {
        return $this->matricola_responsabile;
    }

    public function getNominativoResponsabile() {
        return $this->nominativo_responsabile;
    }

    function esiste() {
        return $this->esiste;
    }

}
