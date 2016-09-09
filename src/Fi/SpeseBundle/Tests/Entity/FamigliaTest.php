<?php

namespace Fi\SpeseBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FamigliaTest extends KernelTestCase
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
    public function famigliaInsertDeleteTest()
    {
        $em = $this->em;
        $descrizione = 'ProvaFamiglia';
        $data = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        $cognome = 'Prova Cognome';
        $nome = 'Prova Nome';
        $email = 'email@email.it';
        $username = 'Username';
        $password = 'Password';
        $famiglia = new \Fi\SpeseBundle\Entity\Famiglia();
        $famiglia->setDescrizione($descrizione);
        $famiglia->setDal($data);
        $em->persist($famiglia);
        $famiglia->setAl($data);
        $em->persist($famiglia);
        $em->flush();
        $this->assertGreaterThanOrEqual(1, $famiglia->getId());
        $this->assertEquals($famiglia->getDal(), $data);
        $this->assertEquals($famiglia->getAl(), $data);

        $utente = new \Fi\SpeseBundle\Entity\Utente();
        $utente->setCognome($cognome);
        $utente->setNome($nome);
        $utente->setEmail($email);
        $utente->setUsername($username);
        $utente->setPassword($password);
        $em->persist($utente);
        $em->flush();
        $famiglia->addUtente($utente);
        $em->flush();
        $famiglia->removeUtente($utente);
        $em->flush();

        $qu = $em->createQueryBuilder();
        $qu->select(array('f'))
            ->from('FiSpeseBundle:Famiglia', 'f')
            ->where('f.descrizione = :descrizione')
            ->setParameter('descrizione', $descrizione);
        $famigliaq = $qu->getQuery()->getSingleResult();
        $this->assertEquals($famigliaq->getDescrizione(), $descrizione);
        $this->assertEquals($famigliaq->getDal(), $data);
        $this->assertEquals($famigliaq->getAl(), $data);

        $em->remove($famiglia);
        $em->flush();
        $this->assertTrue(is_null($famiglia->getId()));
    }
}
