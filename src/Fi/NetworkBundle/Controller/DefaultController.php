<?php

namespace Fi\NetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($ip)
    {
        $fn = new \Fi\NetworkBundle\DependencyInjection\FiNetwork($ip);
        return $this->render('FiNetworkBundle:Default:index.html.twig', array('ip' => $fn));
    }
}
