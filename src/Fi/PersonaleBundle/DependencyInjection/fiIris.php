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
class fiIris {

  protected $matricola;
  protected $data;
  protected $anno;
  protected $mese;
  protected $cognome;
  protected $nome;
  protected $progressivo;
  protected $calendario;
  protected $orario;
  protected $porario;
  protected $passenze;
  protected $settimana;

  public function __construct($container, $matricola, $data = null) {
    $this->matricola = $matricola;

    if ($data) {
      $this->data = $data;
    } else {
      $this->data = date('Y-m-d');
    }

    $this->anno = substr($this->getData(), 0, 4);
    $this->mese = substr($this->getData(), 5, 2);


    $this->container = $container;
    $this->getAnagrafico();
    $this->getStorico();
    $this->getQuotidiano();

    /* var_dump($information);
      var_dump($ferietotali);
      if (($information == false) || ($ferietotali == false)) {
      $messaggio = 'Non sono disponibili i dati su Iris, avvertire la segreteria!';
      //throw new \Exception($messaggio);
      throw new \ErrorException($messaggio, 100);
      //throw new \Symfony\Component\HttpKernel\Exception\HttpException(404, $messaggio);
      } */
  }

  protected function getAnagrafico() {
    $esiste = false;

    $query = $this->getSqlAnagrafico();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    foreach ($resultset as $row) {
      $esiste = true;
      $this->progressivo = $row["PROGRESSIVO"];
      $this->cognome = $row["COGNOME"];
      $this->nome = $row["NOME"];
    }
    return $esiste;
  }

  protected function getStorico() {
    $query = $this->getSqlStorico();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    foreach ($resultset as $row) {
      $this->calendario = $row["CALENDARIO"];
      $this->orario = $row["ORARIO"];
      $this->porario = $row["PORARIO"];
      $this->passenze = $row["PASSENZE"];
    }
  }

  protected function getQuotidiano() {
    $query = $this->getSqlQuotidiano();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    foreach ($resultset as $row) {

      for ($i = 1; $i <= 7; $i++) {
        $codice = $row[$this->getNomeGiorno($i)];

        $query = $this->getSqlOrarioQuotidiano($codice);
        $connessione->executeSelectQuery($query);
        $oregiorno = $connessione->getResultset();
        $vettsettimana[$i]["ore"] = isset($oregiorno[0]["ORETEOR"]) ? $oregiorno[0]["ORETEOR"] : 0;
        $vettsettimana[$i]["pausa"] = isset($oregiorno[0]["MMMINIMI"]) ? $oregiorno[0]["MMMINIMI"] : 0;
        $vettsettimana[$i]["giornolungo"] = isset($oregiorno[0]["PM_AUTO_URIT"]) ? ($oregiorno[0]["PM_AUTO_URIT"] == 'S' ? true : false) : false;

        $query = $this->getSqlOrarioE_U($codice);
        $connessione->executeSelectQuery($query);
        $oregiorno = $connessione->getResultset();

        $vettsettimana[$i]["entrata"] = isset($oregiorno[0]["ENTRATA"]) ? $oregiorno[0]["ENTRATA"] : 0;
        $vettsettimana[$i]["uscita"] = isset($oregiorno[0]["USCITA"]) ? $oregiorno[0]["USCITA"] : 0;

        $this->settimana = $vettsettimana;
        //var_dump($vettsettimana);exit;
      }
    }
  }

  protected function getSqlQuotidiano() {
    $sql = "SELECT * FROM ( SELECT * FROM MONDOEDP.T221_PROFILISETTIMANA WHERE CODICE='" . $this->porario . "' AND DECORRENZA <= TRUNC(SYSDATE) ORDER BY PROGRESSIVO DESC) WHERE ROWNUM <= 2";
    //echo $sql;    exit;
    return $sql;
  }

  protected function getSqlOrarioQuotidiano($codice) {
    $sql = "SELECT * FROM ( SELECT * FROM MONDOEDP.T020_ORARI WHERE CODICE = '" . $codice . "' AND DECORRENZA <= TRUNC(SYSDATE) ORDER BY DECORRENZA DESC) WHERE ROWNUM = 1";
    //echo $sql . "<br>";
    return $sql;
  }

  protected function getSqlOrarioE_U($codice, $fascia = "PN") {
    $sql = "select  * from MONDOEDP.t021_fasceorari where 
 decorrenza=( select max(decorrenza) from MONDOEDP.t021_fasceorari
 where codice=  '" . $codice . "' and tipo_fascia='$fascia' )
 and  codice= '" . $codice . "' and tipo_fascia='$fascia'";

    //echo $sql . "<br>";
    return $sql;
  }

