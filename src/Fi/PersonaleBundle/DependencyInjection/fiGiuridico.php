<?php

namespace Fi\PersonaleBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;

/**
 * Classe per gestire la struttura del Comune di Firenze
 *
 * @author Andrea Manzi
 */
class fiGiuridico {

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
    private $figura;
    private $inquadramento;
    private $area;
    private $direzione;
    private $servizio;
    private $po;
    private $dal;
    private $al;
    private $codiceRuolo;
    private $diRuolo;
    private $esiste;

    public function __construct($container, $matricola, $data = null) {
        $this->matricola = $matricola;
        if (!$data) {
            $this->data = date('Y-m-d');
        } else {
            $this->data = $data;
        }
        $this->esiste = false;

        $query = $this->getSql();
        $this->container = $container;
        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        foreach ($resultset as $row) {
            $this->esiste = true;
            $this->cognome = $row["COGNOME"];
            $this->nome = $row["NOME"];
            $this->inquadramento = $row["INQUADRAMENTO"];
            $this->figura = $row["FIGURA"];
            $this->area = $row["AREA"];
            $this->direzione = $row["DIREZIONE"];
            $this->servizio = $row["SERVIZIO"];
            $this->po = $row["PO"];
            $this->dal = $row["DAL"];
            $this->al = $row["AL"];
            $this->codiceRuolo = $row["CODICERUOLO"];
            $this->diRuolo = ($row["DI_RUOLO"] == 'SI' ? true : false);
        }
    }

    private function getSql() {
        $sql = "SELECT PEGI.CI MATRICOLA,
                P00.GP4_RAIN.GET_COGNOME(PEGI.CI) COGNOME,
                P00.GP4_RAIN.GET_NOME(PEGI.CI) NOME,
                TO_CHAR(P00.GP4_RAIN.GET_DATA_NAS(PEGI.CI),'DD/MM/YYYY') DATA_NASCITA,
                P00.GP4_ANAG.GET_SESSO(GP4_RAIN.GET_NI(PEGI.CI)) SESSO,
                P00.GP4_QUGI.GET_LIVELLO(PEGI.QUALIFICA,
                                     NVL(PEGI.AL, TO_DATE(3333333, 'J'))) INQUADRAMENTO,
                P00.GP4_FIGI.GET_DESCRIZIONE(PEGI.FIGURA,
                                         NVL(PEGI.AL, TO_DATE(3333333, 'J'))) FIGURA,
                PEGI.TIPO_RAPPORTO RAPPORTO,
                GP4_POSI.GET_RUOLO(PEGI.POSIZIONE) DI_RUOLO,
                NVL(UO.DESCR_UO_PADRE_LIV_1, '-') AREA,
                NVL(UO.DESCR_UO_PADRE_LIV_2, '-') DIREZIONE,
                NVL(UO.DESCR_UO_PADRE_LIV_3, '-') SERVIZIO,
                NVL(UO.DESCR_UO_PADRE_LIV_4, '-') PO,
                 TO_CHAR(PEGI.DAL,'DD/MM/YYYY') DAL,
                 TO_CHAR(PEGI.AL,'DD/MM/YYYY') AL,
                 
                NVL((CASE SUBSTR(PEGI.TIPO_RAPPORTO,1,INSTR(PEGI.TIPO_RAPPORTO,'-')-1)
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
                     'DIP' /*DIPENDENTE*/) AS CODICERUOLO

           FROM P00.PERIODI_GIURIDICI_GM PEGI, P00.VISTA_UNITA_ORGANIZZATIVE UO
          WHERE PEGI.RILEVANZA = 'S'
            AND P00.GP4_RAIN.GET_RAPPORTO(PEGI.CI) = 'DIP'
            AND PEGI.SETTORE = UO.NUMERO_SETTORE
            AND UO.REVISIONE = P00.GP4GM.GET_REVISIONE(PEGI.DAL) ";

        $sql = $sql . " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN PEGI.DAL AND NVL(PEGI.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";

        $sql = $sql . " AND PEGI.CI = " . $this->matricola;

        return $sql;
    }

    public function getMatricola() {
        return $this->matricola;
    }

    function getCognome() {
        return $this->cognome;
    }

    function getNome() {
        return $this->nome;
    }

    function getFigura() {
        return $this->figura;
    }

    function getArea() {
        return $this->area;
    }

    function getDirezione() {
        return $this->direzione;
    }

    function esiste() {
        return $this->esiste;
    }

    function getServizio() {
        return $this->servizio;
    }

    function getPo() {
        return $this->po;
    }

    function getDalPeriodoGiuridico() {
        return $this->dal;
    }

    function getAlPeriodoGiuridico() {
        return $this->al;
    }

    function getCodiceRuolo() {
        return $this->codiceRuolo;
    }

    function getInquadramento() {
        return $this->inquadramento;
    }

    function getDiRuolo() {
        return $this->diRuolo;
    }

}
