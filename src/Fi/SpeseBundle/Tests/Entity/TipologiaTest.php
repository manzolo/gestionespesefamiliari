<?php

namespace Fi\SpeseBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TipologiaTest extends KernelTestCase
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
    public function TipologiaInsertDeleteTest()
    {
        $em = $this->em;
        $categoria = new \Fi\SpeseBundle\Entity\Categoria();
        $categoria->setDescrizione('Prova categoria');

        $tipologia = new \Fi\SpeseBundle\Entity\Tipologia();
        $tipologia->setDescrizione('Prova tipologia');
        $tipologia->setCategoria($categoria);

        $em->persist($categoria);
        $em->persist($tipologia);
        $em->flush();
        $this->assertGreaterThanOrEqual(1, $tipologia->getId());
        $em->remove($tipologia);
        $em->remove($categoria);
        $em->flush();
        $this->assertTrue(is_null($tipologia->getId()));
    }
}
