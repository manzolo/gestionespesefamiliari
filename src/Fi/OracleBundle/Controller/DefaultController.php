<?php

namespace Fi\OracleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        $queryObj = $this->get('oracle_manager');
        $queryObj->executeSelectQuery("SELECT * FROM ALL_TABLES WHERE OWNER = 'P00'");
        
        return $this->render('FiOracleBundle:Default:index.html.twig',array("risultato"=>$queryObj->getResultset()));
        
        /*
        //CREATE E DROP TABLE
        $queryObj->executeQuery("DROP TABLE WTMPCDF");
        $queryObj->executeQuery("CREATE TABLE WTMPCDF
                                ( id            NUMBER PRIMARY KEY,
                                  descrizione          VARCHAR2(255),
                                  datultagg DATE
                                )");
        //DELETE
        $queryObj->executeQuery("DELETE FROM WTMPCDF WHERE ID IN (1,2)");
        echo $queryObj->getNumrows();
        
        //INSERT
        $queryObj->executeQuery("INSERT INTO WTMPCDF (ID,DESCRIZIONE,DATULTAGG) VALUES (1,'PROVA',SYSDATE)");
        $queryObj->executeQuery("INSERT INTO WTMPCDF (ID,DESCRIZIONE,DATULTAGG) VALUES (2,'ALTRA PROVA',SYSDATE)");
        
        //SELECT (ritorna il numero di record estratti [$queryObj->getNumrows()] e un array con i risultati [$queryObj->getResultset()]
        $queryObj->executeSelectQuery("SELECT ID,DESCRIZIONE,TO_CHAR(DATULTAGG,'DD/MM/YYYY') DATULTAGG FROM WTMPCDF");
        echo $queryObj->getNumrows();
         */
    }

}
