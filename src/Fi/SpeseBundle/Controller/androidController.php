<?php

namespace Fi\SpeseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class androidController extends Controller
{
    public function loginAction(Request $request)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
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
            $userarray = array('retcode' => -1, 'message' => 'Utente '.$username.' non autorizzato o password errata');
            $response = json_encode($userarray);

            return new Response($response);
        } else {
            $loginuser = $utente[0];
            $userarray = array('retcode' => 0,
                'utente_id' => $loginuser->getId(),
                'famiglia_id' => $loginuser->getFamiglia()->getId(),
                'nominativo' => $loginuser->getNominativo(), );
            $response = json_encode($userarray);

            return new Response($response);
        }
    }

    public function getTipologieAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('t'));
        $qb->from('FiSpeseBundle:tipologia', 't');
        $qb->leftJoin('FiSpeseBundle:categoria', 'c', 'WITH', '(t.categoria_id = c.id)');
        $qb->orderBy('c.descrizione, t.descrizione');
        $tipologie = $qb->getQuery()->getResult();

        if (count($tipologie) <= 0) {
            return new Response(json_encode(array('retcode' => -1, 'message' => 'Nessuna tipologia trovata')));
        } else {
            $tipologiearray = array();
            foreach ($tipologie as $tipologia) {
                $tipologiearray[] = array('id' => $tipologia->getId(),
                    'categoria' => $tipologia->getCategoria()->getDescrizione(),
                    'categoria_id' => $tipologia->getCategoria()->getId(),
                    'descrizione' => $tipologia->getDescrizione(),
                );
            }

            return new Response(json_encode(array('retcode' => 0, 'tipologie' => $tipologiearray)));
        }
    }

    public function getTipimovimentoAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('t'));
        $qb->from('FiSpeseBundle:tipomovimento', 't');
        $tipimovimento = $qb->getQuery()->getResult();

        if (count($tipimovimento) <= 0) {
            return new Response(json_encode(array('retcode' => -1, 'message' => 'Nessuna tipo movimento trovato')));
        } else {
            $tipimovimentoarray = array();
            foreach ($tipimovimento as $tipomovimento) {
                $tipimovimentoarray[] = array('id' => $tipomovimento->getId(),
                    'tipo' => $tipomovimento->getTipo(),
                    'segno' => $tipomovimento->getSegno(), );
            }

            return new Response(json_encode(array('retcode' => 0, 'tipimovimento' => $tipimovimentoarray)));
        }
    }

    public function registraSpesaAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        $utenteid = (int) $request->request->get('utente');
        $tipologiaid = (int) $request->request->get('tipologia');
        $importo = (float) $request->request->get('importo');
        $nota = $request->request->get('nota');
        $datamovimento = $request->request->get('datamovimento');
        $tipomovimentoid = $request->request->get('tipomovimento');

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

        return new Response(json_encode(array('retcode' => 0, 'message' => 'OK')));
    }

    public function appCurrentVersionAction(Request $request)
    {
        $prjPath = substr($this->get('kernel')->getRootDir(), 0, -4);
        $apkFile = $prjPath.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'gestionespesefamiliari.apk';
        $version = '0.0';
        if (file_exists($apkFile)) {
            $apk = new \ApkParser\Parser($apkFile);
            $version = $apk->getManifest()->getVersionName();
        }

        return new Response($version);
    }

    public function getAppApkAction(Request $request)
    {
        $prjPath = substr($this->get('kernel')->getRootDir(), 0, -4);
        $apkFile = $prjPath.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'gestionespesefamiliari.apk';
        /* header('Content-Type', 'application/apk');
          header('Content-disposition: attachment; filename="' . basename($apkName) . '"');
          header('Content-Length: ' . filesize($apkName));
          return new Response(readfile($apkName)); */
        if (file_exists($apkFile)) {
            $response = new Response();
            $response->headers->set('Content-Type', 'application/vnd.android.package-archive');
            $response->headers->set('Content-disposition', 'attachment; filename="'.basename($apkFile).'"');
            $response->headers->set('Content-Length', filesize($apkFile));
            $response->sendHeaders();
            $response->setContent(file_get_contents($apkFile));

            return $response;
        } else {
            $response = new Response('Nessun apk disponibile al momento');

            return $response;
        }
    }

    public function getCategorieAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('c'));
        $qb->from('FiSpeseBundle:categoria', 'c');
        $qb->orderBy('c.descrizione');
        $categorie = $qb->getQuery()->getResult();

        if (count($categorie) <= 0) {
            return new Response(json_encode(array('retcode' => -1, 'message' => 'Nessuna categoria trovata')));
        } else {
            $categoriearray = array();
            foreach ($categorie as $categoria) {
                $categoriearray[] = array('id' => $categoria->getId(), 'descrizione' => $categoria->getDescrizione());
            }

            return new Response(json_encode(array('retcode' => 0, 'categorie' => $categoriearray)));
        }
    }

    public function getUltimiMovimentiAction(Request $request)
    {
        $utenteid = $request->get('utenteid');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('m'));
        $qb->from('FiSpeseBundle:movimento', 'm');
        $qb->where('m.utente_id = :uteteid');
        $qb->orderby('m.id', 'desc');
        $qb->setMaxResults(10);
        $qb->setParameter('uteteid', $utenteid);
        $movimenti = $qb->getQuery()->getResult();

        if (count($movimenti) <= 0) {
            return new Response(json_encode(array('retcode' => -1, 'message' => 'Nessuna movimento trovato')));
        } else {
            $movimentiarray = array();
            foreach ($movimenti as $movimento) {
                $tipologia = $movimento->getTipologia()->__toString();
                $datamovimento = $movimento->getData()->format('d/m/Y');
                $importo = number_format($movimento->getImporto(), 2, '.', ',');
                $nota = ($movimento->getNota() ? ', '.$movimento->getNota() : '');
                $descrizione = $tipologia.' '.$datamovimento.' '.$importo.'€'.$nota;
                $movimentiarray[] = array('id' => $movimento->getId(),
                    'descrizione' => $descrizione, );
            }

            return new Response(json_encode($movimentiarray));
        }
    }

    public function deleteMovimentiAction(Request $request)
    {
        $movimenti = $request->request->get('movimenti');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        foreach ($movimenti as $movimento) {
            /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $em->createQueryBuilder();
            $qb->delete();
            $qb->from('FiSpeseBundle:movimento', 'm');
            $qb->where('m.id = :movimentoid');
            $qb->setParameter('movimentoid', (int) $movimento);
            $qb->getQuery()->execute();
        }

        return new Response(json_encode(array('retcode' => 0, 'message' => 'OK')));
    }
}
