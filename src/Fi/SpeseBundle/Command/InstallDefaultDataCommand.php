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

        $famiglia = new \Fi\SpeseBundle\Entity\Famiglia();
        $famiglia->setDescrizione('Prova famiglia');
        $famiglia->setDal(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));

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

        $em->persist($famiglia);
        $em->persist($utente);
        $em->persist($categoria);
        $em->persist($tipologia);
        $em->persist($tipomovimentou);
        $em->persist($tipomovimentoe);
        $em->flush();
    }
}