  protected function getSqlAnagrafico() {
    $sql = "SELECT ANAG.PROGRESSIVO,ANAG.COGNOME,ANAG.NOME "
            . " FROM MONDOEDP.T030_ANAGRAFICO ANAG"
            . " WHERE 1 = 1 ";
    $sql = $sql . " AND ANAG.MATRICOLA = " . $this->matricola;
    return $sql;
  }

  protected function getSqlStorico() {
    $sql = "SELECT STO.AREA, STO.ZONA, STO.PRESIDIO, STO.SETTORE, STO.ORARIO, STO.CONTRATTO, STO.ORARIO, " .
            " STO.CALENDARIO, STO.PORARIO, STO.PASSENZE, STO.QUALIFICA, STO.LIVELLO, STO.FIGURA, STO.GRUPPO "
            . " FROM MONDOEDP.T030_ANAGRAFICO ANAG, MONDOEDP.T430_STORICO STO "
            . " WHERE ANAG.PROGRESSIVO = STO.PROGRESSIVO "
            . " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN STO.DATADECORRENZA AND NVL(STO.DATAFINE, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";
    $sql = $sql . " AND ANAG.PROGRESSIVO = " . $this->progressivo;
    //echo $sql;exit;
    return $sql;
  }

  protected function getFerieResidueAnno($anno) {
    $query = $this->getSqlFerieResidueAnno($anno);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $ferieresidueannoprecedente = 0;

    foreach ($resultset as $row) {
      $ferieresidueannoprecedente = $row["RESIDUE"];
    }
    return $ferieresidueannoprecedente;
  }

  protected function getSqlFerieResidueAnno($anno) {
    $sql = "SELECT TO_NUMBER(RESIDASSANN.RESIDUO1) AS RESIDUE FROM MONDOEDP.T264_RESIDASSANN RESIDASSANN WHERE RESIDASSANN.PROGRESSIVO=" . $this->getProgressivo() . " AND RESIDASSANN.CODRAGGR='FERIE' AND RESIDASSANN.ANNO=" . $anno;
    //echo $sql;exit;
    return $sql;
  }

  protected function getResidueAnno($anno, $causale) {
    $query = $this->getSqlResidueAnno($anno, $causale);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $ferieresidueannoprecedente = 0;

    foreach ($resultset as $row) {
      $ferieresidueannoprecedente = $row["RESIDUE"];
    }
    return $ferieresidueannoprecedente;
  }

  protected function getSqlResidueAnno($anno, $causale) {
    $raggruppamento = $this->getRaggruppamento($causale);
    $sql = "SELECT TO_NUMBER(RESIDASSANN.RESIDUO1) AS RESIDUE FROM MONDOEDP.T264_RESIDASSANN RESIDASSANN WHERE RESIDASSANN.PROGRESSIVO=" . $this->getProgressivo() . " AND RESIDASSANN.CODRAGGR='" . $raggruppamento . "' AND RESIDASSANN.ANNO=" . $anno;
    //echo $sql;exit;
    return $sql;
  }

  protected function getFerieGoduteAnno($anno) {
    $query = $this->getSqlFerieGoduteAnno($anno);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $feriegodute = 0;
    foreach ($resultset as $row) {
      $feriegodute = (int) $row["GODUTE"];
    }
    return $feriegodute;
  }

  protected function getSqlFerieGoduteAnno($anno) {
    $sql = "SELECT COUNT(CAUSALE) AS GODUTE FROM MONDOEDP.T040_GIUSTIFICATIVI GIUSTIFICATIVI WHERE GIUSTIFICATIVI.PROGRESSIVO=" . $this->getProgressivo() . " AND GIUSTIFICATIVI.CAUSALE='A00' AND TO_CHAR(GIUSTIFICATIVI.DATA,'YYYY') = " . $anno . " AND GIUSTIFICATIVI.DATA <= TO_DATE('" . $this->data . "','YYYY-MM-DD')";
    //echo $sql;exit;
    return $sql;
  }

  protected function getGoduteAnno($anno, $causale) {
    $query = $this->getSqlGoduteAnno($anno, $causale);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $feriegodute = 0;
    foreach ($resultset as $row) {
      $feriegodute = (int) $row["GODUTE"];
    }
    return $feriegodute;
  }

  protected function getSqlGoduteAnno($anno, $causale) {
    $sql = "SELECT COUNT(CAUSALE) AS GODUTE FROM MONDOEDP.T040_GIUSTIFICATIVI GIUSTIFICATIVI WHERE GIUSTIFICATIVI.PROGRESSIVO=" . $this->getProgressivo() . " AND GIUSTIFICATIVI.CAUSALE='" . $causale . "' AND TO_CHAR(GIUSTIFICATIVI.DATA,'YYYY') = " . $anno . " AND GIUSTIFICATIVI.DATA <= TO_DATE('" . $this->data . "','YYYY-MM-DD')";
    //echo $sql;exit;
    return $sql;
  }

