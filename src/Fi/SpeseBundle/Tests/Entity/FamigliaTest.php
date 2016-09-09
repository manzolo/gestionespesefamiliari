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
        $famiglia = new \Fi\SpeseBundle\Entity\Famiglia();
        $famiglia->setDescrizione($descrizione);
        $famiglia->setDal(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));
        $em->persist($famiglia);
        $famiglia->setAl(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));
        $em->persist($famiglia);
        $em->flush();
        $this->assertGreaterThanOrEqual(1, $famiglia->getId());

        $qu = $em->createQueryBuilder();
        $qu->select(array('f'))
            ->from('FiSpeseBundle:Famiglia', 'f')
            ->where('f.descrizione = :descrizione')
            ->setParameter('descrizione', $descrizione);
        $famigliaq = $qu->getQuery()->getSingleResult();
        $this->assertEquals($famigliaq->getDescrizione(), $descrizione);

        $em->remove($famiglia);
        $em->flush();
        $this->assertTrue(is_null($famiglia->getId()));
    }
}
