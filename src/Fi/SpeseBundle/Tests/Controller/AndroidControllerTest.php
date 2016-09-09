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
        $nota = 'prova-' . date('Y-m-d_h:i:s');
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

    /**
     * @test
     */
    public function androidControllerDeletemovimentoTest() 
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
        $qtm->select(array('tm'))
            ->from('FiSpeseBundle:Tipomovimento', 'tm')
            ->where('tm.id = :id')
            ->setParameter('id', 1);
        $tipomovimentoe = $qtm->getQuery()->getSingleResult();

        $nota = 'prova-' . date('Y-m-d_h:i:s');

        $newmovimento = new \Fi\SpeseBundle\Entity\Movimento();
        $newmovimento->setUtente($utente);
        $newmovimento->setTipomovimento($tipomovimentoe);
        $newmovimento->setTipologia($tipologia);
        $newmovimento->setImporto(10);
        $newmovimento->setNota($nota);
        $newmovimento->setData(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));

        $em->persist($newmovimento);
        $em->flush();

        $post = array(
            'movimenti' => array($newmovimento->getId()),
        );

        $crawler = $client->request('POST', '/Android/deletemovimenti', $post);
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);
        $this->assertEquals(0, $json->retcode);

        $em->remove($newmovimento);
        $em->flush();
    }

    /**
     * @test
     */
    public function androidControllerUltimimovimentiTest() 
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
        $qtm->select(array('tm'))
            ->from('FiSpeseBundle:Tipomovimento', 'tm')
            ->where('tm.id = :id')
            ->setParameter('id', 1);
        $tipomovimentoe = $qtm->getQuery()->getSingleResult();

        $nota = 'prova-' . date('Y-m-d_h:i:s');

        $newmovimento = new \Fi\SpeseBundle\Entity\Movimento();
        $newmovimento->setUtente($utente);
        $newmovimento->setTipomovimento($tipomovimentoe);
        $newmovimento->setTipologia($tipologia);
        $newmovimento->setImporto(10);
        $newmovimento->setNota($nota);
        $newmovimento->setData(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));

        $em->persist($newmovimento);
        $em->flush();

        $crawler = $client->request('GET', '/Android/getultimimovimenti', array("utenteid" => $utente->getId()));
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);

        if (isset($json->retcode)) {
            $this->assertEquals(-1, $json->retcode);
        } else {
            $tipimovimento = count($json);
            $this->assertGreaterThanOrEqual(1, $tipimovimento);
        }

        $em->remove($newmovimento);
        $em->flush();
    }

    /**
     * @test
     */
    public function androidControllerGetcategorie() 
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/getcategorie');
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);

        if (isset($json->categorie)) {
            $this->assertGreaterThanOrEqual(0, count($json));
        } else {
            $this->assertEquals(-1, $json->retcode);
        }
    }

        /**
     * @test
     */
    public function androidControllergetTipimovimento() 
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/gettipimovimento');
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);

        if (isset($json->tipimovimento)) {
            $this->assertGreaterThanOrEqual(0, count($json));
        } else {
            $this->assertEquals(-1, $json->retcode);
        }
    }

    /**
     * @test
     */
    public function androidControllerGetAppApk() 
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/getAppApk');

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type', 'application/vnd.android.package-archive'
            ), 'the "Content-Type" header is "application/vnd.android.package-archive"' // optional message shown on failure
        );
    }

}