  protected function getFerieTotaliProfilo($anno) {

    $query = $this->getSqlFerieTotaliProfilo($anno);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $ferietotaliannoprofilo = 0;

    foreach ($resultset as $row) {
      $ferietotaliannoprofilo = (int) $row["TOTALIANNOPROFILO"];
    }

    return $ferietotaliannoprofilo;
  }

  protected function getSqlFerieTotaliProfilo($anno) {
    $sql = "SELECT (SELECT TO_NUMBER(REPLACE(PROFASSANN.COMPETENZA1,',','')) FROM MONDOEDP.T262_PROFASSANN PROFASSANN WHERE PROFASSANN.CODPROFILO=STO.PASSENZE AND PROFASSANN.ANNO=" . $anno . " ) AS TOTALIANNOPROFILO
           FROM MONDOEDP.T430_STORICO STO
          INNER JOIN MONDOEDP.T262_PROFASSANN PROFASSANN
             ON STO.PASSENZE = PROFASSANN.CODPROFILO
          WHERE PROFASSANN.ANNO = " . $anno .
            " AND STO.PROGRESSIVO = " . $this->getProgressivo()
            . " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN STO.DATADECORRENZA AND NVL(STO.DATAFINE, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";
    //echo $sql;exit;
    return $sql;
  }

  protected function getTotaliProfilo($anno, $causale) {
    $query = $this->getSqlTotaliProfilo($anno, $causale);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $ferietotaliannoprofilo = 0;

    foreach ($resultset as $row) {
      $ferietotaliannoprofilo = (int) $row["TOTALIANNOPROFILO"];
    }
    return $ferietotaliannoprofilo;
  }

  protected function getSqlTotaliProfilo($anno, $causale) {

    $raggruppamento = $this->getRaggruppamento($causale);

    $sql = "SELECT (SELECT TO_NUMBER(REPLACE(PROFASSANN.COMPETENZA1,',','')) FROM MONDOEDP.T262_PROFASSANN PROFASSANN WHERE PROFASSANN.CODPROFILO=STO.PASSENZE AND PROFASSANN.ANNO=" . $anno . " ) AS TOTALIANNOPROFILO
           FROM MONDOEDP.T430_STORICO STO
          INNER JOIN MONDOEDP.T262_PROFASSANN PROFASSANN
             ON STO.PASSENZE = PROFASSANN.CODPROFILO
          WHERE PROFASSANN.ANNO = " . $anno .
            " AND STO.PROGRESSIVO = " . $this->getProgressivo() .
            " AND PROFASSANN.CODRAGGR = '" . $raggruppamento . "'" .
            " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN STO.DATADECORRENZA AND NVL(STO.DATAFINE, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";
    //echo $sql;exit;
    return $sql;
  }

  public function getGiustificativoGiorno() {
    $query = $this->getSqlGiustificativoGiorno();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $causaligiorno = array();
    foreach ($resultset as $row) {
      array_push($causaligiorno, $row["CAUSALE"]);
    }

    $vettorestraordinari = $this->getStraordinari(true);

    foreach ($vettorestraordinari as $stra) {
      array_push($causaligiorno, $stra["causale"]);
    }
    $vettorestraordinarifesta = $this->getStraordinari(true, "XL");

    foreach ($vettorestraordinarifesta as $stra) {
      array_push($causaligiorno, $stra["causale"]);
    }

    return $causaligiorno;
  }

  protected function getSqlGiustificativoGiorno() {
    $sql = "SELECT TO_CHAR(GIUSTIFICATIVI.DATA,'YYYY-MM-DD') AS PRENOTATE, "
            . " TO_CHAR(GIUSTIFICATIVI.DAORE,'HH24.MI') AS INIZIO, "
            . " TO_CHAR(GIUSTIFICATIVI.AORE,'HH24.MI') AS FINE, "
            . " GIUSTIFICATIVI.CAUSALE AS CAUSALE "
            . " FROM MONDOEDP.T040_GIUSTIFICATIVI GIUSTIFICATIVI "
            . " WHERE GIUSTIFICATIVI.PROGRESSIVO= " . $this->getProgressivo()
            . " AND GIUSTIFICATIVI.CAUSALE IN (SELECT CODICE FROM MONDOEDP.T265_CAUASSENZE)"
            //. " AND GIUSTIFICATIVI.CAUSALE = 'A00'"
            . " AND TO_CHAR(GIUSTIFICATIVI.DATA,'YYYY') = " . $this->anno . " "
            . " AND GIUSTIFICATIVI.DATA = TO_DATE('" . $this->data . "','YYYY-MM-DD') ";
    //echo $sql;exit;
    return $sql;
  }

