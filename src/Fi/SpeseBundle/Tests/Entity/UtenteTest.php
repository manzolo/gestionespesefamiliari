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
    public function utenteInsertDeleteTest()
    {
        $em = $this->em;
        $famiglia = new \Fi\SpeseBundle\Entity\Famiglia();
        $nomefamiglia = 'Prova famiglia';
        $data = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        $famiglia->setDescrizione($nomefamiglia);
        $famiglia->setDal($data);
        $famiglia->setAl($data);

        $utente = new \Fi\SpeseBundle\Entity\Utente();
        $utente->setFamiglia($famiglia);

        $cognome = 'Prova Cognome';
        $nome = 'Prova Nome';
        $email = 'email@email.it';
        $username = 'Username';
        $password = 'Password';

        $utente->setCognome($cognome);
        $utente->setNome($nome);
        $utente->setEmail($email);
        $utente->setUsername($username);
        $utente->setPassword($password);
        $em->persist($famiglia);
        $em->persist($utente);
        $em->flush();

        $this->assertGreaterThanOrEqual(1, $utente->getId());
        $this->assertEquals($cognome, $utente->getCognome());
        $this->assertEquals($nome, $utente->getNome());
        $this->assertEquals($email, $utente->getEmail());
        $this->assertEquals($username, $utente->getUsername());
        $this->assertEquals($password, $utente->getPassword());

        $this->assertGreaterThanOrEqual(1, $utente->getId());

        $em->remove($utente);
        $em->remove($famiglia);
        $em->flush();
        $this->assertTrue(is_null($utente->getId()));
    }
}
