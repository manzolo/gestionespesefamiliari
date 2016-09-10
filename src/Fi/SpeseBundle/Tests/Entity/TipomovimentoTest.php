<?php

namespace Fi\SpeseBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TipomovimentoTest extends KernelTestCase
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
    public function tipomovimentoInsertDeleteTest()
    {
        $em = $this->em;
        $tipomovimentou = new \Fi\SpeseBundle\Entity\Tipomovimento();
        $tipomovimentou->setTipo('Uscita');
        $tipomovimentou->setAbbreviazione('U');
        $tipomovimentou->setSegno('-');

        $em->persist($tipomovimentou);
        $em->flush();
        $this->assertGreaterThanOrEqual(1, $tipomovimentou->getId());
        $this->assertEquals('Uscita', $tipomovimentou->getTipo());
        $this->assertEquals('U', $tipomovimentou->getAbbreviazione());
        $this->assertEquals('-', $tipomovimentou->getSegno());
        $this->assertEquals('Uscita', $tipomovimentou);
        $this->assertGreaterThanOrEqual(0, count($tipomovimentou->getMovimentos()));

        $em->remove($tipomovimentou);
        $em->flush();
        $this->assertTrue(is_null($tipomovimentou->getId()));
    }
}