  public function getGiorniFeriePrenotate() {
    $query = $this->getSqlGiorniFeriePrenotate();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $ferieprenotateanno = array();
    foreach ($resultset as $row) {
      $ferieprenotateanno[] = array("giorno" => $row["PRENOTATE"],
          "daore" => $row["INIZIO"],
          "aore" => $row["FINE"],
          "confermato" => true,
          "iris" => true
      );
    }
    return $ferieprenotateanno;
  }

  protected function getSqlGiorniFeriePrenotate() {
    $sql = "SELECT TO_CHAR(GIUSTIFICATIVI.DATA,'YYYY-MM-DD') AS PRENOTATE, "
            . " TO_CHAR(GIUSTIFICATIVI.DAORE,'HH24.MI') AS INIZIO, "
            . " TO_CHAR(GIUSTIFICATIVI.AORE,'HH24.MI') AS FINE"
            . " FROM MONDOEDP.T040_GIUSTIFICATIVI GIUSTIFICATIVI "
            . " WHERE GIUSTIFICATIVI.PROGRESSIVO= " . $this->getProgressivo()
            //. " AND GIUSTIFICATIVI.CAUSALE IN (SELECT CODICE FROM MONDOEDP.T265_CAUASSENZE)"
            . " AND GIUSTIFICATIVI.CAUSALE = 'A00'"
            . " AND TO_CHAR(GIUSTIFICATIVI.DATA,'YYYY') = " . $this->anno . " "
            . " AND GIUSTIFICATIVI.DATA >= TO_DATE('" . $this->data . "','YYYY-MM-DD') ";
    //echo $sql;exit;
    return $sql;
  }

  public function getMatricola() {
    return $this->matricola;
  }

  public function getData() {
    return $this->data;
  }

  public function getProgressivo() {
    return $this->progressivo;
  }

  function getCognome() {
    return $this->cognome;
  }

  function getSettimana() {
    return $this->settimana;
  }

  function getNome() {
    return $this->nome;
  }

  function getNominativo() {
    return $this->getCognome() . " " . $this->getNome();
  }

  public function getFerieTotali() {
    //Non viene volutamente decurtato i giorni di ferie ancora da godere (prenotazioni sia jfestere che iris)

    /*
      $ferietotali = $this->getFerieTotaliAnnoProfilo($this->anno);
      //$this->ferietotalianno
      if ($ferietotali<=0) {
      $ferietotali = $this->getFerieTotali($this->anno - 1);
      $ferietotali += $this->getFerieTotaliAnno();
      }
      $giorniFeriePrenotate = $this->getGiorniFeriePrenotate(); */

    $totale = $this->getFerieTotaliProfilo($this->anno) + $this->getFerieResidueAnno($this->anno);
    return $totale;
  }

  public function getTotali($causale) {
    //Non viene volutamente decurtato i giorni di ferie ancora da godere (prenotazioni sia jfestere che iris)

    /*
      $ferietotali = $this->getFerieTotaliAnnoProfilo($this->anno);
      //$this->ferietotalianno
      if ($ferietotali<=0) {
      $ferietotali = $this->getFerieTotali($this->anno - 1);
      $ferietotali += $this->getFerieTotaliAnno();
      }
      $giorniFeriePrenotate = $this->getGiorniFeriePrenotate(); */

    $totale = $this->getTotaliProfilo($this->anno, $causale) + $this->getResidueAnno($this->anno, $causale);
    return $totale;
  }

  public function getFerieGodute() {
    return $this->getFerieGoduteAnno($this->anno);
  }

  public function getGodute($causale) {
    return $this->getGoduteAnno($this->anno, $causale);
  }

  public function getFerieResidue() {
    return $this->getFerieTotali() - $this->getFerieGodute();
  }

  public function getResiduo($causale) {
    return $this->getTotali($causale) - $this->getGodute($causale);
  }

  public function getFeriePrenotate() {
    return count($this->getGiorniFeriePrenotate());
  }

  function getCalendario() {
    return $this->calendario;
  }

  function getOrario() {
    return $this->orario;
  }

  function getPorario() {
    return $this->porario;
  }

  function getPassenze() {
    return $this->passenze;
  }

  function getGiorniNonLavorativi($dal = '', $al = '') {
    $query = "SELECT TO_CHAR(DATA,'YYYY-MM-DD') AS GIORNO, LAVORATIVO,FESTIVO 
                 FROM MONDOEDP.T011_CALENDARI
                 WHERE codice = '" . $this->calendario . "' " .
            " AND (LAVORATIVO = 'N' OR FESTIVO = 'S')";

    if ($dal) {
      $query = $query . " AND DATA >= TO_DATE('$dal','YYYY-MM-DD')";
    }

    if ($al) {
      $query = $query . " AND DATA <= TO_DATE('$al','YYYY-MM-DD')";
    }
    //echo $query;exit;
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $giorninonlavorativi = array();
    foreach ($resultset as $row) {
      $giorninonlavorativi[] = array("giorno" => $row["GIORNO"], "lavorativo" => ($row["LAVORATIVO"] == 'S' ? true : false), "festivo" => ($row["FESTIVO"] == 'S' ? true : false));
    }
    return $giorninonlavorativi;
  }

