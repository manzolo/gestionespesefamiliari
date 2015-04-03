<?php

namespace Fi\PersonaleBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;
use Fi\PersonaleBundle\DependencyInjection\fiPersonale;

/**
 * Classe per gestire la struttura del Comune di Firenze
 *
 * @author Andrea Manzi
 */
class fiOrganigramma {

    /**
     *
     * @param string $matricola
     * @param string $data
     * @return string
     */
    private $matricola;
    private $data;
    private $esiste;

    public function __construct($container, $matricola, $data = null) {
        $this->matricola = $matricola;
        if ($data) {
            $this->data = $data;
        } else {
            $this->data = date('Y-m-d');
        }

        $this->container = $container;
    }

    private function getUoFiglieSql($uo) {
        $query = "select DISTINCT uo.id_elemento,
                    uo.id_unita_padre,
                    uo.progr_unita_organizzativa,
                    /* DESCRIZIONE UO */
                    /*lpad(' ', 10 * (level - 1)) ||*/
                    (select anag.descrizione
                       from so4.anagrafe_unita_organizzative anag
                      where anag.progr_unita_organizzativa = uo.progr_unita_organizzativa
                      and rownum < 2
                        and TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN anag.DAL AND NVL(anag.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) DESCRIZIONE_UO,
                   /*MATRICOLA RESPONSABILE*/
                   (select co.ci nominativo from so4.componenti co, so4.attributi_componente attr, so4.tipi_incarico ti
                            where co.id_componente = attr.id_componente
                            and attr.incarico = ti.incarico
                            /*and co.stato = 'D'*/
                            and (TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN attr.DAL AND
                                   NVL(attr.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                            and (TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN co.DAL AND
                                   NVL(co.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                            and ti.responsabile = 'SI'
                            and co.progr_unita_organizzativa = uo.progr_unita_organizzativa
                            and rownum < 2
                            ) MATRICOLA,
                      /*NOME RESPONSABILE*/
                      /*lpad(' ', 10 * (level - 1)) ||*/
                      (select p00.gp4_rain.GET_NOMINATIVO(co.ci) nominativo from so4.componenti co, so4.attributi_componente attr, so4.tipi_incarico ti
                            where co.id_componente = attr.id_componente
                            and attr.incarico = ti.incarico
                            /*and co.stato = 'D'*/
                            and (TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN attr.DAL AND
                                   NVL(attr.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                            and (TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN co.DAL AND
                                   NVL(co.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                            and ti.responsabile = 'SI'
                            and co.progr_unita_organizzativa = uo.progr_unita_organizzativa
                            and rownum < 2
                            ) RESPONSABILE,
                          level LIVELLO
                     from so4.unita_organizzative uo
                    WHERE TO_DATE('" . $this->data . "', 'YYYY-MM-DD') BETWEEN uo.DAL AND
                          NVL(uo.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))
                          and level <=2
                   start with uo.progr_unita_organizzativa = " . $uo . "
                   connect by prior uo.progr_unita_organizzativa = id_unita_padre
                   ORDER BY level";
        //echo $query;exit;
        return $query;
    }

    public function getPersonaleCompetenza() {
        $personalediretto = array();
        $fiPers = new fiPersonale($this->container, $this->matricola);
        $uos = $fiPers->getUnitaOrganizzativa();
        foreach ($uos as $uo) {
            $personalediretto = array_merge($personalediretto, $this->getPersonaleUo($uo["progr_unita_organizzativa"]));
        }
        return $personalediretto;
    }

    public function getPersonaleTotaleCompetenza() {
        $dipe = new fiOrganigramma($this->container, $this->matricola);
        $dipendenti = $dipe->getPersonaleCompetenza();
        $dipendentitotali = array();
        foreach ($dipendenti as $dipendente) {
            $dipendentitotali[] = $dipendente;
            if ($dipendente["is_responsabile"]) {
                $respo = new fiOrganigramma($this->container, $dipendente["matricola"]);
                $dipendentiresponsabile = $respo->getPersonaleTotaleCompetenza();
                $dipendentitotali = array_merge($dipendentitotali, $dipendentiresponsabile);
            }
        }
        return $dipendentitotali;
    }

    /* private function file_put_contents($msg) {
      $f = @fopen("/src/php/jfestere/logs.log", 'a');
      if (!$f) {
      return false;
      } else {
      $bytes = fwrite($f, $msg);
      fclose($f);
      return $bytes;
      }
      } */

    private function getPersonaleUo($uo) {
        $personalediretto = array();
        $query = $this->getUoFiglieSql($uo);

        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        foreach ($resultset as $row) {
            if ((int) $row["LIVELLO"] > 1) {
                if ($row["MATRICOLA"] == $this->matricola) {
                    continue;
                }
                if ($row["MATRICOLA"]) {
                    //$this->file_put_contents($row["MATRICOLA"] . "\n");
                    $personalediretto[] = array("matricola" => $row["MATRICOLA"], "nominativo" => $row["RESPONSABILE"], "uo" => $row["PROGR_UNITA_ORGANIZZATIVA"], "uo_padre" => $row["ID_UNITA_PADRE"], "is_responsabile" => true);
                } else {
                    $personalediretto = array_merge($personalediretto, $this->getPersonaleUo($row["PROGR_UNITA_ORGANIZZATIVA"]));
                }
            } else {
                $personalediretto = array_merge($personalediretto, $this->getDipendentiUo($row["PROGR_UNITA_ORGANIZZATIVA"]));
            }
        }
        return $personalediretto;
    }

    private function getDipendentiUo($uo) {
        $dipendenti = array();
        $query = $this->getDipendentiUoSql($uo);

        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;

        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        foreach ($resultset as $row) {
            if ($row["MATRICOLA"] == $this->matricola) {
                continue;
            }
            if ($row["MATRICOLA"]) {
                $dipendenti[] = array("matricola" => $row["MATRICOLA"], "nominativo" => $row["NOMINATIVO"], "uo" => $row["PROGR_UNITA_ORGANIZZATIVA"], "uo_padre" => $row["ID_UNITA_PADRE"], "is_responsabile" => ($row["IS_RESPONSABILE"] == 'SI' ? true : false));
            }
        }
        return $dipendenti;
    }

    private function getDipendentiUoSql($uo) {
        $query = "select CO.id_componente id_componente, CO.ci matricola,
                         p00.gp4_rain.GET_NOMINATIVO(co.ci) nominativo,
                         CO.progr_unita_organizzativa progr_unita_organizzativa,
                         (select uo.id_unita_padre id_unita_padre
                        from so4.unita_organizzative uo
                       where uo.progr_unita_organizzativa = CO.progr_unita_organizzativa
                       AND TO_DATE('$this->data', 'YYYY-MM-DD') BETWEEN uo.DAL AND NVL(uo.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY')))
                        id_unita_padre,
                         (select ti.responsabile responsabile
                    from so4.tipi_incarico ti,
                    SO4.attributi_componente attcmp,
                    SO4.componenti cmp                   
                   where ti.incarico = attcmp.incarico
                     and attcmp.id_componente = co.id_componente
                     and CMP.progr_unita_organizzativa =  CO.progr_unita_organizzativa
                     and CMP.ci = CO.ci
                     AND attcmp.id_componente = CO.id_componente
                     AND TO_DATE('$this->data', 'YYYY-MM-DD') BETWEEN attcmp.DAL AND NVL(attcmp.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))
                     AND cmp.id_componente = CO.id_componente
                     AND TO_DATE('$this->data', 'YYYY-MM-DD') BETWEEN cmp.DAL AND NVL(cmp.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))) IS_RESPONSABILE
                  from so4.componenti CO
                  where  1=1 
                  AND CO.progr_unita_organizzativa = " . $uo .
                " AND TO_DATE('$this->data','YYYY-MM-DD') BETWEEN CO.DAL AND NVL(CO.AL,TO_DATE('31/12/9999','DD/MM/YYYY'))";
        ;
        return $query;
    }

    /**
     * Metodo che ritorna se il dipendente è un responsabile
     * @return bool Se è (true) o meno (false) responsabile
     */
    function isResponsabile($uo) {
        /* di default è no */
        $responsabilesn = "NO";

        $this->OracleConnection = $this->container->get("oracle_sigru");
        $connessione = $this->OracleConnection;
        
        $query = "select ti.responsabile responsabile
                    from so4.tipi_incarico ti,
                    SO4.attributi_componente attcmp,
                    SO4.componenti cmp,
                    SO4.componenti co
                   where ti.incarico = attcmp.incarico
                     AND attcmp.id_componente = co.id_componente
                     AND CMP.progr_unita_organizzativa =  CO.progr_unita_organizzativa
                     AND CMP.ci = CO.ci
                     AND CO.ci = $this->matricola
                     AND attcmp.id_componente = CO.id_componente
                     AND TO_DATE('$this->data', 'YYYY-MM-DD') BETWEEN attcmp.DAL AND NVL(attcmp.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))
                     AND cmp.id_componente = CO.id_componente
                     AND TO_DATE('$this->data', 'YYYY-MM-DD') BETWEEN cmp.DAL AND NVL(cmp.AL, TO_DATE('31/12/9999', 'DD/MM/YYYY'))
                     AND CO.progr_unita_organizzativa = " . $uo . "
                     AND TO_DATE('$this->data','YYYY-MM-DD') BETWEEN CO.DAL AND NVL(CO.AL,TO_DATE('31/12/9999','DD/MM/YYYY'))";


        $connessione->executeSelectQuery($query);
        $resultset = $connessione->getResultset();
        foreach ($resultset as $row) {
            $responsabilesn = $row["RESPONSABILE"];
        }
        $responsabile = ($responsabilesn == 'SI' ? true : false);
        return $responsabile;
    }

    public function getMatricola() {
        return $this->matricola;
    }

    public function getData() {
        return $this->data;
    }

    /**
     * La funzione cerca un valore $elem nell'array multidimensionale $array all'interno di ogni elemento con chiave $key di ogni riga di array
     * e restituisce l'indice 
     *
     * @param $elem Oggetto da cercare
     * @param $array Array nel quale cercare
     * @param $key Nome della chiave nella quale cercare $elem
     * @return Mixed False se non trovato l'elemento, altrimenti l'indice in cui si è trovato il valore
     */
    private function in_multiarray($elem, $array, $key) {

        foreach ($array as $indice => $value) {
            if (!is_array($value)) {
                return false;
            }
            if (array_key_exists($key, $value)) {
                foreach ($value as $colonna) {
                    if ($colonna === $elem) {
                        return $indice;
                    }
                }
            } else {
                return false;
            }
        }
        return false;
    }

}
