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
        $categorie = count($json->tipologie);

        $this->assertGreaterThanOrEqual(0, $categorie);
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
        $tipimovimento = count($json->tipimovimento);

        $this->assertGreaterThanOrEqual(0, $tipimovimento);
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
}