  protected function getRaggruppamento($causale) {
    $query = $this->getSqlRaggruppamento($causale);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $ferieprenotateanno = array();
    foreach ($resultset as $row) {
      $raggruppamento = $row["RAGGRUPPAMENTO"];
    }
    return $raggruppamento;
  }

  protected function getSqlRaggruppamento($causale) {
    $sql = "SELECT CAUASSENZE.CODRAGGR AS RAGGRUPPAMENTO "
            . " FROM MONDOEDP.T265_CAUASSENZE CAUASSENZE "
            . " WHERE CAUASSENZE.CODICE= '" . $causale . "'";
    //echo $sql;exit;
    return $sql;
  }

  public function getQueryPersonalizzata($sqlpassato = "") {
    $query = ($sqlpassato ? $sqlpassato : $this->getSqlQueryPersonalizzata());
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();


    return $resultset;
  }

  protected function getSqlQueryPersonalizzata() {
    $sql = "SELECT * FROM MONDOEDP.T020_ORARI";
    /*
      $sql = "SELECT * "
      . " FROM MONDOEDP.T030_ANAGRAFICO ANAG, MONDOEDP.T430_STORICO STO "
      . " WHERE ANAG.PROGRESSIVO = STO.PROGRESSIVO "
      . " AND (TO_DATE('" . $this->data . "','YYYY-MM-DD') BETWEEN STO.DATADECORRENZA AND NVL(STO.DATAFINE, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) ";
      $sql = $sql . " AND ANAG.PROGRESSIVO = " . $this->progressivo;
     * 
     */
    //echo $sql;exit;
    return $sql;
  }

  public function getMesePrecedente() {

    $mese = ($this->mese == 1 ? 12 : $this->mese);
    $anno = ($this->mese == 1 ? $this->anno - 1 : $this->anno);

    $meseprecedente["mese"] = $mese;
    $meseprecedente["anno"] = $anno;

    return $meseprecedente;
  }

  public function getSaldo() {

    $oredovute = $this->getOreDovute(); //ore 
    $annoprec = $this->getAnnoPrec(); //minuti 
    $saldo_annuale = $this->getSaldoAnnuale(); //minuti 

    $saldo = $annoprec + $saldo_annuale; //minuti 
    $orelav = $this->getOreLav();

    $minuti = ($saldo % 60);
    $ore = ($saldo - $minuti) / 60;

    /* return ($recuperi_dip); */
    $saldo_dip = array("annoprec" => $annoprec,
        "saldo_annuale" => $saldo_annuale,
        "oredovute" => $oredovute,
        "orelav" => $orelav,
        "ore" => $ore,
        "minuti" => $minuti
    );


    return $saldo_dip;
  }

  protected function getOreDovute() {
    $query = $this->getSqlOreDovute();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();

    return $resultset;
  }

  protected function getSqlOreDovute() {

    $meseprecedente = $this->getMesePrecedente();

    $sql = "select to_char(data,'MONTH','NLS_DATE_LANGUAGE = italian') as MESE,to_char(data,'YYYY') as ANNO,
        debitoorario,rownum from MONDOEDP.t070_schedariepil where progressivo=" . $this->progressivo . "
        and extract (year from data)=" . $meseprecedente["anno"] . " and extract (month from data)= " . $meseprecedente["mese"];
    return $sql;
  }

  protected function getSaldoAnnuale() {
    $query = $this->getSqlSaldoAnnuale();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();

    foreach ($resultset as $row) {
      $ore = $row["SALDO"];
    }
    return $ore;
  }

