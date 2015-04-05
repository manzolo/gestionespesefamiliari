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

    public function getTipologieAction(Request $request) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('t'));
        $qb->from('FiSpeseBundle:tipologia', 't');
        $qb->leftJoin("FiSpeseBundle:categoria", 'c', 'WITH', '(t.categoria_id = c.id)');
        $qb->orderBy("c.descrizione, t.descrizione");
        $tipologie = $qb->getQuery()->getResult();

        if (count($tipologie) <= 0) {
            return new Response(json_encode(array("retcode" => -1, "message" => "Nessuna tipologia trovata")));
        } else {
            $tipologiearray = array();
            foreach ($tipologie as $tipologia) {
                $tipologiearray[] = array("id" => $tipologia->getId(), "categoria" => $tipologia->getCategoria()->getDescrizione(), "descrizione" => $tipologia->getDescrizione());
            }

            return new Response(json_encode(array("retcode" => 0, "tipologie" => $tipologiearray)));
        }
    }

    public function getTipimovimentoAction(Request $request) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('t'));
        $qb->from('FiSpeseBundle:tipomovimento', 't');
        $tipimovimento = $qb->getQuery()->getResult();

        if (count($tipimovimento) <= 0) {
            return new Response(json_encode(array("retcode" => -1, "message" => "Nessuna tipo movimento trovato")));
        } else {
            $tipimovimentoarray = array();
            foreach ($tipimovimento as $tipomovimento) {
                $tipimovimentoarray[] = array("id" => $tipomovimento->getId(), "tipo" => $tipomovimento->getTipo(), "segno" => $tipomovimento->getSegno());
            }

            return new Response(json_encode(array("retcode" => 0, "tipimovimento" => $tipimovimentoarray)));
        }
    }

    public function registraSpesaAction(Request $request) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        $json = file_get_contents('php://input');
        $obj = json_decode($json);
        $utenteid = (int) $obj->spesa->utente;
        $tipologiaid = (int) $obj->spesa->tipologia;
        $importo = (float) $obj->spesa->importo;
        $nota = $obj->spesa->nota;
        $datamovimento = $obj->spesa->datamovimento;
        $tipomovimentoid = $obj->spesa->tipomovimento;
        $utente = $em->getReference('FiSpeseBundle:utente', $utenteid);
        $tipologia = $em->getReference('FiSpeseBundle:tipologia', $tipologiaid);
        $tipomovimento = $em->getReference('FiSpeseBundle:tipomovimento', $tipomovimentoid);

        $nuovaspesa = new \Fi\SpeseBundle\Entity\movimento();
        $nuovaspesa->setUtente($utente);
        $nuovaspesa->setTipologia($tipologia);
        $nuovaspesa->setTipomovimento($tipomovimento);
        $nuovaspesa->setData(new \DateTime($datamovimento));
        $nuovaspesa->setNota($nota);
        $nuovaspesa->setImporto($importo);
        $em->persist($nuovaspesa);
        $em->flush();

        return new Response(json_encode(array("retcode" => 0, "message" => "OK")));
    }

    public function appCurrentVersionAction(Request $request) {
        $prjPath = substr($this->get('kernel')->getRootDir(), 0, -4);
        $apkFile = $prjPath . DIRECTORY_SEPARATOR . "web" . DIRECTORY_SEPARATOR . "app-release-unaligned.apk";
        $apk = new \ApkParser\Parser($apkFile);

        return new Response($apk->getManifest()->getVersionName());
    }

    public function getAppApkAction(Request $request) {
        $prjPath = substr($this->get('kernel')->getRootDir(), 0, -4);
        $apkName = $prjPath . DIRECTORY_SEPARATOR . "web" . DIRECTORY_SEPARATOR . "app-release-unaligned.apk";
        /* header('Content-Type', 'application/apk');
          header('Content-disposition: attachment; filename="' . basename($apkName) . '"');
          header('Content-Length: ' . filesize($apkName));
          return new Response(readfile($apkName)); */
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.android.package-archive');
        $response->headers->set('Content-disposition','attachment; filename="' . basename($apkName) . '"');
        $response->headers->set('Content-Length',filesize($apkName));
        $response->sendHeaders();
        $response->setContent(file_get_contents($apkName));
        return $response;
        
    }

}
