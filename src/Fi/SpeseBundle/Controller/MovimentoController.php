<?php

namespace Fi\SpeseBundle\Controller;

use Fi\CoreBundle\Controller\FiController;
use Symfony\Component\HttpFoundation\Request;
use Fi\CoreBundle\Controller\Griglia;
use Fi\SpeseBundle\Entity\movimento;

/**
 * Movimento controller.
 */
class MovimentoController extends FiController
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
            'tipomovimento_id' => array(
                array('nomecampo' => 'tipomovimento.tipo',
                    'lunghezza' => '180',
                    'descrizione' => 'Movimento',
                    'tipo' => 'text', ),
            ),
            'tipologia_id' => array(
                array('nomecampo' => 'tipologia.descrizione',
                    'lunghezza' => '180',
                    'descrizione' => 'Tipologia',
                    'tipo' => 'text', ), ),
            'categoria' => array('nomecampo' => 'categoria.descrizione',
                'lunghezza' => '180',
                'descrizione' => 'Categoria',
                'tipo' => 'text', ),
            'utente_id' => array(
                array('nomecampo' => 'utente.nome',
                    'lunghezza' => '180', 'descrizione' => 'Nome',
                    'tipo' => 'text', ),
                array('nomecampo' => 'utente.cognome',
                    'lunghezza' => '180',
                    'descrizione' => 'Cognome',
                    'tipo' => 'text', ), ),
            'data' => array(array('nomecampo' => 'data',
                    'lunghezza' => '150',
                    'descrizione' => 'Data',
                    'tipo' => 'text', )),
            'importo' => array(
                array('nomecampo' => 'importo',
                    'lunghezza' => '150',
                    'descrizione' => 'Importo',
                    'tipo' => 'text', ), ),
            'nota' => array(
                array('nomecampo' => 'nota',
                    'lunghezza' => '400',
                    'descrizione' => 'Nota',
                    'tipo' => 'text', ), ),
        );
        $escludi = array('id');
        $campiextra = array(
            array('nomecampo' => 'categoria',
                'lunghezza' => '500',
                'descrizione' => 'Categoria',
                'type' => 'string', ), );
        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'escludere' => $escludi,
            'container' => $container,
            'campiextra' => $campiextra, );

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

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

        return $this->render($nomebundle.':'.$controller.':index.html.twig', array(
                    'entities' => $entities,
                    'nomecontroller' => $controller,
                    'testata' => $testata,
        ));
    }

    public function setParametriGriglia($prepar = array())
    {
        self::setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace.$bundle.'Bundle';
        $escludi = array('id', 'utente', 'tipomovimento', 'categoria', 'tipologia');

        $tabellej['utente_id'] = array('tabella' => 'utente', 'campi' => array('nome', 'cognome'));
        $tabellej['tipomovimento_id'] = array('tabella' => 'tipomovimento', 'campi' => array('tipo'));
        $tabellej['tipologia_id'] = array('tabella' => 'tipologia', 'campi' => array('descrizione'));
        $campiextra = array('nomecampo' => 'descrizionecategoria');

        $paricevuti = array('container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'escludere' => $escludi,
            'campiextra' => $campiextra, );

        if ($prepar) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}
