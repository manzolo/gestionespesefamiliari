<?php

namespace Fi\SipoBundle\DependencyInjection;

use Fi\OracleBundle\DependencyInjection\fiQuery;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FiSipo {

  /**
   * {@inheritdoc}
   */
  private $schemasipo;
  private $container;

  public function __construct($container) {
    $this->container = $container;
    $this->schemasipo = $container->getParameter("schemasipo");
  }


  function getSqlXaternita($codicefiscale) {
    $query = "select b.PER_KEY_MATRICOLA AS MATRICOLA1, 
       b.PER_KEY_CODICE_FIS AS COD_FISC1,
   SUBSTR (b.PER_COG_NOM, INSTR(b.PER_COG_NOM, '/') + 1)   AS NOME1,
   SUBSTR(b.PER_COG_NOM,  1, INSTR(b.PER_COG_NOM, '/') -1) AS COGNOME1,
    c.PER_KEY_MATRICOLA as MATRICOLA2,
    c.PER_KEY_CODICE_FIS AS COD_FISC2,
   SUBSTR (c.PER_COG_NOM, INSTR(c.PER_COG_NOM, '/') + 1)   AS NOME2,
   SUBSTR(c.PER_COG_NOM,  1, INSTR(c.PER_COG_NOM, '/') -1) AS COGNOME2
    from " . $this->schemasipo . ".potdaper a, " . $this->schemasipo . ".potdaper b, " . $this->schemasipo . ".potdaper c
   WHERE
     a.PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
   AND
    trim(SUBSTR(A.PER_COG_NOM,  1, INSTR(A.PER_COG_NOM, '/') -1)) || '/' ||
    trim(a.PER_COG_PAT) = b.PER_COG_NOM
   and a.PER_KEY_COD_FAM = b.PER_KEY_COD_FAM
   AND
      a.PER_COG_MAT = c.PER_COG_NOM
   and a.PER_KEY_COD_FAM = c.PER_KEY_COD_FAM";
    return $query;
  }

  function sqlPersonaMatricola($matricola) {
    $query = "SELECT  PER_KEY_CODICE_FIS       AS CODICE_FISCALE,
        PER_KEY_MATRICOLA                      AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
   
        PER_KEY_SESSO                                       AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)                  AS DATA_NASCITA,
         PER_NAS_COMUNE                                     AS COD_LUOGO_NASCITA,
         CMN_NOME                                           AS LUOGO_NASCITA,  
         CMN_PROVINCIA                                      AS PROVINCIA_NASCITA,
         PER_KEY_COD_FAM                                    AS CODICE_FAMIGLIA,
         PER_CITTADINANZA                                   AS CITTADINANZA,
         CNAZ_COD_CITT                                      AS ISTAT_CITT,
         CNAZ_NOME_BREVE                                    AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                                       AS DESC_CITTADINANZA,
         TOP_KEY_TIPO_AREA                                  AS TIPOVIA,
         PER_KEY_COD_VIA                                    AS CODVIA,
         TOP_DESCR_POSTALE                                  AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                                  AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                                  AS ESPONENTE,
       PER_KEY_NUM_INT                                      AS INTERNO,
       PER_STATO_CIVILE                                     AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
						WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE PER_KEY_ENTE = '01'
AND PER_KEY_MATRICOLA = $matricola
AND PER_KEY_COD_VIA    = TOP_KEY_CODICE(+)
AND PER_NAS_COMUNE = CMN_KEY_COMUNE(+)
AND PER_CITTADINANZA = CNAZ_SIGLA(+)
AND PER_TIT_STUDIO = STUD_CHIAVE(+)
AND PER_PROFESSIONE = PRF_CHIAVE(+)";
    return $query;
  }

  function sqlPersonaUno($codicefiscale) {
    $query = "SELECT  PER_KEY_CODICE_FIS    AS   CODICE_FISCALE,
        PER_KEY_MATRICOLA     AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
         PER_KEY_SESSO        AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '|| SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '|| SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)   AS DATA_NASCITA,
         PER_NAS_COMUNE                      AS COD_LUOGO_NASCITA,
         CMN_NOME                            AS LUOGO_NASCITA,  
         CMN_PROVINCIA                       AS PROVINCIA_NASCITA,
         PER_CITTADINANZA                    AS CITTADINANZA,
         CNAZ_COD_CITT                       AS ISTAT_CITT,
         CNAZ_NOME_BREVE                     AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                        AS DESC_CITTADINANZA,
         PER_KEY_COD_FAM                     AS CODICE_FAMIGLIA,
         TOP_KEY_TIPO_AREA                   AS TIPOVIA,
         PER_KEY_COD_VIA                     AS CODVIA,
         TOP_DESCR_POSTALE                   AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                   AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                   AS ESPONENTE,
       PER_KEY_NUM_INT                       AS INTERNO,
       PER_STATO_CIVILE                      AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
   		WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE PER_FLAG_STATUS1 IN ('0', '1')
AND PER_KEY_CODICE_FIS = ('" . $codicefiscale . "')
AND PER_KEY_COD_VIA  = TOP_KEY_CODICE
AND PER_NAS_COMUNE   = CMN_KEY_COMUNE
AND PER_CITTADINANZA = CNAZ_SIGLA
AND PER_TIT_STUDIO   = STUD_CHIAVE
AND PER_PROFESSIONE = PRF_CHIAVE";

    return $query;
  }

  function sqlPersonaTre($codicefiscale) {
    $query = "SELECT  PER_KEY_CODICE_FIS                                  AS CODICE_FISCALE,
        PER_KEY_MATRICOLA                                   AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
   
        PER_KEY_SESSO                                       AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)                  AS DATA_NASCITA,
         PER_NAS_COMUNE                                     AS COD_LUOGO_NASCITA,
         CMN_NOME                                           AS LUOGO_NASCITA,  
         CMN_PROVINCIA                                      AS PROVINCIA_NASCITA,
         PER_CITTADINANZA                                   AS CITTADINANZA,
         CNAZ_COD_CITT                                      AS ISTAT_CITT,
         CNAZ_NOME_BREVE                                    AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                                       AS DESC_CITTADINANZA,
         PER_KEY_COD_FAM                                    AS CODICE_FAMIGLIA,
         TOP_KEY_TIPO_AREA                                  AS TIPOVIA,
         PER_KEY_COD_VIA                                    AS CODVIA,
         TOP_DESCR_POSTALE                                  AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                                  AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                                  AS ESPONENTE,
       PER_KEY_NUM_INT                                      AS INTERNO,
       PER_STATO_CIVILE                                     AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
						WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE PER_FLAG_STATUS1 in ('7')
AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
AND PER_KEY_COD_VIA    = TOP_KEY_CODICE
AND PER_NAS_COMUNE = CMN_KEY_COMUNE
AND PER_CITTADINANZA = CNAZ_SIGLA
AND PER_TIT_STUDIO = STUD_CHIAVE
AND PER_PROFESSIONE = PRF_CHIAVE";

    return $query;
  }

  function sqlPersonaTreMultipli($codicefiscale) {
    $query = "SELECT  PER_KEY_CODICE_FIS                                  AS CODICE_FISCALE,
        PER_KEY_MATRICOLA                                   AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
   
        PER_KEY_SESSO                                       AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)                  AS DATA_NASCITA,
         PER_NAS_COMUNE                                     AS COD_LUOGO_NASCITA,
         CMN_NOME                                           AS LUOGO_NASCITA,  
         CMN_PROVINCIA                                      AS PROVINCIA_NASCITA,
         PER_CITTADINANZA                                   AS CITTADINANZA,
         CNAZ_COD_CITT                                      AS ISTAT_CITT,
         CNAZ_NOME_BREVE                                    AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                                       AS DESC_CITTADINANZA,
         PER_KEY_COD_FAM                                    AS CODICE_FAMIGLIA,
         TOP_KEY_TIPO_AREA                                  AS TIPOVIA,
         PER_KEY_COD_VIA                                    AS CODVIA,
         TOP_DESCR_POSTALE                                  AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                                  AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                                  AS ESPONENTE,
       PER_KEY_NUM_INT                                      AS INTERNO,
       PER_STATO_CIVILE                                     AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
						WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE PER_FLAG_STATUS1 in ('7')
AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
AND PER_KEY_COD_VIA    = TOP_KEY_CODICE
AND PER_NAS_COMUNE = CMN_KEY_COMUNE
AND PER_CITTADINANZA = CNAZ_SIGLA
AND PER_TIT_STUDIO = STUD_CHIAVE
AND PER_PROFESSIONE = PRF_CHIAVE
AND PER_KEY_MATRICOLA = (SELECT MAX(PER_KEY_MATRICOLA) FROM " . $this->schemasipo . ".POTDAPER 
                     WHERE PER_KEY_ENTE = '01'   
     		            AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
	                   AND NOT PER_FLAG_STATUS1 
                           IN ('9' )
)";

    return $query;
  }

  function sqlPersonaDue($codicefiscale) {
    $query = "SELECT  PER_KEY_CODICE_FIS                                  AS CODICE_FISCALE,
        PER_KEY_MATRICOLA                                   AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
   
        PER_KEY_SESSO                                       AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)                  AS DATA_NASCITA,
         PER_NAS_COMUNE                                     AS COD_LUOGO_NASCITA,
         CMN_NOME                                           AS LUOGO_NASCITA,  
         CMN_PROVINCIA                                      AS PROVINCIA_NASCITA,
         PER_CITTADINANZA                                   AS CITTADINANZA,
         CNAZ_COD_CITT                                      AS ISTAT_CITT,
         CNAZ_NOME_BREVE                                    AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                                       AS DESC_CITTADINANZA,
         PER_KEY_COD_FAM                                    AS CODICE_FAMIGLIA,
         TOP_KEY_TIPO_AREA                                  AS TIPOVIA,
         PER_KEY_COD_VIA                                    AS CODVIA,
         TOP_DESCR_POSTALE                                  AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                                  AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                                  AS ESPONENTE,
       PER_KEY_NUM_INT                                      AS INTERNO,
       PER_STATO_CIVILE                                     AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
						WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE PER_FLAG_STATUS1 in ('3', '4', '5')
AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
AND PER_KEY_COD_VIA    = TOP_KEY_CODICE
AND PER_NAS_COMUNE = CMN_KEY_COMUNE
AND PER_CITTADINANZA = CNAZ_SIGLA
AND PER_TIT_STUDIO = STUD_CHIAVE
AND PER_PROFESSIONE = PRF_CHIAVE";

    return $query;
  }

  function sqlPersonaDueMultipli($codicefiscale) {
    $query = "SELECT  PER_KEY_CODICE_FIS                                  AS CODICE_FISCALE,
        PER_KEY_MATRICOLA                                   AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
   
        PER_KEY_SESSO                                       AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)                  AS DATA_NASCITA,
         PER_NAS_COMUNE                                     AS COD_LUOGO_NASCITA,
         CMN_NOME                                           AS LUOGO_NASCITA,  
         CMN_PROVINCIA                                      AS PROVINCIA_NASCITA,
         PER_CITTADINANZA                                   AS CITTADINANZA,
         CNAZ_COD_CITT                                      AS ISTAT_CITT,
         CNAZ_NOME_BREVE                                    AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                                       AS DESC_CITTADINANZA,
         PER_KEY_COD_FAM                                    AS CODICE_FAMIGLIA,
         TOP_KEY_TIPO_AREA                                  AS TIPOVIA,
         PER_KEY_COD_VIA                                    AS CODVIA,
         TOP_DESCR_POSTALE                                  AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                                  AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                                  AS ESPONENTE,
       PER_KEY_NUM_INT                                      AS INTERNO,
       PER_STATO_CIVILE                                     AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
						WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE PER_FLAG_STATUS1 in ('3', '4', '5')
AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
AND PER_KEY_COD_VIA    = TOP_KEY_CODICE
AND PER_NAS_COMUNE = CMN_KEY_COMUNE
AND PER_CITTADINANZA = CNAZ_SIGLA
AND PER_TIT_STUDIO = STUD_CHIAVE
AND PER_PROFESSIONE = PRF_CHIAVE
AND PER_KEY_MATRICOLA = (SELECT MAX(PER_KEY_MATRICOLA) FROM " . $this->schemasipo . ".POTDAPER 
                     WHERE PER_KEY_ENTE = '01'   
     		            AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
	                   AND NOT PER_FLAG_STATUS1 
                           IN ('9' )
)";

    return $query;
  }

  function sqlPersonaQuattroMultipli($codicefiscale) {
    $query = "SELECT  PER_KEY_CODICE_FIS                                  AS CODICE_FISCALE,
        PER_KEY_MATRICOLA                                   AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
   
        PER_KEY_SESSO                                       AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)                  AS DATA_NASCITA,
         PER_NAS_COMUNE                                     AS COD_LUOGO_NASCITA,
         CMN_NOME                                           AS LUOGO_NASCITA,  
         CMN_PROVINCIA                                      AS PROVINCIA_NASCITA,
         PER_CITTADINANZA                                   AS CITTADINANZA,
         CNAZ_COD_CITT                                      AS ISTAT_CITT,
         CNAZ_NOME_BREVE                                    AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                                       AS DESC_CITTADINANZA,
         PER_KEY_COD_FAM                                    AS CODICE_FAMIGLIA,
         TOP_KEY_TIPO_AREA                                  AS TIPOVIA,
         PER_KEY_COD_VIA                                    AS CODVIA,
         TOP_DESCR_POSTALE                                  AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                                  AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                                  AS ESPONENTE,
       PER_KEY_NUM_INT                                      AS INTERNO,
       PER_STATO_CIVILE                                     AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
						WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE NOT PER_FLAG_STATUS1 IN ('0', '1', '9')
and not PER_FLAG_STATUS1 || PER_FLAG_STATUS2 in 
  ('26', '29', '2M', '2N', '2Z', '8D')
AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
AND PER_KEY_COD_VIA    = TOP_KEY_CODICE
AND PER_NAS_COMUNE = CMN_KEY_COMUNE
AND PER_CITTADINANZA = CNAZ_SIGLA
AND PER_TIT_STUDIO = STUD_CHIAVE
AND PER_PROFESSIONE = PRF_CHIAVE
AND PER_KEY_MATRICOLA = (SELECT MAX(PER_KEY_MATRICOLA) FROM " . $this->schemasipo . ".POTDAPER 
                     WHERE PER_KEY_ENTE = '01'   
     		            AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
	                   AND NOT PER_FLAG_STATUS1 
                           IN ( '0' , '1' , '9' )
and not PER_FLAG_STATUS1 || PER_FLAG_STATUS2 in 
  ('26', '29', '2M', '2N', '2Z', '8D')
)";

    return $query;
  }

  function sqlPersonaQuattro($codicefiscale) {
    $query = "SELECT  PER_KEY_CODICE_FIS                                  AS CODICE_FISCALE,
        PER_KEY_MATRICOLA                                   AS MATRICOLA,
   SUBSTR (PER_COG_NOM, INSTR(PER_COG_NOM, '/') + 1)   AS NOME,
   SUBSTR(PER_COG_NOM,  1, INSTR(PER_COG_NOM, '/') -1) AS COGNOME,
   
        PER_KEY_SESSO                                       AS SESSO,
         SUBSTR(PER_KEY_NAS_DATA ,  7 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  5 , 2) ||' '||                 
         SUBSTR(PER_KEY_NAS_DATA ,  1 , 4)                  AS DATA_NASCITA,
         PER_NAS_COMUNE                                     AS COD_LUOGO_NASCITA,
         CMN_NOME                                           AS LUOGO_NASCITA,  
         CMN_PROVINCIA                                      AS PROVINCIA_NASCITA,
         PER_CITTADINANZA                                   AS CITTADINANZA,
         CNAZ_COD_CITT                                      AS ISTAT_CITT,
         CNAZ_NOME_BREVE                                    AS DESC_CITT_BREVE,
         CNAZ_ESONIMO                                       AS DESC_CITTADINANZA,
         PER_KEY_COD_FAM                                    AS CODICE_FAMIGLIA, 
         TOP_KEY_TIPO_AREA                                  AS TIPOVIA,
         PER_KEY_COD_VIA                                    AS CODVIA,
         TOP_DESCR_POSTALE                                  AS DESVIA,                                                
       case when PER_KEY_NUM_CIV > 5000                                 
               then per_key_num_civ - 5000                                 
               else PER_KEY_NUM_CIV                                        
       end                                                  AS NUMCIV,                                                      
       case when PER_KEY_NUM_CIV > 5000                                 
               then 'rosso'                                                
               else PER_KEY_esp_CIV                                        
       end                                                  AS ESPONENTE,
       PER_KEY_NUM_INT                                      AS INTERNO,
       PER_STATO_CIVILE                                     AS STATO_CIVILE,
       CASE WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NM'
         THEN  'Celibe'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'NF'
         THEN  'Nubile'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CM'
         THEN  'Coniugato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'CF'
         THEN  'Coniugata'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VM'
         THEN  'Vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'VF'
         THEN  'Vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PM'
         THEN  'Presunto vedovo'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'PF'
         THEN  'Presunta vedova'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DM'
         THEN  'Divorziato'
            WHEN PER_STATO_CIVILE || PER_KEY_SESSO = 'DF'
         THEN  'Divorziata'
						WHEN PER_STATO_CIVILE = 'L'
         THEN  'Libero vincoli'
         ELSE  'Non trovato'
       END AS DES_STATO_CIVILE,
       STUD_CHIAVE AS COD_T_STUD,
       STUD_DESCRIZIONE AS DES_T_STUD,
       STUD_LIVELLO AS LIV_T_STUD,
       PRF_CHIAVE AS COD_PROF,
       PRF_DESCRIZIONE_M AS DES_PROF,
       case WHEN prf_ramo_attivita = ' '
              THEN  'NON SPECIFICATO'
            WHEN prf_ramo_attivita = '0'
              THEN  'AGRICOLTURA    '
            WHEN prf_ramo_attivita = '1'
              THEN  'INDUSTRIA      '
            WHEN prf_ramo_attivita = '2'
              THEN  'COMMERCIO/P.E. '
            WHEN prf_ramo_attivita = '3'
              THEN  'P.A. E SERVIZI '
            WHEN prf_ramo_attivita = '9'
              THEN  'COND. NON PROF.'
              ELSE  'NON TROVATO    '
       END AS RAMO_ATTIVITA,
       case WHEN prf_POSIZ_PROF = ' '
              THEN  'NON INDICATO'
            WHEN prf_POSIZ_PROF = '0'
              THEN  'IMPR./LIB.PROF.'
            WHEN prf_POSIZ_PROF = '1'
              THEN  'IMPIEGATO      '
            WHEN prf_POSIZ_PROF = '2'
              THEN  'IN PROPRIO     '
            WHEN prf_POSIZ_PROF = '3'
              THEN  'COADIUVANTE    '
            WHEN prf_POSIZ_PROF = '4'
              THEN  'DIPENDENTE     '
            WHEN prf_POSIZ_PROF = '6'
              THEN  'CASALINGA      '
            WHEN prf_POSIZ_PROF = '7'
              THEN  'STUDENTE       '
            WHEN prf_POSIZ_PROF = '8'
              THEN  'ATTESA 1 OCCUP.'
            WHEN prf_POSIZ_PROF = '9'
              THEN  'ALTRE NON PROF.'
              ELSE  'NON TROVATO    '
       END AS POSIZ_PROF
          
FROM " . $this->schemasipo . ".POTDAPER, " . $this->schemasipo . ".POTDTTOP, " . $this->schemasipo . ".POTDCCOM, 
     " . $this->schemasipo . ".POTDCNAZ, " . $this->schemasipo . ".POTDSTUD, " . $this->schemasipo . ".POTDPROF
WHERE NOT PER_FLAG_STATUS1 IN ('0', '1', '9')
AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'
AND PER_KEY_COD_VIA    = TOP_KEY_CODICE
AND PER_NAS_COMUNE = CMN_KEY_COMUNE
AND PER_CITTADINANZA = CNAZ_SIGLA
AND PER_TIT_STUDIO = STUD_CHIAVE
AND PER_PROFESSIONE = PRF_CHIAVE
";

    return $query;
  }

  function getSqlPersona($codicefiscale, $giro, $escludimultipli = 1) {
    switch ($giro) {
      case 1:
        $risposta = $this->sqlPersonaUno($codicefiscale);
        break;
      case 2:
        $risposta = ($escludimultipli ? $this->sqlPersonaDue($codicefiscale) : $this->sqlPersonaDueMultipli($codicefiscale));
        break;
      case 3:
        $risposta = ($escludimultipli ? $this->sqlPersonaTre($codicefiscale) : $this->sqlPersonaTreMultipli($codicefiscale));
        break;
      case 4:
        $risposta = ($escludimultipli ? $this->sqlPersonaQuattro($codicefiscale) : $this->sqlPersonaQuattroMultipli($codicefiscale));
        break;

      default:
        break;
    }
    return $risposta;
  }

  function getPersonaMatricola($matricola) {
    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $risultato = array();

    $query = $this->sqlPersonaMatricola($matricola);
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();


    if (!$resultset) {
      $risultato = array('trovato' => 0);
    } else {
      foreach ($resultset as $row) {
        $risultato = array(
            'matricola' => $row["MATRICOLA"],
            'cognome' => $row["COGNOME"],
            'nome' => $row["NOME"],
            'codicefiscale' => $row["CODICE_FISCALE"],
            'sesso' => $row["SESSO"],
            'datanascita' => $row["DATA_NASCITA"],
            'codluogonascita' => $row["COD_LUOGO_NASCITA"],
            'luogonascita' => $row["LUOGO_NASCITA"],
            'provincianascita' => $row["PROVINCIA_NASCITA"],
            'cittadinanza' => $row["CITTADINANZA"],
            'istatcitt' => $row["ISTAT_CITT"],
            'descrcittbreve' => $row["DESC_CITT_BREVE"],
            'desccittadinanza' => $row["DESC_CITTADINANZA"],
            'codicefamiglia' => $row["CODICE_FAMIGLIA"],
            'tipovia' => $row["TIPOVIA"],
            'codvia' => $row["CODVIA"],
            'desvia' => $row["DESVIA"],
            'numciv' => $row["NUMCIV"],
            'esponente' => $row["ESPONENTE"],
            'interno' => $row["INTERNO"],
            'statocivile' => $row["STATO_CIVILE"],
            'descrizionestatocivile' => $row["DES_STATO_CIVILE"],
            'codicetitolostudio' => $row["COD_T_STUD"],
            'descrizionetitolostudio' => $row["DES_T_STUD"],
            'livellotitolostudio' => $row["LIV_T_STUD"],
            'codiceprofessione' => $row["COD_PROF"],
            'descizioneprofessione' => $row["DES_PROF"],
            'ramoattivita' => $row["RAMO_ATTIVITA"],
            'posizioneprofessionale' => $row["POSIZ_PROF"]
        );
        break;
      }
    }

    return $risultato;
  }

  function getPersona($codicefiscale) {
    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $risultato = array();

    $giro = 1;

    do {
      switch ($giro) {
        case 1:
          $query = $this->getSqlPersona($codicefiscale, $giro);
          $connessione->executeSelectQuery($query);
          $resultset = $connessione->getResultset();

          break;
        case 2:
        case 3:
        case 4:
          $query = $this->getSqlPersona($codicefiscale, $giro);
          $connessione->executeSelectQuery($query);
          $resultset = $connessione->getResultset();
          if (count($resultset) > 1) {
            unset($resultset);
            $query = $this->getSqlPersona($codicefiscale, $giro, 1);
            $connessione->executeSelectQuery($query);
            $resultset = $connessione->getResultset();
          }
          break;
        default:
          break;
      }
      $giro++;
    } while ($giro <= 4 && !$resultset);

    if (!$resultset) {
      $risultato = array('trovato' => 0);
    } else {
      foreach ($resultset as $row) {
        $risultato = array(
            'matricola' => $row["MATRICOLA"],
            'cognome' => $row["COGNOME"],
            'nome' => $row["NOME"],
            'codicefiscale' => $row["CODICE_FISCALE"],
            'sesso' => $row["SESSO"],
            'datanascita' => $row["DATA_NASCITA"],
            'codluogonascita' => $row["COD_LUOGO_NASCITA"],
            'luogonascita' => $row["LUOGO_NASCITA"],
            'provincianascita' => $row["PROVINCIA_NASCITA"],
            'cittadinanza' => $row["CITTADINANZA"],
            'istatcitt' => $row["ISTAT_CITT"],
            'descrcittbreve' => $row["DESC_CITT_BREVE"],
            'desccittadinanza' => $row["DESC_CITTADINANZA"],
            'codicefamiglia' => $row["CODICE_FAMIGLIA"],
            'tipovia' => $row["TIPOVIA"],
            'codvia' => $row["CODVIA"],
            'desvia' => $row["DESVIA"],
            'numciv' => $row["NUMCIV"],
            'esponente' => $row["ESPONENTE"],
            'interno' => $row["INTERNO"],
            'statocivile' => $row["STATO_CIVILE"],
            'descrizionestatocivile' => $row["DES_STATO_CIVILE"],
            'codicetitolostudio' => $row["COD_T_STUD"],
            'descrizionetitolostudio' => $row["DES_T_STUD"],
            'livellotitolostudio' => $row["LIV_T_STUD"],
            'codiceprofessione' => $row["COD_PROF"],
            'descizioneprofessione' => $row["DES_PROF"],
            'ramoattivita' => $row["RAMO_ATTIVITA"],
            'posizioneprofessionale' => $row["POSIZ_PROF"],
            'residente' => ($giro == 2 ? 1 : 0),
            'deceduto' => ($giro == 3 ? 1 : 0),
            'aire' => ($giro == 4 ? 1 : 0),
            'trovato' => 1
        );
        break;
      }
    }

    return $risultato;
  }

  function getXaternita($codicefiscale) {
    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $risultato = array();

    $query = $this->getSqlXaternita($codicefiscale);
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();


    if (!$resultset) {
      $risultato = array('trovato' => 0);
    } else {
      foreach ($resultset as $row) {
        $risultato = array(
            'matricola1' => $row["MATRICOLA1"], 
            'codicefiscale1' => $row["COD_FISC1"], 
            'nome1' => $row["NOME1"],
            'cognnome1' => $row["COGNOME1"],
            'matricola2' => $row["MATRICOLA2"], 
            'codicefiscale2' => $row["COD_FISC2"], 
            'nome2' => $row["NOME2"],
            'cognome2' => $row["COGNOME2"],
            'trovato' => 1
        );
        break;
      }
    }

    return $risultato;
  }

  function getStatofamiglia($codicefiscale) {
    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $risultato = array();

    $query = $this->getSqlStatofamiglia($codicefiscale);

    if ($query) {
      $connessione->executeSelectQuery($query);
      $resultset = $connessione->getResultset();
    } else {
      $resultset = false;
    }

    if (!$resultset) {
      $risultato = array('trovato' => 0);
    } else {
      foreach ($resultset as $row) {
        $risultato[$row["MATRICOLA"]] = array(
            'codicefiscale' => $row["CODFISCALE"],
            'parentela' => $row["GRADO_PARENTELA"],
            'descrizioneparentela' => $row["DESC_PARENTELA"],
            'trovato' => 1
        );
      }
    }
    return $risultato;
  }
  
  function getStatofamigliaCompleto($codicefiscale) {
    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $risultato = array();

    $query = $this->getSqlStatofamiglia($codicefiscale);

    if ($query) {
      $connessione->executeSelectQuery($query);
      $resultset = $connessione->getResultset();
    } else {
      $resultset = false;
    }

    if (!$resultset) {
      $risultato = array('trovato' => 0);
    } else {
      foreach ($resultset as $row) {
        $risultato[$row["MATRICOLA"]] = array(
            'codicefiscale' => $row["CODFISCALE"],
            'parentela' => $row["GRADO_PARENTELA"],
            'descrizioneparentela' => $row["DESC_PARENTELA"],
            'dettaglio' => $this->getPersonaMatricola($row["MATRICOLA"]),
            'trovato' => 1
        );
      }
    }
    return $risultato;
  }
  
  

  function getSqlStatofamiglia($codicefiscale) {


    $vettorecodicefiscale = $this->getPersona($codicefiscale);

    if ($vettorecodicefiscale["trovato"] == 0) {
      return false;
    }

    $codicefamiglia = $vettorecodicefiscale["codicefamiglia"];


    $query = "SELECT FPER_COD_PERSONA as MATRICOLA,
    PER_KEY_CODICE_FIS AS CODFISCALE,
    FPER_RAPP_PAR AS GRADO_PARENTELA,
    case WHEN FPER_RAPP_PAR = 'CF'
    THEN 'INTESTATARIO SCHEDA'
    WHEN FPER_RAPP_PAR = 'SL'
    THEN 'INTESTATARIO SCHEDA'
    WHEN FPER_RAPP_PAR = 'IC'
    THEN 'INTESTATARIO CONVIVENZA'
    WHEN FPER_RAPP_PAR = 'MR'
    THEN 'MARITO                 '
    WHEN FPER_RAPP_PAR = 'MG'
    THEN 'MOGLIE                 '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'MFG'
    THEN 'FIGLIO                 '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'FFG'
    THEN 'FIGLIA                 '
    WHEN FPER_RAPP_PAR = 'FU'
    THEN 'FRATELLO UNILATERALE   '
    WHEN FPER_RAPP_PAR = 'SU'
    THEN 'SORELLA UNILATERALE    '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'MFS'
    THEN 'FIGLIASTRO             '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'FFS'
    THEN 'FIGLIASTRA             '
    WHEN FPER_RAPP_PAR = 'PA'
    THEN 'PADRE                  '
    WHEN FPER_RAPP_PAR = 'MA'
    THEN 'MADRE                  '
    WHEN FPER_RAPP_PAR = 'PT'
    THEN 'PATRIGNO               '
    WHEN FPER_RAPP_PAR = 'MT'
    THEN 'MATRIGNA               '
    WHEN FPER_RAPP_PAR = 'NI'
    THEN 'NIPOTE                 '
    WHEN FPER_RAPP_PAR = 'GE'
    THEN 'GENERO                 '
    WHEN FPER_RAPP_PAR = 'NU'
    THEN 'NUORA                  '
    WHEN FPER_RAPP_PAR = 'PN'
    THEN 'PRONIPOTE              '
    WHEN FPER_RAPP_PAR = 'FR'
    THEN 'FRATELLO               '
    WHEN FPER_RAPP_PAR = 'SO'
    THEN 'SORELLA                '
    WHEN FPER_RAPP_PAR = 'AF'
    THEN 'AFFINE                 '
    WHEN FPER_RAPP_PAR = 'AV'
    THEN 'AVO                    '
    WHEN FPER_RAPP_PAR = 'BV'
    THEN 'BISAVOLO               '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'MSC'
    THEN 'SUOCERO               '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'FSC'
    THEN 'SUOCERA               '
    WHEN FPER_RAPP_PAR = 'NC'
    THEN 'NIPOTE COLLATERALE    '
    WHEN FPER_RAPP_PAR = 'PC'
    THEN 'PRONIPOTE COLLATERALE '
    WHEN FPER_RAPP_PAR = 'CO'
    THEN 'COGNATO               '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'MZI'
    THEN 'ZIO                   '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'FZI'
    THEN 'ZIA                   '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'MCU'
    THEN 'CUGINO                '
    WHEN PER_KEY_SESSO || FPER_RAPP_PAR = 'FCU'
    THEN 'CUGINA                '
    WHEN FPER_RAPP_PAR = 'CV'
    THEN 'CONVIVENTE            '
    WHEN FPER_RAPP_PAR = 'AG'
    THEN 'AGGREGATO             ' ELSE 'NON TROVATO           '
    END AS DESC_PARENTELA
    from SIPO.POTDFPER, SIPO.POTDAPER
    where fper_ente = '01'
    AND FPER_COD_FAMIGLIA = " . $codicefamiglia . "
    and FPER_DATA_FINE = '99999999'
    and FPER_COD_PERSONA = PER_KEY_MATRICOLA";


    return $query;
  }


  function getElettoriByRevisione($tiporevisione) {

    $container = $this->container;

    //$this->OracleConnection = $this->container->get("oracle_sipo");
    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EAN_MATRICOLA, EAN_COG_NOM, PER_KEY_CODICE_FIS, PER_FLAG_CFIS, EAN_SESSO, EAN_DATANAS, EAN_COMUNAS, 
                  EAN_ANNISC_ATTONAS, EAN_NUM_ATTONAS, PER_COM_ISCR_NAS, PER_FLAG_TRASC,
                  EAN_PARTE , EAN_SERIE1, EAN_SERIE2 , EAN_TIPREG , EAN_NUMREG, EAN_STATCIV, EAN_CONIUGE, EPO_COMUEMIG
                  FROM " . $this->schemasipo . ".POTDEANA, " . $this->schemasipo . ".POTDEPOS , " . $this->schemasipo . ".POTDAPER
	          WHERE EPO_STATO = 'C' AND EPO_CAUSA_CANC IN (5 , 6 , 7)
	          AND EPO_POSREV = '2' AND EPO_TIPREV = '" . $tiporevisione . "'
	          AND EAN_MATRICOLA = EPO_MATRICOLA AND EAN_ENTE = EPO_ENTE
	          AND PER_KEY_MATRICOLA = EPO_MATRICOLA AND PER_KEY_ENTE = EPO_ENTE
                  ORDER BY EPO_COMUEMIG";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    /*
      $risultato[] = array('matricola' => 725, 'cognome' => 'CORSANI', 'nome' => 'LIU', 'codicefiscale' => 'CRSLIU44R62D612K',
      'flagcodicefiscale' => 3, 'sesso' => 'F', 'datanascita' => '19441022',
      'comunenascita' => '48017', 'annoiscrizioneattonascita' => 1944,
      'comuneatto'=> '48017', 'numeroattonascita' => 2519, 'parte' => 1, 'serie1' => 'A',
      'serie2' => '', 'tiporegistrazione' => '', 'numeroregistrazione' => '',
      'statocivile' => 'C', 'coniuge' => 'MAGGIORELLI', 'comuneemigrazione' => 48016);
      $risultato[] = array('matricola' => 726, 'cognome' => 'PROVA', 'nome' => 'PROVA', 'codicefiscale' => 'CRSLIU44R62D612K',
      'flagcodicefiscale' => 3, 'sesso' => 'F', 'datanascita' => '19441022',
      'comunenascita' => '48017', 'annoiscrizioneattonascita' => 1944,
      'comuneatto'=> '48017','numeroattonascita' => 2520, 'parte' => 1, 'serie1' => 'A',
      'serie2' => '', 'tiporegistrazione' => '', 'numeroregistrazione' => '',
      'statocivile' => 'C', 'coniuge' => 'PROVA', 'comuneemigrazione' => 48016);
      $risultato[] = array('matricola' => 727, 'cognome' => 'RIPROVA', 'nome' => 'RIPROVA',  'codicefiscale' => 'CRSLIU44R62D612K',
      'flagcodicefiscale' => 3, 'sesso' => 'F', 'datanascita' => '19441022',
      'comunenascita' => '48017', 'annoiscrizioneattonascita' => 1944,
      'comuneatto'=> '48017','numeroattonascita' => 2521, 'parte' => 1, 'serie1' => 'A',
      'serie2' => '', 'tiporegistrazione' => '', 'numeroregistrazione' => '',
      'statocivile' => 'C', 'coniuge' => 'RIPROVA', 'comuneemigrazione' => 48018);
     */
    foreach ($resultset as $row) {
      $cognome = trim(substr($row["EAN_COG_NOM"], 0, strpos($row["EAN_COG_NOM"], '/')));
      $nome = trim(substr($row["EAN_COG_NOM"], strpos($row["EAN_COG_NOM"], '/') + 1));
      if ($row["PER_FLAG_TRASC"] == '1') {
        $comuneatto = $this->getTrascrizioneNascita($row["EAN_MATRICOLA"], $container);
      } else {
        $comuneatto = $row["PER_COM_ISCR_NAS"];
      }
      $risultato[] = array('matricola' => $row["EAN_MATRICOLA"], 'cognome' => $cognome, 'nome' => $nome, 'codicefiscale' => $row["PER_KEY_CODICE_FIS"],
          'flagcodicefiscale' => $row["PER_FLAG_CFIS"], 'sesso' => $row["EAN_SESSO"], 'datanascita' => $row["EAN_DATANAS"],
          'comunenascita' => $row["EAN_COMUNAS"], 'annoiscrizioneattonascita' => $row["EAN_ANNISC_ATTONAS"],
          'comuneatto' => $comuneatto, 'numeroattonascita' => $row["EAN_NUM_ATTONAS"], 'parte' => $row["EAN_PARTE"], 'serie1' => $row["EAN_SERIE1"],
          'serie2' => $row["EAN_SERIE2"], 'tiporegistrazione' => $row["EAN_TIPREG"], 'numeroregistrazione' => $row["EAN_NUMREG"],
          'statocivile' => $row["EAN_STATCIV"], 'coniuge' => $row["EAN_CONIUGE"], 'comuneemigrazione' => $row["EPO_COMUEMIG"]);
    }
    return $risultato;
  }

  function getTrascrizioneNascita($matricola) {

    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;
    $query = "SELECT TNAS_ATTO_COMUNE
                  FROM " . $this->schemasipo . ".POTDATNA
                   WHERE TNAS_KEY_ENTE = '01' AND 
                   TNAS_KEY_MATRICOLA = $matricola";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = $resultset[0]["TNAS_ATTO_COMUNE"];
    return $risultato;
  }

  function getDecodificaComune($codice) {

    $container = $this->container;

    $risultato = array();

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT CMN_KEY_COMUNE, CMN_NOME, CMN_PROVINCIA, CMN_COD_ISTAT
                  FROM " . $this->schemasipo . ".POTDCCOM 
                  WHERE CMN_KEY_COMUNE = $codice";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    if (count($resultset) > 0) {
      if (($codice > 120000) && (((strlen(trim($resultset[0]["CMN_PROVINCIA"])) == 3) && (substr($resultset[0]["CMN_PROVINCIA"], 2, 1) == '*')) || (strlen(trim($resultset[0]["CMN_PROVINCIA"])) != 2) )) {
        $querynaz = "SELECT CNAZ_SIGLA, CNAZ_COD_CITT, CNAZ_NOME_BREVE
                            FROM " . $this->schemasipo . ".POTDCNAZ
                            WHERE CNAZ_SIGLA = '" . $resultset[0]["CMN_PROVINCIA"] . "'";
        $connessione->executeSelectQuery($querynaz);
        $resultsetnaz = $connessione->getResultset();
        $risultato = array("codice" => $resultsetnaz[0]["CNAZ_COD_CITT"], "comune" => $resultset[0]["CMN_NOME"], "provincia" => ' ', "stato" => $resultsetnaz[0]["CNAZ_NOME_BREVE"]);
      } else {
        $risultato = array("codice" => $resultset[0]["CMN_KEY_COMUNE"], "comune" => $resultset[0]["CMN_NOME"], "provincia" => $resultset[0]["CMN_PROVINCIA"], "stato" => 'ITALIA');
      }
    } else {
      $risultato = array("codice" => $codice, "comune" => "Sconosciuto", "provincia" => "ZZZ", "stato" => 'SCONOSCIUTO');
    }

    return $risultato;
  }

  function getTesseraElettorale($matricola) {

    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT T.TES_KEY_NUM, T.TES_DATA_NOTIF, T.TES_DATA_ANN
	          FROM " . $this->schemasipo . ".POTDETES T 
                  WHERE T.TES_KEY_MATRICOLA  = $matricola AND 
                        T.TES_KEY_ENTE = '01' AND
                        T.TES_DATA_RIL = (SELECT MAX(X.TES_DATA_RIL) 
                                          FROM " . $this->schemasipo . ".POTDETES X 
                                          WHERE X.TES_KEY_MATRICOLA  = T.TES_KEY_MATRICOLA AND 
					        X.TES_KEY_ENTE       = T.TES_KEY_ENTE)";

    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();

    $risultato = array('numerotessera' => $resultset[0]["TES_KEY_NUM"], 'datanotifica' => $resultset[0]["TES_DATA_NOTIF"], 'dataannullamento' => $resultset[0]["TES_DATA_ANN"]);
    return $risultato;
  }

  function scriviDocumentiFascicoloElettronico($valori) {

    $container = $this->container;

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $codfiscale = $valori["codicefiscale"];
    if ($valori["senso"] == 'E') {
      $query = "SELECT EPO_MATRICOLA, EPO_FASCICOLO , EPO_STATO
            FROM " . $this->schemasipo . ".POTDEPOS, " . $this->schemasipo . ".POTDAPER " .
              " WHERE PER_KEY_ENTE = '01' AND PER_KEY_CODICE_FIS = '$codfiscale' " .
              " AND EPO_MATRICOLA = PER_KEY_MATRICOLA " .
              " AND EPO_ENTE = PER_KEY_ENTE " .
              " AND ((EPO_STATO = 'I' AND EPO_CAUSA_ISCR = 50 AND EPO_TIPREV = ' ') OR " .
              " (EPO_STATO = 'C' and EPO_ISCR_LISTAGG IN ('*', 'T' , 'V') AND " .
              " EPO_TIPREV IN('D' , 'T' , 'E') AND " .
              " EXISTS(SELECT * FROM " . $this->schemasipo . ".POTDEVAR " .
              " WHERE VAR_MATRICOLA = EPO_MATRICOLA AND " .
              " VAR_ENTE = EPO_ENTE AND VAR_TIPO = 'LA')))";
    } else {
      $query = "SELECT EPO_MATRICOLA, EPO_FASCICOLO
FROM " . $this->schemasipo . ".POTDEPOS, " . $this->schemasipo . ".POTDAPER
WHERE PER_KEY_ENTE = '01' AND PER_KEY_CODICE_FIS = '$codfiscale'
AND EPO_MATRICOLA = PER_KEY_MATRICOLA
AND EPO_ENTE = PER_KEY_ENTE
AND EPO_STATO = 'C'
AND EPO_CAUSA_CANC  IN (5, 6, 7)
AND EPO_TIPREV IN ('D', 'T', 'E')";
    }
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();

    $fascicolo = $resultset[0]["EPO_FASCICOLO"];
    $matricola = $resultset[0]["EPO_MATRICOLA"];
    $oggi = date("Ymd");
    //$array_oggi = date_parse_from_format("Ymd", $oggi);
    //$dataoper = $array_oggi["year"] . $array_oggi["month"] . $array_oggi["day"];

    $queryInsert = "INSERT INTO " . $this->schemasipo . ".POTDEFAD (EFAD_ENTE, EFAD_FASCICOLO, EFAD_MATRICOLA, EFAD_DATA, EFAD_SENSO, EFAD_TIPODOC, EFAD_TIPOENTE, EFAD_COMUNE, EFAD_NOME_DOC, EFAD_ELABORATO, EFAD_TESSERA_CONS)" .
            " VALUES ('01', $fascicolo, $matricola, '$oggi', '" . $valori["senso"] . "' , '3D', 'CM', " . $valori["codiceistat"] . ", '" . $valori["nomefile"] . "', 'N' , '" . $valori["tesseracons"] . "')";

    $connessione->executeQuery($queryInsert);
    if (($valori["senso"] == 'E') && ($resultset[0]["EPO_STATO"] == 'I')) {
      $queryInsert = "UPDATE " . $this->schemasipo . ".POTDEPOS SET EPO_POSREV = '4' WHERE EPO_MATRICOLA = $matricola AND " .
              "EPO_STATO = 'I' AND EPO_TIPREV = ' ' AND EPO_POSREV IN('0', '1')";
      $connessione->executeQuery($queryInsert);
    }
    return TRUE;
  }

  function getPersona3D($codicefiscale) {

    $container = $this->container;

    //$this->OracleConnection = $this->container->get("oracle_sipo");
    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EAN_MATRICOLA, EAN_COG_NOM, PER_KEY_CODICE_FIS,  EAN_SESSO, EAN_DATANAS, EAN_COMUNAS, " .
            " EAN_STATCIV , EAN_ANNISC_ATTONAS, EAN_NUM_ATTONAS , EAN_PARTE, EAN_SERIE1 " .
            " FROM " . $this->schemasipo . ".POTDEANA , " . $this->schemasipo . ".POTDEPOS , " . $this->schemasipo . ".POTDAPER " .
            " WHERE PER_KEY_CODICE_FIS = '" . $codicefiscale . "'" .
            " AND EAN_MATRICOLA = PER_KEY_MATRICOLA AND EAN_ENTE = PER_KEY_ENTE " .
            " AND PER_KEY_MATRICOLA = EPO_MATRICOLA AND PER_KEY_ENTE = EPO_ENTE " .
            " AND ((EPO_STATO = 'I' AND EPO_CAUSA_ISCR = 50)  OR" .
            " (EPO_STATO = 'C' and EPO_ISCR_LISTAGG IN ('*', 'T' , 'V') AND " .
            " EPO_TIPREV IN('D' , 'T' , 'E') AND " .
            " EXISTS(SELECT * FROM " . $this->schemasipo . ".POTDEVAR " .
            " WHERE VAR_MATRICOLA = EPO_MATRICOLA AND " .
            " VAR_ENTE = EPO_ENTE AND VAR_TIPO = 'LA')))";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    foreach ($resultset as $row) {
      $cognome = trim(substr($row["EAN_COG_NOM"], 0, strpos($row["EAN_COG_NOM"], '/')));
      $nome = trim(substr($row["EAN_COG_NOM"], strpos($row["EAN_COG_NOM"], '/') + 1));
      $daticomune = $this->getDecodificaComune($row["EAN_COMUNAS"], $container);
      $risultato = array('matricola' => $row["EAN_MATRICOLA"], 'cognome' => $cognome, 'nome' => $nome,
          'codicefiscale' => $row["PER_KEY_CODICE_FIS"],
          'sesso' => $row["EAN_SESSO"], 'datanascita' => $row["EAN_DATANAS"],
          'comunenascita' => $row["EAN_COMUNAS"], 'statocivile' => $row["EAN_STATCIV"],
          'descrcomune' => $daticomune["comune"], 'provincia' => $daticomune["provincia"],
          'annoatto' => $row["EAN_ANNISC_ATTONAS"], 'numatto' => $row["EAN_NUM_ATTONAS"], 'parteatto' => $row["EAN_PARTE"],
          'descrstato' => $daticomune["stato"], 'codicecomnaz' => $daticomune["codice"]);
      break;
    }
    return $risultato;
  }

  function getPersonaNome3D($nominativo, $datanascita, $comunenascita) {


    $container = $this->container;

    //$this->OracleConnection = $this->container->get("oracle_sipo");
    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EAN_MATRICOLA, EAN_COG_NOM, PER_KEY_CODICE_FIS,  EAN_SESSO, EAN_DATANAS, EAN_COMUNAS, " .
            " EAN_STATCIV , EAN_ANNISC_ATTONAS, EAN_NUM_ATTONAS , EAN_PARTE, EAN_SERIE1, PER_KEY_CODICE_FIS " .
            " FROM " . $this->schemasipo . ".POTDEANA , " . $this->schemasipo . ".POTDEPOS , " . $this->schemasipo . ".POTDAPER " .
            " WHERE EAN_COG_NOM = '" . $nominativo . "' AND EAN_DATANAS = '" . $datanascita . "' AND EAN_COMUNAS = $comunenascita" .
            " AND PER_KEY_MATRICOLA = EAN_MATRICOLA AND PER_KEY_ENTE = EAN_ENTE  " .
            " AND EPO_MATRICOLA = EAN_MATRICOLA AND EPO_ENTE = EAN_ENTE " .
            " AND ((EPO_STATO = 'I' AND EPO_CAUSA_ISCR = 50)  OR" .
            " (EPO_STATO = 'C' and EPO_ISCR_LISTAGG IN ('*', 'T' , 'V') AND " .
            " EPO_TIPREV IN('D' , 'T' , 'E') AND " .
            " EXISTS(SELECT * FROM " . $this->schemasipo . ".POTDEVAR " .
            " WHERE VAR_MATRICOLA = EPO_MATRICOLA AND " .
            " VAR_ENTE = EPO_ENTE AND VAR_TIPO = 'LA')))";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    foreach ($resultset as $row) {
      $cognome = trim(substr($row["EAN_COG_NOM"], 0, strpos($row["EAN_COG_NOM"], '/')));
      $nome = trim(substr($row["EAN_COG_NOM"], strpos($row["EAN_COG_NOM"], '/') + 1));
      $daticomune = $this->getDecodificaComune($row["EAN_COMUNAS"], $container);
      $risultato = array('matricola' => $row["EAN_MATRICOLA"], 'cognome' => $cognome, 'nome' => $nome,
          'sesso' => $row["EAN_SESSO"], 'datanascita' => $row["EAN_DATANAS"],
          'codicefiscale' => $row["PER_KEY_CODICE_FIS"],
          'comunenascita' => $row["EAN_COMUNAS"], 'statocivile' => $row["EAN_STATCIV"],
          'descrcomune' => $daticomune["comune"], 'provincia' => $daticomune["provincia"],
          'annoatto' => $row["EAN_ANNISC_ATTONAS"], 'numatto' => $row["EAN_NUM_ATTONAS"], 'parteatto' => $row["EAN_PARTE"],
          'descrstato' => $daticomune["stato"], 'codicecomnaz' => $daticomune["codice"]);
      break;
    }
    return $risultato;
  }

  function getElettoreCodFiscale($codicefiscale) {
    $container = $this->container;
    //$this->OracleConnection = $this->container->get("oracle_sipo");
    $this->OracleConnection = $this->container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EAN_MATRICOLA, EAN_COG_NOM, PER_KEY_CODICE_FIS,  EAN_SESSO, EAN_DATANAS, EAN_COMUNAS, " .
            " EAN_STATCIV , EAN_ANNISC_ATTONAS, EAN_NUM_ATTONAS , EAN_PARTE, EAN_SERIE1, EPO_FASCICOLO " .
            " FROM " . $this->schemasipo . ".POTDEANA , " . $this->schemasipo . ".POTDEPOS , " . $this->schemasipo . ".POTDAPER " .
            " WHERE EAN_MATRICOLA = EPO_MATRICOLA AND EAN_ENTE = EPO_ENTE " .
            " AND PER_KEY_MATRICOLA = EPO_MATRICOLA AND PER_KEY_ENTE = EPO_ENTE " .
            " AND PER_KEY_CODICE_FIS = '" . $codicefiscale . "'";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    $righe = 0;
    foreach ($resultset as $row) {
      $daticomune = $this->getDecodificaComune($row["EAN_COMUNAS"], $container);
      $risultato[] = array('matricola' => $row["EAN_MATRICOLA"], 'nominativo' => $row["EAN_COG_NOM"],
          'codicefiscale' => $row["PER_KEY_CODICE_FIS"],
          'sesso' => $row["EAN_SESSO"], 'datanascita' => $row["EAN_DATANAS"],
          'comunenascita' => $row["EAN_COMUNAS"], 'statocivile' => $row["EAN_STATCIV"],
          'descrcomune' => $daticomune["comune"], 'provincia' => $daticomune["provincia"],
          'annoatto' => $row["EAN_ANNISC_ATTONAS"], 'numatto' => $row["EAN_NUM_ATTONAS"], 'parteatto' => $row["EAN_PARTE"],
          'descrstato' => $daticomune["stato"], 'codicecomnaz' => $daticomune["codice"], 'fascicolo' => $row["EPO_FASCICOLO"]);
      $righe += 1;
    }
    return $risultato;
  }

  function getElettoreFascicolo($fascicolo) {

    $container = $this->container;

    //$this->OracleConnection = $this->container->get("oracle_sipo");

    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EAN_MATRICOLA, EAN_COG_NOM, PER_KEY_CODICE_FIS,  EAN_SESSO, EAN_DATANAS, EAN_COMUNAS, " .
            " EAN_STATCIV , EAN_ANNISC_ATTONAS, EAN_NUM_ATTONAS , EAN_PARTE, EAN_SERIE1, EPO_FASCICOLO " .
            " FROM " . $this->schemasipo . ".POTDEANA , " . $this->schemasipo . ".POTDEPOS , " . $this->schemasipo . ".POTDAPER " .
            " WHERE EAN_MATRICOLA = EPO_MATRICOLA AND EAN_ENTE = EPO_ENTE " .
            " AND PER_KEY_MATRICOLA = EPO_MATRICOLA AND PER_KEY_ENTE = EPO_ENTE " .
            " AND EPO_FASCICOLO = " . $fascicolo;
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    $righe = 0;
    foreach ($resultset as $row) {
      $daticomune = $this->getDecodificaComune($row["EAN_COMUNAS"], $container);
      $risultato[] = array('matricola' => $row["EAN_MATRICOLA"], 'nominativo' => $row["EAN_COG_NOM"],
          'codicefiscale' => $row["PER_KEY_CODICE_FIS"],
          'sesso' => $row["EAN_SESSO"], 'datanascita' => $row["EAN_DATANAS"],
          'comunenascita' => $row["EAN_COMUNAS"], 'statocivile' => $row["EAN_STATCIV"],
          'descrcomune' => $daticomune["comune"], 'provincia' => $daticomune["provincia"],
          'annoatto' => $row["EAN_ANNISC_ATTONAS"], 'numatto' => $row["EAN_NUM_ATTONAS"], 'parteatto' => $row["EAN_PARTE"],
          'descrstato' => $daticomune["stato"], 'codicecomnaz' => $daticomune["codice"], 'fascicolo' => $row["EPO_FASCICOLO"]);
      $righe += 1;
    }
    return $risultato;
  }

  function getElettoreNome($nome, $datanascita, $comunenascita) {

    $container = $this->container;

    //$this->OracleConnection = $this->container->get("oracle_sipo");
    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EAN_MATRICOLA, EAN_COG_NOM, PER_KEY_CODICE_FIS,  EAN_SESSO, EAN_DATANAS, EAN_COMUNAS, " .
            " EAN_STATCIV , EAN_ANNISC_ATTONAS, EAN_NUM_ATTONAS , EAN_PARTE, EAN_SERIE1, EPO_FASCICOLO " .
            " FROM " . $this->schemasipo . ".POTDEANA , " . $this->schemasipo . ".POTDEPOS , " . $this->schemasipo . ".POTDAPER " .
            " WHERE EAN_MATRICOLA = EPO_MATRICOLA AND EAN_ENTE = EPO_ENTE " .
            " AND PER_KEY_MATRICOLA = EPO_MATRICOLA AND PER_KEY_ENTE = EPO_ENTE " .
            " AND EAN_COGNOM = '" . $nome . "'";
    if ($datanascita != '') {
      $query .= " AND EAN_DATANAS = '" . $datanascita . "' ";
    }
    if ($comunenascita != '') {
      $query .= " AND EAN_COMUNAS = " . $comunenascita;
    }
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    foreach ($resultset as $row) {
      $daticomune = $this->getDecodificaComune($row["EAN_COMUNAS"], $container);
      $risultato[] = array('matricola' => $row["EAN_MATRICOLA"], 'nominativo' => $row["EAN_COG_NOM"],
          'codicefiscale' => $row["PER_KEY_CODICE_FIS"],
          'sesso' => $row["EAN_SESSO"], 'datanascita' => $row["EAN_DATANAS"],
          'comunenascita' => $row["EAN_COMUNAS"], 'statocivile' => $row["EAN_STATCIV"],
          'descrcomune' => $daticomune["comune"], 'provincia' => $daticomune["provincia"],
          'annoatto' => $row["EAN_ANNISC_ATTONAS"], 'numatto' => $row["EAN_NUM_ATTONAS"], 'parteatto' => $row["EAN_PARTE"],
          'descrstato' => $daticomune["stato"], 'codicecomnaz' => $daticomune["codice"], 'fascicolo' => $row["EPO_FASCICOLO"]);
    }
    return $risultato;
  }

  function getMovimentiFascicolo($fascicolo) {

    $container = $this->container;

    //$this->OracleConnection = $this->container->get("oracle_sipo");
    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EFAI_TIPOMOV, EFAI_DATA, EFAI_OPERAZIONE,  EFAI_COG_NOM, EFAI_SESSO, EFAI_STATCIV, EFAI_CODVIA, " .
            " EFAI_NUMCIV , EFAI_ESPCIV, EFAI_NUMINT , EFAI_ESPINT, EFAI_COMUNAS, EFAI_ANNISC_ATTONAS, " .
            " EFAI_NUM_ATTONAS , EFAI_PARTE, EFAI_SERIE1 , EFAI_SERIE2, EFAI_TIPREG, EFAI_NUMREG, " .
            " EFAI_UFF_ATTONAS , EFAI_CONIUGE, EFAI_DATANAS ,EFAI_COMURES , EFAI_SEZIONE, EFAI_PROGRSEZ, EFAI_LISTGEN, " .
            " EFAI_AIRE , EFAI_COMUMIG, EFAI_VRB_TIPREV , EFAI_VRB_DATREV, EFAI_VRB_NUMREV, EFAI_LISTAGG, EFAI_MATRICOLA " .
            " FROM " . $this->schemasipo . ".POTDEFAI" .
            " WHERE EFAI_FASCICOLO = " . $fascicolo . 
            " AND EFAI_ENTE = '01'" .
            " ORDER BY EFAI_DATA, EFAI_TIPOMOV, EFAI_OPERAZIONE";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    foreach ($resultset as $row) {
      $tipo = $row["EFAI_TIPOMOV"];
      switch($tipo) {
          case 'I';
              $tipomov = "Inserimento";
          case 'V';
              $tipomov = "Variazione";
          case 'C';
              $tipomov = "Cancellazione";
          case 'D';
              $tipomov = "Documento Elettronico";
      }
      $operazione = $row["EFAI_OPERAZIONE"];
      // da decodificare
      $risultato[] = array('matricola' => $row["EFAI_MATRICOLA"], 
          'tipomov' => $row["EFAI_TIPOMOV"], 'descrtipomov' => $tipomov, 'datamov' => $row["EFAI_DATA"], 
          'operazione' => $row["EFAI_OPERAZIONE"],  'descroperazione' => $operazione,
          'nominativo' => $row["EFAI_COG_NOM"],
          'sesso' => $row["EFAI_SESSO"], 
          'statocivile' => $row["EFAI_STATCIV"],
          'codvia' => $row["EFAI_CODVIA"], 'numciv' => $row["EFAI_NUMCIV"], 'espciv' => $row["EFAI_ESPCIV"],
          'numint' => $row["EFAI_NUMINT"], 'espint' => $row["EFAI_ESPINT"],
          'comunenascita' => $row["EFAI_COMUNAS"], 
          'annoatto' => $row["EFAI_ANNISC_ATTONAS"], 'numatto' => $row["EFAI_NUM_ATTONAS"], 'parteatto' => $row["EFAI_PARTE"],
          'serie1atto' => $row["EFAI_SERIE1"],'serie2atto' => $row["EFAI_SERIE2"],'tipreg' => $row["EFAI_TIPREG"],
          'numreg' => $row["EFAI_NUMREG"],'ufficio' => $row["EFAI_UFF_ATTONAS"],
          'coniuge' => $row["EFAI_CONIUGE"],
          'datanascita' => $row["EFAI_DATANAS"],
          'comuneres' => $row["EFAI_COMURES"],
          'sezione' => $row["EFAI_SEZIONE"], 'progrsez' => $row["EFAI_PROGRSEZ"], 'listagen' => $row["EFAI_LISTGEN"],
          'aire' => $row["EFAI_AIRE"],'comuneemig' => $row["EFAI_COMUMIG"],
          'tipoverbalerev' => $row["EFAI_VRB_TIPREV"],'dataverbalerev' => $row["EFAI_VRB_DATREV"],'numeroverbalerev' => $row["EFAI_VRB_NUMREV"],
          'listaaggiunta' => $row["EFAI_LISTAGG"]);
    }
    return $risultato;
  }

  function getAllegatiFascicolo($fascicolo) {

    $container = $this->container;

    //$this->OracleConnection = $this->container->get("oracle_sipo");
    $this->OracleConnection = $container->get("oracle_sipo");
    $connessione = $this->OracleConnection;

    $query = "SELECT EFAD_MATRICOLA, EFAD_DATA, EFAD_SENSO, EFAD_TIPODOC,  EFAD_TIPOENTE, EFAD_COMUNE, EFAD_NOME_DOC, EFAD_ELABORATO, " .
            " EFAD_TESSERA_CONS " .
            " FROM " . $this->schemasipo . ".POTDEFAD" .
            " WHERE EFAD_FASCICOLO = " . $fascicolo . 
            " AND EFAD_ENTE = '01'" .
            " ORDER BY EFAD_DATA, EFAD_SENSO, EFAD_TIPODOC";
    $connessione->executeSelectQuery($query);
    $resultset = $connessione->getResultset();
    $risultato = array();
    foreach ($resultset as $row) {
        $senso = $row["EFAD_SENSO"];
        switch($senso) {
            case 'E';
                $sensodoc = "In Entrata";
            case 'U';
                $sensodoc = "In Uscita";
        }
        $tipo = $row["EFAD_TIPODOC"];
        // da decodificare
        $ente = $row["EFAD_TIPOENTE"];
        // da decodificare
        if ($row["EFAD_COMUNE"] > 0) {
            $daticomune = $this->getDecodificaComune($row["EFAD_COMUNE"], $container);
        } else {
            $daticomune = ' ';
        }  
        $risultato[] = array('matricola' => $row["EFAD_MATRICOLA"], 
           'dataallegato' => $row["EFAD_DATA"], 
           'senso' => $row["EFAD_SENSO"], 'descrsenso' => $sensodoc, 
           'tipodoc' => $row["EFAD_TIPODOC"],'descrtipodoc' => $tipo, 
           'tipoente' => $row["EFAD_TIPOENTE"], 'descrtipoente' => $ente, 
           'comune' => $row["EFAD_COMUNE"] ,'descrcomune' =>  $daticomune["comune"] ,
           'nomedocumento' => $row["EFAD_NOME_DOC"], 
           'elaborato' => $row["EFAD_ELABORATO"],
           'tesseracons' => $row["EFAD_TESSERA_CONS"]);
    }
    return $risultato;
  }

}