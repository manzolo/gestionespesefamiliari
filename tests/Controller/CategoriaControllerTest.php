<?php

namespace Fi\SpeseBundle\Tests\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\SpeseBundle\DependencyInjection\SpeseTest;

class CategoriaControllerTest extends SpeseTest
{

    /**
     * @test
     */
    public function testIndexCategoria()
    {
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Categoria_container');

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Categoria"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Categoria/';
        $clientnoauth->request('GET', $urlnoauth);
        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }

}
