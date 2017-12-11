<?php

namespace Fi\SpeseBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class SpeseTest extends WebTestCase
{
    private $clientNonAutorizzato;
    private $clientAutorizzato;
    private $testclassname;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct()
    {
        $this->clientNonAutorizzato = static::createClient();
        $this->clientAutorizzato = $this->createAuthorizedClient(static::createClient());

        $this->em = $this->clientAutorizzato->getContainer()->get('doctrine')->getManager();
    }

    public function setClassName($testclassname)
    {
        $this->testclassname = $testclassname;
    }

    public function getClassName()
    {
        return $this->testclassname;
    }

    public function getEm()
    {
        return $this->em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function getClientNonAutorizzato()
    {
        return $this->clientNonAutorizzato;
    }

    public function getClientAutorizzato()
    {
        return $this->clientAutorizzato;
    }

    public static function createAuthorizedClient($client)
    {
        $container = $client->getContainer();

        $session = $container->get('session');
        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('spese.fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('spese.fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $username4test = $container->getParameter('user4test');
        $user = $userManager->findUserBy(array('username' => $username4test));
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_'.$firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
