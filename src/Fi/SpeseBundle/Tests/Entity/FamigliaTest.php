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
    public function FamigliaInsertDeleteTest()
    {
        $em = $this->em;
        $famiglia = new \Fi\SpeseBundle\Entity\Famiglia();
        $famiglia->setDescrizione('Prova Famiglia');
        $famiglia->setDal(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));
        $em->persist($famiglia);
        $em->flush();
        $this->assertGreaterThanOrEqual(1, $famiglia->getId());
        $em->remove($famiglia);
        $em->flush();
        $this->assertTrue(is_null($famiglia->getId()));
    }
}
