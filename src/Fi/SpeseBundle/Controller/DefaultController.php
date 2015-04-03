<?php

namespace Fi\SpeseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FiSpeseBundle:Default:index.html.twig', array('name' => $name));
    }
}
