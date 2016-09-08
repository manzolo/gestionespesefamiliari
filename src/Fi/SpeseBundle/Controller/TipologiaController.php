<?php

namespace Fi\SpeseBundle\Controller;

use Fi\CoreBundle\Controller\FiController;
use Symfony\Component\HttpFoundation\Request;
use Fi\CoreBundle\Controller\griglia;
use Fi\SpeseBundle\Entity\tipologia;

/**
 * Tipologia controller.
 */
class TipologiaController extends FiController
{
    public function indexAction(Request $request)
    {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace.$bundle.'Bundle';

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($nomebundle.':'.$controller)->findAll();

        $dettaglij = array(
            'descrizione' => array(
                array('nomecampo' => 'descrizione',
                    'lunghezza' => '400',
                    'descrizione' => 'Descrizione tipologia',
                    'tipo' => 'text', ), ),
            'categoria_id' => array(
                array('nomecampo' => 'categoria.descrizione',
                    'lunghezza' => '400', 'descrizione' => 'Categoria',
                    'tipo' => 'text', ),
            ),
        );
        $escludi = array();
        $paricevuti = array('nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'escludere' => $escludi,
            'container' => $container, );

        $testatagriglia = griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;

        //$testatagriglia["filterToolbar_stringResult"] = false;
        //$testatagriglia["filterToolbar_searchOnEnter"] = true;
        //$testatagriglia["filterToolbar_searchOperators"] = false;
        //$testatagriglia["filterToolbar_clearSearch"] = false;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);
        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        $testata = json_encode($testatagriglia);
        $parametriTwig = array(
            'entities' => $entities,
            'nomecontroller' => $controller,
            'testata' => $testata,
        );

        return $this->render($nomebundle.':'.$controller.':index.html.twig', $parametriTwig);
    }

    public function setParametriGriglia($prepar = array())
    {
        self::setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace.$bundle.'Bundle';
        $escludi = array();
        $tabellej['categoria_id'] = array('tabella' => 'categoria', 'campi' => array('descrizione'));

        $paricevuti = array('container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'escludere' => $escludi, );

        if ($prepar) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}
