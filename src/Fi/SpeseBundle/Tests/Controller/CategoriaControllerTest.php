<?php

namespace Fi\SpeseBundle\Tests\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\SpeseBundle\DependencyInjection\SpeseTest;

class CategoriaControllerTest extends SpeseTest
{
    /**
     * @test
     */
    public function testCategoria()
    {
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $client->request('GET', '/Categoria/');
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
