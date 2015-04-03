<?php

namespace Fi\AutenticazioneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AutenticazioneBundle:Default:index.html.twig', array('name' => $name));
    }
}
