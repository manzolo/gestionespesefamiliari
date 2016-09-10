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
    public function tipologiaInsertDeleteTest()
    {
        $em = $this->em;
        $descrizinecategoria = 'Prova categoria';
        $descrizinetipologia = 'Prova tipologia';

        $categoria = new \Fi\SpeseBundle\Entity\Categoria();
        $categoria->setDescrizione($descrizinecategoria);

        $tipologia = new \Fi\SpeseBundle\Entity\Tipologia();
        $tipologia->setDescrizione($descrizinetipologia);
        $tipologia->setCategoria($categoria);

        $categoria->addTipologia($tipologia);
        $categoria->removeTipologia($tipologia);

        $em->persist($categoria);
        $em->persist($tipologia);

        $em->flush();
        $this->assertGreaterThanOrEqual(0, count($tipologia->getMovimentos()));
        $this->assertGreaterThanOrEqual(1, $tipologia->getId());
        $this->assertEquals($descrizinetipologia, $tipologia->getDescrizione());
        $this->assertEquals($descrizinecategoria, $categoria->getDescrizione());
        $em->remove($tipologia);
        $em->remove($categoria);
        $em->flush();
        $this->assertTrue(is_null($tipologia->getId()));
        $tipologia->setCategoriaId(1);
    }
}
