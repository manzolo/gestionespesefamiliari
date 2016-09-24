<?php

namespace Fi\SpeseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallDefaultDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('gestionespese:installdefaultdata')
                ->setDescription('Insert default data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadDefaultValues();
    }

    private function loadDefaultValues()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $today = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        $famiglia = new \Fi\SpeseBundle\Entity\Famiglia();
        $famiglia->setDescrizione('Prova famiglia');
        $famiglia->setDal($today);

        $utente = new \Fi\SpeseBundle\Entity\Utente();
        $utente->setFamiglia($famiglia);
        $utente->setCognome('Prova Cognome');
        $utente->setNome('Prova Nome');
        $utente->setEmail('email@email.it');
        $utente->setUsername('Username');
        $utente->setPassword('Password');

        $categoria = new \Fi\SpeseBundle\Entity\Categoria();
        $categoria->setDescrizione('Prova categoria');

        $tipologia = new \Fi\SpeseBundle\Entity\Tipologia();
        $tipologia->setDescrizione('Prova tipologia');
        $tipologia->setCategoria($categoria);

        $tipomovimentou = new \Fi\SpeseBundle\Entity\Tipomovimento();
        $tipomovimentou->setTipo('Uscita');
        $tipomovimentou->setAbbreviazione('U');
        $tipomovimentou->setSegno('-');

        $tipomovimentoe = new \Fi\SpeseBundle\Entity\Tipomovimento();
        $tipomovimentoe->setTipo('Entrata');
        $tipomovimentoe->setAbbreviazione('E');
        $tipomovimentoe->setSegno('+');

        $movimento = new \Fi\SpeseBundle\Entity\Movimento();
        $movimento->setData($today);
        $movimento->setImporto(10);
        $movimento->setNota('Acquisto ricarica telefonica');
        $movimento->setTipologia($tipologia);
        $movimento->setTipomovimento($tipomovimentou);
        $movimento->setUtente($utente);

        $em->persist($famiglia);
        $em->persist($utente);
        $em->persist($categoria);
        $em->persist($tipologia);
        $em->persist($tipomovimentou);
        $em->persist($tipomovimentoe);
        $em->persist($movimento);
        $em->flush();

        $menutabellegestione = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutabellegestione->setNome('Gestione');
        $menutabellegestione->setAttivo(true);
        $menutabellegestione->setOrdine(10);
        $em->persist($menutabellegestione);
        $em->flush();

        $menutabellefamiglia = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutabellefamiglia->setNome('Famiglia');
        $menutabellefamiglia->setAttivo(true);
        $menutabellefamiglia->setOrdine(50);
        $menutabellefamiglia->setPercorso('Famiglia_container');
        $menutabellefamiglia->setPadre($menutabellegestione->getId());
        $em->persist($menutabellefamiglia);

        $menutabelleutente = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutabelleutente->setNome('Utente');
        $menutabelleutente->setAttivo(true);
        $menutabelleutente->setOrdine(100);
        $menutabelleutente->setPercorso('Utente_container');
        $menutabelleutente->setPadre($menutabellegestione->getId());
        $em->persist($menutabelleutente);

        $menutabellecategoria = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutabellecategoria->setNome('Categorie');
        $menutabellecategoria->setAttivo(true);
        $menutabellecategoria->setOrdine(200);
        $menutabellecategoria->setPercorso('Categoria_container');
        $menutabellecategoria->setPadre($menutabellegestione->getId());
        $em->persist($menutabellecategoria);

        $menutabelletipologia = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutabelletipologia->setNome('Tipologie');
        $menutabelletipologia->setAttivo(true);
        $menutabelletipologia->setOrdine(300);
        $menutabelletipologia->setPercorso('Tipologia_container');
        $menutabelletipologia->setPadre($menutabellegestione->getId());
        $em->persist($menutabelletipologia);

        $menutabellemovimento = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutabellemovimento->setNome('Movimenti');
        $menutabellemovimento->setAttivo(true);
        $menutabellemovimento->setOrdine(500);
        $menutabellemovimento->setPercorso('Movimento_container');
        $menutabellemovimento->setPadre($menutabellegestione->getId());
        $em->persist($menutabellemovimento);

        $em->flush();

        $menutabelleprova = $em
                ->getRepository('FiCoreBundle:MenuApplicazione')
                ->find(1);

        $menutabelleprova->setAttivo(false);
        $em->persist($menutabelleprova);

        $em->flush();
    }
}
