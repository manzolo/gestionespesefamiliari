<?php

namespace Fi\SpeseBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoriaTest extends KernelTestCase
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
    public function categoriaInsertDeleteTest()
    {
        $em = $this->em;
        $categoria = new \Fi\SpeseBundle\Entity\Categoria();
        $descrizione = 'Prova categoria';
        $categoria->setDescrizione($descrizione);

        $em->persist($categoria);
        $em->flush();
        $this->assertGreaterThanOrEqual(0, count($categoria->getTipologias()));
        $this->assertEquals($descrizione, $categoria);
        $this->assertEquals($descrizione, $categoria->getDescrizione());
        $this->assertGreaterThanOrEqual(1, $categoria->getId());
        $em->remove($categoria);
        $em->flush();
        $em->clear();
        $this->assertTrue(is_null($categoria->getId()));
    }
}
