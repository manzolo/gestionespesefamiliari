<?php

namespace Fi\SpeseBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UtenteTest extends KernelTestCase
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
    public function UtenteInsertDeleteTest()
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
        $em->persist($famiglia);
        $em->persist($utente);
        $em->flush();
        $this->assertGreaterThanOrEqual(1, $utente->getId());
        $em->remove($utente);
        $em->remove($famiglia);
        $em->flush();
        $this->assertTrue(is_null($utente->getId()));
    }
}
