<?php

namespace Fi\SpeseBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AndroidControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function androidControllerTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/login');
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);

        $this->assertEquals(-1, $json->retcode);
    }

    /**
     * @test
     */
    public function androidControllerTipologieTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/gettipologie');
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);
        if (isset($json->tipologie)) {
            $categorie = count($json->tipologie);
            $this->assertGreaterThanOrEqual(0, $categorie);
        } else {
            $this->assertEquals(-1, $json->retcode);
        }
    }

    /**
     * @test
     */
    public function androidControllerTipimovimentoTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/gettipimovimento');
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);
        if (isset($json->tipimovimento)) {
            $tipimovimento = count($json->tipimovimento);
            $this->assertGreaterThanOrEqual(0, $tipimovimento);
        } else {
            $this->assertEquals(-1, $json->retcode);
        }
    }

    /**
     * @test
     */
    public function androidControllerAppCurrentVersionTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/appCurrentVersion');
        $body = $crawler->filter('body');
        $string = strip_tags($body->html());
        $isVersion = preg_match('/^(\d+\.)?(\d+\.)?(\d+\.)?(\*|\d+)$/', $string);

        $this->assertTrue((bool) $isVersion);
    }

    /**
     * @test
     */
    public function androidControllerRegistraSpesaTest()
    {
        $client = static::createClient();

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();

        $qu = $em->createQueryBuilder();
        $qu->select(array('m'))
            ->from('FiSpeseBundle:Utente', 'm')
            ->where('m.id = :id')
            ->setParameter('id', 1);
        $utente = $qu->getQuery()->getSingleResult();

        $qt = $em->createQueryBuilder();
        $qt->select(array('t'))
            ->from('FiSpeseBundle:Tipologia', 't')
            ->where('t.id = :id')
            ->setParameter('id', 1);
        $tipologia = $qt->getQuery()->getSingleResult();

        $qtm = $em->createQueryBuilder();
        $qtm->select(array('t'))
            ->from('FiSpeseBundle:Tipomovimento', 't')
            ->where('t.id = :id')
            ->setParameter('id', 1);
        $tipomovimentoe = $qt->getQuery()->getSingleResult();
        $nota = 'prova-'.date('Y-m-d_h:i:s');
        $post = array(
            'utente' => $utente->getId(),
            'tipologia' => $tipologia->getId(),
            'importo' => 10,
            'nota' => $nota,
            'datamovimento' => date('Y-m-d'),
            'tipomovimento' => $tipomovimentoe->getId(),
        );
        $crawler = $client->request('POST', '/Android/registraspesa', $post);
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);
        $this->assertEquals(0, $json->retcode);

        $qtmv = $em->createQueryBuilder();
        $qtmv->select(array('m'))
            ->from('FiSpeseBundle:Movimento', 'm')
            ->where('m.nota = :nota')
            ->setParameter('nota', $nota);

        $movimento = $qtmv->getQuery()->getSingleResult();
        $this->assertGreaterThanOrEqual(1, $movimento->getId());

        $em->remove($movimento);
        $em->flush();
    }
}
