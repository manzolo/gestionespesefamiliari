<?php

namespace Fi\SpeseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class androidController extends Controller {

    public function loginAction(Request $request) {
        $username = $request->get("username");
        $password = $request->get("password");
        /* @var $em \Doctrine\ORM\EntityManager */

        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiSpeseBundle:utente', 'a');
        $qb->where('a.username = :username');
        $qb->andWhere('a.password= :password');
        $qb->setParameter('username', $username);
        $qb->setParameter('password', $password);
        $utente = $qb->getQuery()->getResult();

        if (count($utente) <= 0) {
            return new Response(json_encode(array("retcode" => -1, "message" => "Utente " . $username . " non autorizzato")));
        } else {
            $loginuser = $utente[0];
            return new Response(json_encode(array("retcode" => 0, "utente_id" => $loginuser->getId(), "famiglia_id" => $loginuser->getFamiglia()->getId(), "nominativo" => $loginuser->getNominativo())));
        }
    }

}
