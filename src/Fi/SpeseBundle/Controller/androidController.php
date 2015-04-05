<?php

namespace Fi\SpeseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class androidController extends Controller
{
    public function loginAction(Request $request)
    {
        $username = $request->get("username");
        $password = $request->get("password");
        return new Response(json_encode(array("errcode"=>-1,"message"=>"Utente ".$username." non autorizzato")));
    }
}
