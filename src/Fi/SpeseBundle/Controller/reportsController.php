<?php

namespace Fi\SpeseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DoctrineExtensions\Query\Mysql\Month;

class reportsController extends Controller {

    public function indexAction(Request $request) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine')->getManager();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder("reports");
        $qb->select("m utenteid, u.nome nomeutente, u.cognome cognomeutente, SUM(m.importo) as importototale")
                ->from("FiSpeseBundle:movimento", 'm')
                ->leftJoin("FiSpeseBundle:utente", 'u', 'WITH', '(m.utente_id = u.id)')
                ->leftJoin("FiSpeseBundle:tipologia", 't', 'WITH', '(m.tipologia_id = t.id)')
                ->leftJoin("FiSpeseBundle:categoria", 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
                ->groupBy('m.utente_id, u.nome, u.cognome')
                //->setParameter('utenteid', 1)
                ->orderby("u.id")
                ;
        $reporttotale = $qb->getQuery()->getResult();
        
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder("reports");
        $qb->select("m utenteid, u.nome nomeutente, u.cognome cognomeutente, YEAR(m.data) anno,MONTH(m.data) mese,SUM(m.importo) as importototale")
                ->from("FiSpeseBundle:movimento", 'm')
                ->leftJoin("FiSpeseBundle:utente", 'u', 'WITH', '(m.utente_id = u.id)')
                ->leftJoin("FiSpeseBundle:tipologia", 't', 'WITH', '(m.tipologia_id = t.id)')
                ->leftJoin("FiSpeseBundle:categoria", 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
                ->groupBy('m.utente_id, u.nome, u.cognome, anno, mese')
                ->orderby("u.id")
                //->setParameter('utenteid', 1)
                ;
        $reportmensile = $qb->getQuery()->getResult();
        
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder("reports");
        $qb->select("m utenteid, u.nome nomeutente, u.cognome cognomeutente, concat(concat(c.descrizione,' -> '),t.descrizione) descrizionetipologia, YEAR(m.data) anno,MONTH(m.data) mese,SUM(m.importo) as importototale")
                ->from("FiSpeseBundle:movimento", 'm')
                ->leftJoin("FiSpeseBundle:utente", 'u', 'WITH', '(m.utente_id = u.id)')
                ->leftJoin("FiSpeseBundle:tipologia", 't', 'WITH', '(m.tipologia_id = t.id)')
                ->leftJoin("FiSpeseBundle:categoria", 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
                ->groupBy('m.utente_id, u.nome, u.cognome,  t.descrizione , anno, mese')
                ->orderby('u.id, c.descrizione,t.descrizione,anno,mese')
                //->setParameter('utenteid', 1)
                ;
        $reportmensiletipologia = $qb->getQuery()->getResult();
        
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder("reports");
        $qb->select("m utenteid, u.nome nomeutente, u.cognome cognomeutente, c.descrizione descrizionecategoria, YEAR(m.data) anno,MONTH(m.data) mese,SUM(m.importo) as importototale")
                ->from("FiSpeseBundle:movimento", 'm')
                ->leftJoin("FiSpeseBundle:utente", 'u', 'WITH', '(m.utente_id = u.id)')
                ->leftJoin("FiSpeseBundle:tipologia", 't', 'WITH', '(m.tipologia_id = t.id)')
                ->leftJoin("FiSpeseBundle:categoria", 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
                ->groupBy('m.utente_id, u.nome, u.cognome,  c.descrizione , anno, mese')
                ->orderby('u.id,c.descrizione,anno,mese')
                //->setParameter('utenteid', 1)
                ;
        $reportmensilecategoria = $qb->getQuery()->getResult();
        
        //var_dump($reportmensilecategoria);exit;
        return $this->render('FiSpeseBundle:Reports:index.html.twig', array('reporttotale' => $reporttotale, 'reportmensile'=>$reportmensile ,'reportmensiletipologia'=>$reportmensiletipologia, 'reportmensilecategoria'=>$reportmensilecategoria));
        
    }

}
