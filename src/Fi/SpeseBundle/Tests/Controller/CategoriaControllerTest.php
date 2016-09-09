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
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Categoria_container');
        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Categoria"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');
    }

    /**
     * @test
     */
    public function testAddCategoria() 
    {
        /* parent::__construct();
          $this->setClassName(get_class());
          $client = $this->getClientAutorizzato();
          $driver = new \Behat\Mink\Driver\BrowserKitDriver($client);

          $session = new \Behat\Mink\Session($driver);

          $session->start();
          $url = $client->getContainer()->get('router')->generate('Categoria_container');
          $session->visit($url);
          echo $session->evaluateScript("jQuery('#addjqgridrow')");
         */
    }

}
