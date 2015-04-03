<?php

namespace Fi\PersonaleBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;

/**
 * Classe per gestire la struttura del Comune di Firenze
 *
 * @author Andrea Manzi
 */
class fiUtilityUnitaOrganizzativa {

    private $data;
    private $esiste;
    protected $container;

    public function __construct($container, $data = null) {
        $this->container = $container;
        if (!$data) {
            $this->data = date('Y-m-d');
        } else {
            $this->data = $data;
        }
    }

    public function getProgrUnitaOrganizzativaByCodiceUo($codice_uo) {

        $query = "select PROGR_UNITA_ORGANIZZATIVA from SO4.ANAGRAFE_UNITA_ORGANIZZATIVE ANAG
      WHERE ANAG.CODICE_UO ='" . $codice_uo . "' AND TO_DATE('" . $this->data . "','YYYY-MM-DD')
      BETWEEN ANAG.DAL AND NVL(ANAG.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))";

        $connessione = $this->container->get("oracle_sigru");

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        $progr_unita_organizzativa = null;
        foreach ($resultset as $row) {
            $progr_unita_organizzativa = $row["PROGR_UNITA_ORGANIZZATIVA"];
        }
        return $progr_unita_organizzativa;
    }

    public function getElencoDirezioni() {

        $query = $this->getElencoDirezioniSql();

        $connessione = $this->container->get("oracle_sigru");

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        $elencoDirezioni = array();
        foreach ($resultset as $row) {
            $elencoDirezioni[] = array(
                "progr_unita_organizzativa"=> $row["PROGR_UNITA_ORGANIZZATIVA"],
                "id_unita_padre"=> $row["ID_UNITA_PADRE"],
                "descrizione"=> $row["DESCRIZIONE"]
                );
        }
        return $elencoDirezioni;
    }

    private function getElencoDirezioniSql() {
        //Questa l'ho inventata così alla volè, ovviamente senza conoscere le tabelle e provando ad unire 2 database (SIGRU E SOA)
        return "SELECT uo.progr_unita_organizzativa PROGR_UNITA_ORGANIZZATIVA,
                uo.id_unita_padre ID_UNITA_PADRE,
                auo.descrizione DESCRIZIONE
                from so4.unita_organizzative uo, so4.anagrafe_unita_organizzative auo, P00.VISTA_UNITA_ORGANIZZATIVE SIGRU_UO
               where SIGRU_UO.REVISIONE = P00.GP4GM.GET_REVISIONE(TO_DATE('" . $this->data . "','YYYY-MM-DD'))
                 /*AND NVL(SIGRU_UO.DESCR_UO_PADRE_LIV_2, '-') = auo.DESCRIZIONE*/
                AND auo.progr_unita_organizzativa = uo.progr_unita_organizzativa
                and SIGRU_UO.livello = 2 /*1 aree, 2 direzioni, 3 servizi ecc*/
                 AND auo.progr_unita_organizzativa = uo.progr_unita_organizzativa
                 and SIGRU_UO.descr_unita_organizzative = auo.descrizione
                 and (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN UO.DAL AND NVL(UO.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                 and (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN AUO.DAL AND NVL(AUO.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
              ";
    }

    public function getData() {
        return $this->data;
    }

}
