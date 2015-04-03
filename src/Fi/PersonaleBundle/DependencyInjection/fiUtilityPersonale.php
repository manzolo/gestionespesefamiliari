<?php

namespace Fi\PersonaleBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;

/**
 * Classe per gestire la struttura del Comune di Firenze
 *
 * @author Andrea Manzi
 */
class fiUtilityPersonale {

    /**
     *
     * @param string $matricola
     * @param string $data
     * @return string
     */
    private $data;

    public function __construct($container, $data = null) {
        if ($data) {
            $this->data = $data;
        } else {
            $this->data = date('Y-m-d');
        }
        $this->container = $container;
    }

    public function getDirettoreGenerale() {

        $query = $this->getSqlDirettoreGenerale();
        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        $matricola = "";
        foreach ($resultset as $row) {
            $matricola = $row["MATRICOLA"];
        }
        return $matricola;
    }

    private function getSqlDirettoreGenerale() {

        $sql = "SELECT /*+PUSH_SUBQ */ PEGI.CI MATRICOLA
           FROM P00.PERIODI_GIURIDICI_GM PEGI, P00.VISTA_UNITA_ORGANIZZATIVE UO
          WHERE PEGI.RILEVANZA = 'S'
            AND P00.GP4_RAIN.GET_RAPPORTO(PEGI.CI) = 'DIP'
            AND PEGI.SETTORE = UO.NUMERO_SETTORE
            AND UO.REVISIONE = P00.GP4GM.GET_REVISIONE(PEGI.DAL) ";

        $sql = $sql . " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN  PEGI.DAL AND NVL(PEGI.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";

        $sql = $sql . " AND NVL((CASE SUBSTR(PEGI.TIPO_RAPPORTO,1,INSTR(PEGI.TIPO_RAPPORTO,'-')-1)
                       WHEN 'PO' THEN 'PO' /*PO*/
                       WHEN 'D' THEN (CASE SUBSTR(PEGI.TIPO_RAPPORTO,INSTR(PEGI.TIPO_RAPPORTO,'-')+1+1,1)
                                     WHEN 'A' THEN 'DIRE' /*DIRETTORE*/
                                     WHEN 'B' THEN 'DIRE' /*DIRETTORE*/
                                     WHEN 'C' THEN 'DIRE' /*DIRETTORE*/
                                     ELSE 'DIRI' /*DIRIGENTE*/
                                   END)
                       ELSE (CASE NVL(PEGI.TIPO_RAPPORTO,'*')
                               WHEN 'DIGE' THEN 'DIRG' /*DIRETTORE GENERALE*/
                               WHEN 'DIG1' THEN 'DIRG' /*DIRETTORE GENERALE*/
                               WHEN '*' THEN 
                                 (CASE P00.GP4_QUGI.GET_LIVELLO(PEGI.QUALIFICA,
                                     NVL(PEGI.AL, TO_DATE(3333333, 'J'))) WHEN 'DIR' THEN 'DIRI' /*DIRIGENTE*/   WHEN 'SEG' THEN 'SEG' /*SEGRETARIO GENERALE COME COORDINATORE D'AREA*/ ELSE 'DIP'/*DIPENDENTE*/ END)
                                     
                               ELSE (CASE SUBSTR(PEGI.TIPO_RAPPORTO,1,3)
                                     WHEN 'ARE' THEN 'CA' /*COORDINATORE D'AREA*/
                                     ELSE NULL
                                   END)
                             END)
                     END),
                     'DIP' /*DIPENDENTE*/) = 'DIRG' ";

        return $sql;
    }

    public function getPersonaleInServizio() {
        $query = $this->getSqlPersonaleInServizio();
        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        
        return $resultset;
    }

    private function getSqlPersonaleInServizio() {

        $sql = "SELECT /*+PUSH_SUBQ */ PEGI.CI MATRICOLA
           FROM P00.PERIODI_GIURIDICI_GM PEGI, P00.VISTA_UNITA_ORGANIZZATIVE UO
          WHERE PEGI.RILEVANZA = 'S'
            AND P00.GP4_RAIN.GET_RAPPORTO(PEGI.CI) = 'DIP'
            AND PEGI.SETTORE = UO.NUMERO_SETTORE
            AND UO.REVISIONE = P00.GP4GM.GET_REVISIONE(PEGI.DAL) ";

        $sql = $sql . " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN  PEGI.DAL AND NVL(PEGI.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";
        //$sql = $sql . " AND CI IN (59495,59207) ";
        //$sql = $sql . " AND NVL(UO.DESCR_UO_PADRE_LIV_2, '-') /*DIREZIONE*/ = 'DIREZIONE SISTEMI INFORMATIVI'";

        return $sql;
    }

    public function getData() {
        return $this->data;
    }

}
