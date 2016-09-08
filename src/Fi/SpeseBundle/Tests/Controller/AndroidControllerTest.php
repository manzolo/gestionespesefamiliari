<?php

namespace Fi\SpeseBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AndroidControllerTest extends WebTestCase
{
    /**
 * @test 
*/
    public function AndroidControllerTest()
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
    public function AndroidControllerLoginTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/gettipologie');
        $body = $crawler->filter('body');
        $jsonString = strip_tags($body->html());
        $json = json_decode($jsonString);
        $categorie = count($json->tipologie);

        $this->assertGreaterThanOrEqual(0, $categorie);
    }
}
