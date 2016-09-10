<?php

namespace Fi\SpeseBundle\Tests\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\SpeseBundle\DependencyInjection\SpeseTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

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

    /*
     * @test
     */
    /*    public function testAddCategoria()
      {
      parent::__construct();
      $this->setClassName(get_class());
      $browser = 'firefox';
      //$url = $client->getContainer()->get('router')->generate('Categoria_container');
      $url = 'http://127.0.0.1:8000/app_test.php/Categoria';

      // Choose a Mink driver. More about it in later chapters.
      $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
      $session = new \Behat\Mink\Session($driver);
      // start the session
      $session->start();
      $session->visit($url);
      $page = $session->getPage();
      $page->fillField('username', 'admin');
      $page->fillField('password', 'admin');
      $page->pressButton('_submit');
      //$page = $session->getPage();

      $element = $page->findAll('css', '.ui-icon-plus');

      foreach ($element as $e) {
      if ($e->isVisible()) {
      $e->click();
      }
      }

      sleep(1);
      $descrizionetest = 'CategoriaProva';
      $page->fillField('fi_spesebundle_categoria_descrizione', $descrizionetest);
      $page->find('css', 'a#sDataCategoriaS')->click();
      sleep(1);
      $session->stop();

      $client = $this->getClientAutorizzato();
      // @var $em \Doctrine\ORM\EntityManager
      $em = $client->getContainer()->get('doctrine')->getManager();

      $qu = $em->createQueryBuilder();
      $qu->select(array('c'))
      ->from('FiSpeseBundle:Categoria', 'c')
      ->where('c.descrizione = :descrizione')
      ->setParameter('descrizione', $descrizionetest);
      $categoria = $qu->getQuery()->getSingleResult();
      $this->assertEquals($categoria->getDescrizione(), $descrizionetest);

      $em->remove($categoria);
      $em->flush();
      $em->clear();
      $this->assertTrue(is_null($categoria->getId()));

      //$session->wait(5000, "$('.ui-icon-plus').visible");
      //$findName = $page->find("css", ".ui-icon-plus");
      //$findName->click();
      //$session->evaluateScript("$('#grid1').jqGrid('getGridParam', 'selrow')");
      //$findName = $page->find("css", "#addjqgridrow");
      //$findName->click();
      //$session->evaluateScript("jQuery('#addjqgridrow');");
      //$js = 'jQuery("#list1").jqGrid("addRow","new");';
      //$session->evaluateScript($js);
      //$page->pressButton("jQuery('#list1').jqGrid('addjqgridrow');");
      //$session->stop();
      } */
}
