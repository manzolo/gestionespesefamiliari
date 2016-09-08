<?php

namespace Fi\SpeseBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AndroidControllerTest extends WebTestCase {

    /** @test */
    public function AndroidControllerTest() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Android/login');
        $body = $crawler->filter('body');
        $jsonString = $body->html();
        $json = json_decode(strip_tags($jsonString));

        $this->assertEquals(-1, $json->retcode);
    }

}
