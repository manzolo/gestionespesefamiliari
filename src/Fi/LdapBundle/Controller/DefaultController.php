<?php

namespace Fi\LdapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        /* @var $ldap \Fi\LdapBundle\DependencyInjection\fiLdap */
        $ldap = $this->get('ldap_manager');

        //Stampa dettagli utenti
        /*
          $ldap->dumpUtenti(array("attribute"=>array(),"filter"=>"(&(objectClass=user)(cn=D59207))"));
          //$ldap->dumpUtenti(array("filter"=>"(&(objectClass=user))"));
         */

        //Cerca utente
        /*
          $utenti = $ldap->getUtenti();
          $risultatoricerca = $ldap->cercaUtente($utenti, "d59495");
          var_dump($risultatoricerca);exit;
         */
        
        //Tutti i dettagli di un utente
        /*
          var_dump($ldap->getUserInformation(array("username"=>"d59495")));exit;
         */

        return $this->render('FiLdapBundle:Default:index.html.twig');
    }

}
