<?php

namespace Fi\SipoBundle\Controller;

use Fi\SipoBundle\DependencyInjection\FiSipo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

  public function indexAction($codicefiscale) {

    $estensioneSipo = new FiSipo($this->container);
    
    echo "$codicefiscale"; 
    
    var_dump($estensioneSipo->getPersona($codicefiscale));

    return $this->render('FiSipoBundle:Default:index.html.twig', array('codicefiscale' => $codicefiscale));
  }

}
