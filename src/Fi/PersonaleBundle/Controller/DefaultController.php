<?php

namespace Fi\PersonaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FiPersonaleBundle:Default:index.html.twig', array('name' => $name));
    }
}