  protected function getSqlSaldoAnnuale() {

    $meseprecedente = $this->getMesePrecedente();

    $anno_corretto = $meseprecedente["anno"];
    $matricola = $this->matricola;

    $sql = "select /*+ PUSH_SUBQ*/  (sum(numero)
        -(select sum(numero) from MONDOEDP.v070_riepiloghimensili where matricola=$matricola and anno=$anno_corretto and totalizzatore='DEBMM' and MESE<=" . $meseprecedente["mese"] . ") 
        -(select sum(numero) from MONDOEDP.v070_riepiloghimensili where matricola=$matricola and anno=$anno_corretto and totalizzatore='HHLIQ'  and MESE<=" . $meseprecedente["mese"] . ")) saldo
        from MONDOEDP.v070_riepiloghimensili
        where matricola=$matricola and anno=$anno_corretto and totalizzatore='HHLAV' and MESE<=" . $meseprecedente["mese"] . " group by totalizzatore";
    return $sql;
  }

  protected function getAnnoPrec() {
    $query = $this->getSqlAnnoPrec();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;
    $ore = 0;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    foreach ($resultset as $row) {
      $ore = $row["ORE"];
    }
    return $ore;
  }

  protected function getSqlAnnoPrec() {

    $meseprecedente = $this->getMesePrecedente();

    $anno_corretto = $meseprecedente["anno"];
    $progressivo = $this->progressivo;

    $sql = "select trunc(to_number(saldoorelav,'9999,9999.99'))*60
        + mod(to_number(saldoorelav,'9999,9999.99'),1)*100 as ORE
        from MONDOEDP.t130_residannoprec where progressivo=$progressivo
        and anno=$anno_corretto";
    return $sql;
  }

  public function getOreLav() {
    $query = $this->getSqlOreLav();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();

    return $resultset;
  }

  protected function getSqlOreLav() {

    $meseprecedente = $this->getMesePrecedente();
    $anno_corretto = $meseprecedente["anno"];
    $lastmese = $meseprecedente["mese"];
    $progressivo = $this->progressivo;

    $sql = "select sum(to_number(orelavorate)) as orelav
        from MONDOEDP.t071_schedafasce where progressivo=$progressivo
        and extract (year from data)=($anno_corretto) and extract (month from data)=($lastmese)";
    return $sql;
  }

  protected function getNomeGiorno($numero) {

    switch ($numero) {
      case 1:
        $risposta = "LUNEDI";
        break;
      case 2:
        $risposta = "MARTEDI";
        break;
      case 3:
        $risposta = "MERCOLEDI";
        break;
      case 4:
        $risposta = "GIOVEDI";
        break;
      case 5:
        $risposta = "VENERDI";
        break;
      case 6:
        $risposta = "SABATO";
        break;
      case 7:
        $risposta = "DOMENICA";
        break;
    }

    return $risposta;
  }

  public function getSaldoGiornaliero() {

    /* prendere dal db le pause con codice cauassenze */
    /* mettere in minutipausa */

    $minutieffettivi = 0;
    $oreeffettive = 0;
    $minutilavorati = 0;
    $minutipausapranzo = 0;
    $minuti_U = 0;
    $minuti_E = 0;

    $settimana = $this->settimana;
    $numerogiornosettimana = date("N", strtotime($this->data));
    $minutiingressoteorici = $this->ore2minuti($settimana[$numerogiornosettimana]["entrata"]);

    $query = $this->getSqlSaldoGiornaliero();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();

    $minuti = 0;
    $verso = "";

    /* prendere dalla tabella W1-PN l'orario di ingresso 
     *  select * from MONDOEDP.t021_fasceorari order by codice; codice = W1 codice_fascia = PN 
     * se il primo E della giornata è precedente a ORARIO 
     * allora il primo E della giornata = ORARIO 
     * se ci sono difficoltà l'urgenza è recuperare le 
     * pause con codice 10 
     */

    $primoingresso = true;

    foreach ($resultset as $row) {

      if ($row["VERSO"] == "E" && $primoingresso) {
        if ($row["MINUTI"] < $minutiingressoteorici) {
          $row["MINUTI"] = $minutiingressoteorici;
        }
        $primoingresso = false;
      }

      if ($row["VERSO"] == "E" && $verso == "U") {
        $minutipausapranzo = ($row["MINUTI"] - $minuti);
      }

      $verso = $row["VERSO"];
      $causale = $row["CAUSALE"];
      $minuti = $row["MINUTI"];

      if ($verso == 'E') {
        $minuti_E = $minuti;
      }
      if ($verso == 'U') {
        $minuti_U = $minuti;
        $minutilavorati = $minutilavorati + ($minuti_U - $minuti_E);
      }
    }

    // controllo se ci sono dei recuperi pausa mensa 

    $query = $this->getSqlGiustificativoGiornaliero();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();

    //var_dump($resultset);
    $giustificativo = 0;
    foreach ($resultset as $row) {
      $giustificativo += $row["ALLE"] - $row["DALLE"];
    }


    //calcolo pausa causale 10 ;
    $minutipausa = 0;
    $query = $this->getSqlSaldoGiornaliero(array(
        "causale" => "10",
        "confrontocausale" => "=",
        "ordine" => "data,to_char(T100.ora,'HH24MI')",
        "escludinulle" => true
    ));
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();


    foreach ($resultset as $row) {
      $verso = $row["VERSO"];
      $causale = $row["CAUSALE"];
      $minuti = $row["MINUTI"];

      if ($verso == 'E') {
        $minuti_E = $minuti;
      }
      if ($verso == 'U') {
        $minuti_U = $minuti;
        $minutipausa = $minutipausa + ($minuti_U - $minuti_E);
      }
    }

    $numerogiornosettimana = 0;
    if (strftime("%u", strtotime($this->data))) {
      $numerogiornosettimana = strftime("%u", strtotime($this->data));
    } else {
      $numerogiornosettimana = (strftime("%w", strtotime($this->data)) == 0 ? 7 : strftime("%w", strtotime($this->data)));
    }

    $settimana = $this->settimana;

    $minutilavoratiteorici = $this->ore2minuti($settimana[$numerogiornosettimana]["ore"]);
    $minutipausaobbligatoria = $this->ore2minuti($settimana[$numerogiornosettimana]["pausa"]);
    $pausainterna = $settimana[$numerogiornosettimana]["giornolungo"];

    if ($minutilavorati <= 360) {
      $pausainterna = false;
    }

    if ($pausainterna OR ( $minutilavorati > $minutilavoratiteorici)) {
      if ($minutipausapranzo == 0 &&
              $minutilavorati < ($minutilavoratiteorici + $minutipausaobbligatoria) &&
              ($minutilavorati > $minutilavoratiteorici))
        $minutilavorati = $minutilavoratiteorici;
      else
        $minutilavorati = $minutilavorati - (($minutipausaobbligatoria > $minutipausapranzo) ? ($minutipausaobbligatoria - $minutipausapranzo) : 0);
    }

    $minutilavorati = $minutilavorati - $minutipausa + $giustificativo;
    $minutieffettivi = $minutilavorati % 60;
    $oreeffettive = ($minutilavorati - $minutieffettivi) / 60;

    return array("ore" => $oreeffettive, "minuti" => $minutieffettivi);
  }

  protected function getSqlSaldoGiornaliero($parametri = array()) {

    $causale = isset($parametri["causale"]) ? $parametri["causale"] : "11";
    $confrontocausale = isset($parametri["confrontocausale"]) ? $parametri["confrontocausale"] : "!=";
    $ordine = isset($parametri["ordine"]) ? $parametri["ordine"] : "ora";
    $escludinulle = isset($parametri["escludinulle"]) ? $parametri["escludinulle"] : false;

    /* non basta che sia diverso da 11, deve essere diverso da tutte
     *  tranne la STRA 
     *  */

    $data = $this->data;
    $progressivo = $this->progressivo;

    $sql = "SELECT T100.verso,T100.causale,
    ((to_char(T100.ora,'HH24')*60)+to_char(T100.ora,'MI')) as minuti
    FROM MONDOEDP.T100_TIMBRATURE T100, MONDOEDP.T275_CAUPRESENZE T275
    WHERE
    T100.FLAG IN ('O','I') AND
    T100.CAUSALE = T275.CODICE(+) AND
    (T100.causale $confrontocausale '$causale'";

    if (!$escludinulle) {
      $sql .= " or T100.causale is null";
    }

    $sql .= ") AND
    T100.PROGRESSIVO = $progressivo AND
    to_char(T100.data,'yyyy-mm-dd')='$data'
    order by $ordine";

    //echo $sql . "<br>"; //exit; 
    /*
     * 
     * SELECT T100.verso,T100.causale, 
     * ((to_char(T100.ora,'HH24')*60)+to_char(T100.ora,'MI')) as minuti 
     * FROM MONDOEDP.T100_TIMBRATURE T100, 
     * MONDOEDP.T275_CAUPRESENZE T275 
     * WHERE T100.FLAG IN ('O','I') AND 
     * T100.CAUSALE = T275.CODICE(+) AND 
     * (T100.causale != '11' or T100.causale is null) AND 
     * T100.PROGRESSIVO = 40830 AND 
     * to_char(T100.data,'yyyy-mm-dd')='2014-11-3' order by ora
     */

    return $sql;
  }

  protected function getSqlGiustificativoGiornaliero($parametri = array()) {

    /* non basta che sia diverso da 11, deve essere diverso da tutte
     *  tranne la STRA 
     *  */

    $data = $this->data;
    $progressivo = $this->progressivo;


    $sql = "SELECT ((to_char(T040.DAORE,'HH24')*60)+to_char(T040.DAORE,'MI')) as DALLE,  "
            . " ((to_char(T040.AORE,'HH24')*60)+to_char(T040.AORE,'MI')) as ALLE "
            . "FROM MONDOEDP.T040_GIUSTIFICATIVI T040 "
            . "WHERE TO_CHAR(T040.DATA, 'YYYY-MM-DD') = '$data' "
            . "AND T040.PROGRESSIVO = $progressivo";


    //echo $sql . "<br>"; //exit; 
    /*
     * 
     * SELECT T100.verso,T100.causale, 
     * ((to_char(T100.ora,'HH24')*60)+to_char(T100.ora,'MI')) as minuti 
     * FROM MONDOEDP.T100_TIMBRATURE T100, 
     * MONDOEDP.T275_CAUPRESENZE T275 
     * WHERE T100.FLAG IN ('O','I') AND 
     * T100.CAUSALE = T275.CODICE(+) AND 
     * (T100.causale != '11' or T100.causale is null) AND 
     * T100.PROGRESSIVO = 40830 AND 
     * to_char(T100.data,'yyyy-mm-dd')='2014-11-3' order by ora
     */

    return $sql;
  }

  public function getTimbratureGiornaliere() {


    $query = $this->getSqlTimbratureGiornaliere();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $timbrature = array();
    foreach ($resultset as $row) {
      $timbrature[] = array("ora" => $row["ORA"], "verso" => $row["VERSO"], "causale" => $row["CAUSALE"], "descrizione" => $row["DESCRIZIONE"], "rilevatore" => $row["RILEVATORE"]);
    }
    return $timbrature;
  }

  protected function getSqlTimbratureGiornaliere() {

    $data = $this->data;
    $progressivo = $this->progressivo;

    $sql = "SELECT TO_CHAR(T100.ORA,'HH24:MI') ORA, T100.VERSO VERSO,T100.CAUSALE CAUSALE, T275.DESCRIZIONE DESCRIZIONE, T361.DESCRIZIONE RILEVATORE
                FROM MONDOEDP.T100_TIMBRATURE T100, MONDOEDP.T275_CAUPRESENZE T275,MONDOEDP.T361_OROLOGI T361
                WHERE T100.FLAG IN ('O','I') AND
                T100.CAUSALE = T275.CODICE(+) AND
                T100.RILEVATORE = T361.CODICE(+) AND
                T100.PROGRESSIVO = $progressivo AND
                TO_CHAR(T100.data,'YYYY-MM-DD')='$data'
                ORDER BY DATA,ORA";

    return $sql;
  }

  public function getDifferenzaGiornaliera() {

    /* testare l'assenza del giorno (se è un giorno di ferie o di permesso) */

    $saldogiornaliero = $this->getSaldoGiornaliero();
    $minutilavorati = (int) $saldogiornaliero["ore"] * 60 + (int) $saldogiornaliero["minuti"];
    $numerogiornosettimana = date("N", strtotime($this->data));
    $settimana = $this->settimana;

    $minutilavoratiteorici = $this->ore2minuti($settimana[$numerogiornosettimana]["ore"]);
    $differenza = $this->ore2minuti((int) $minutilavorati - (int) $minutilavoratiteorici);
    return ($differenza);
  }

  public function ore2minuti($stringa, $separatore = ".") {

    $trovato = stripos($stringa, $separatore);

    if ($trovato === false) {
      $risultato = (int) $stringa;
    } else {
      $risultato = (int) substr($stringa, $trovato + 1);
      $risultato += (int) substr($stringa, 0, $trovato) * 60;
    }

    return $risultato;
  }

  public function getDataChiusura() {


    $query = $this->getSqlDataChiusura();
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $timbrature = array();
    foreach ($resultset as $row) {
      $al = $row["AL"];
    }
    return $al;
  }

  protected function getSqlDataChiusura() {

    $data = $this->data;
    $progressivo = $this->progressivo;

    $sql = "SELECT TO_CHAR(T180.DAL,'YYYY-MM-DD') DAL, 
                TO_CHAR(T180.AL,'YYYY-MM-DD') AL, 
                T180.RIEPILOGO RIEPILOGO,
                T180.PROGRESSIVO PROGRESSIVO
                FROM MONDOEDP.T180_DATIBLOCCATI T180 
                WHERE 
                T180.RIEPILOGO = 'T040' AND
                T180.PROGRESSIVO = $progressivo 
                ";
    return $sql;
  }

  public function getStraordinari($oggi = false, $causale = "STRA") {


    $query = $this->getSqlStraordinari($oggi, $causale);
    $this->OracleConnection = $this->container->get("oracle_adreader");
    $connessione = $this->OracleConnection;

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $timbrature = array();
    foreach ($resultset as $row) {
      $timbrature[] = array(
          "giorno" => $row["GIORNO"],
          "ora" => $row["ORA"],
          "verso" => $row["VERSO"],
          "causale" => $row["CAUSALE"]
      );
    }
    return $timbrature;
  }

  protected function getSqlStraordinari($oggi = false, $causale = "STRA") {
    $sql = "SELECT 
      TO_CHAR(T100.data,'YYYY-MM-DD') as giorno, 
      T100.verso,
      T100.causale, 
      (to_char(T100.ora,'HH24:MI')) as ora
    FROM 
      MONDOEDP.T100_TIMBRATURE T100
    WHERE
      T100.causale = '" . $causale . "' 
    AND
      T100.PROGRESSIVO = $this->progressivo 
    AND
      to_char(T100.data,'yyyy-mm-dd')" . ($oggi ? "=" : ">=") . "'$this->data'";

    return $sql;
  }

}
