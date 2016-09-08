<?php

namespace Fi\SpeseBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MovimentoTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function FamigliaInsertDeleteTest()
    {
        $em = $this->em;

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

        $nota = 'prova-'.date('Y-m-d_h:i:s');

        $movimento = new \Fi\SpeseBundle\Entity\Movimento();
        $movimento->setTipomovimento($tipomovimentoe);
        $movimento->setTipologia($tipologia);
        $movimento->setUtente($utente);
        $movimento->setData(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));
        $movimento->setImporto(0);
        $movimento->setNota($nota);
        $em->persist($movimento);
        $em->flush();

        $this->assertGreaterThanOrEqual(1, $movimento->getId());

        $em->remove($movimento);
        $em->flush();
        $this->assertTrue(is_null($movimento->getId()));
        $em->remove($utente);
        $em->remove($famiglia);
        $em->remove($categoria);
        $em->remove($tipologia);
        $em->remove($tipomovimentoe);
        $em->remove($tipomovimentou);
        $em->flush();
    }
}
