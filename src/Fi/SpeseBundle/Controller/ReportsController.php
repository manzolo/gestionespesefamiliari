<?php

namespace Fi\SpeseBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use PHPExcel;
use PHPExcel_CachedObjectStorageFactory;
use PHPExcel_Settings;
use PHPExcel_Style_Fill;
use PHPExcel_Writer_Excel5;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportsController extends Controller
{
    public function indexAction(Request $request)
    {
    }

    public function excelspeseAction(Request $request)
    {
        \set_time_limit(960);
        \ini_set('memory_limit', '2048M');
        /* @var $em EntityManager */
        $em = $this->get('doctrine')->getManager();

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '8MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        //Creare un nuovo file
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // Set properties
        $objPHPExcel->getProperties()->setCreator('Andrea Manzi');
        $objPHPExcel->getProperties()->setLastModifiedBy('Andrea Manzi');
        $dbfunctions = $this->getDbfunctions($em);
        $year = $dbfunctions['year'];
        $month = $dbfunctions['month'];

        //REPORT TOTALE
        /* @var $qb QueryBuilder */
        $qb = $em->createQueryBuilder('reports');
        $selectFields = 'm.utente_id utenteid, f.descrizione descrizionefamiglia, u.nome nomeutente, u.cognome cognomeutente,  '
                .'tm.segno segnomovimento,'.$year.' anno, SUM(m.importo) as importototale';
        $qb->select($selectFields)
            ->from('FiSpeseBundle:Movimento', 'm')
            ->leftJoin('FiSpeseBundle:Tipomovimento', 'tm', 'WITH', '(m.tipomovimento_id = tm.id)')
            ->leftJoin('FiSpeseBundle:Utente', 'u', 'WITH', '(m.utente_id = u.id)')
            ->leftJoin('FiSpeseBundle:Famiglia', 'f', 'WITH', '(u.famiglia_id = f.id)')
            ->leftJoin('FiSpeseBundle:Tipologia', 't', 'WITH', '(m.tipologia_id = t.id)')
            ->leftJoin('FiSpeseBundle:Categoria', 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
            ->groupBy('utenteid, descrizionefamiglia, nomeutente, cognomeutente, segnomovimento, anno')
            ->orderby('descrizionefamiglia, m.utente,anno')
        //->setParameter('utenteid', 1)
        //->orderby('u.id')
        ;
        $reporttotale = $qb->getQuery()->getResult();

        $sheet = $objPHPExcel->getActiveSheet();
        $this->writeReportTotale($reporttotale, $sheet);
        unset($reporttotale);
        unset($sheet);

        //REPORT TOTALE PER CATEGORIA
        /* @var $qb QueryBuilder */
        $qb = $em->createQueryBuilder('reports');
        $selectFields = 'm.utente_id utenteid, f.descrizione descrizionefamiglia, u.nome nomeutente, u.cognome cognomeutente, '
                .'c.descrizione descrizionecategoria,  tm.segno segnomovimento, '.$year.' anno, SUM(m.importo) as importototale';
        $qb->select($selectFields)
            ->from('FiSpeseBundle:Movimento', 'm')
            ->leftJoin('FiSpeseBundle:Tipomovimento', 'tm', 'WITH', '(m.tipomovimento_id = tm.id)')
            ->leftJoin('FiSpeseBundle:Utente', 'u', 'WITH', '(m.utente_id = u.id)')
            ->leftJoin('FiSpeseBundle:Famiglia', 'f', 'WITH', '(u.famiglia_id = f.id)')
            ->leftJoin('FiSpeseBundle:Tipologia', 't', 'WITH', '(m.tipologia_id = t.id)')
            ->leftJoin('FiSpeseBundle:Categoria', 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
            ->groupBy('utenteid, descrizionefamiglia, nomeutente, cognomeutente,  descrizionecategoria , segnomovimento, anno')
            ->orderby('descrizionefamiglia,cognomeutente,nomeutente,anno, descrizionecategoria')
        //->setParameter('utenteid', 1)
        ;
        $reporttotalecategoria = $qb->getQuery()->getResult();
        $sheet = $objPHPExcel->createSheet();
        $this->writeReportTotaleCategoria($reporttotalecategoria, $sheet);
        unset($reporttotalecategoria);
        unset($sheet);

        //REPORT MENSILE PER CATEGORIA
        /* @var $qb QueryBuilder */
        $qb = $em->createQueryBuilder('reports');
        $selectFields = 'm.utente_id utenteid, f.descrizione descrizionefamiglia, u.nome nomeutente, u.cognome cognomeutente, '
                .'c.descrizione descrizionecategoria,  '
                .'tm.segno segnomovimento, '.$year.' anno, '.$month.' mese, SUM(m.importo) as importototale';
        $qb->select($selectFields)
            ->from('FiSpeseBundle:Movimento', 'm')
            ->leftJoin('FiSpeseBundle:Tipomovimento', 'tm', 'WITH', '(m.tipomovimento_id = tm.id)')
            ->leftJoin('FiSpeseBundle:Utente', 'u', 'WITH', '(m.utente_id = u.id)')
            ->leftJoin('FiSpeseBundle:Famiglia', 'f', 'WITH', '(u.famiglia_id = f.id)')
            ->leftJoin('FiSpeseBundle:Tipologia', 't', 'WITH', '(m.tipologia_id = t.id)')
            ->leftJoin('FiSpeseBundle:Categoria', 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
            ->groupBy('utenteid, descrizionefamiglia, nomeutente, cognomeutente, descrizionecategoria, segnomovimento, anno, mese')
            ->orderby('descrizionefamiglia,cognomeutente, nomeutente, anno desc, mese desc, descrizionecategoria')
        //->setParameter('utenteid', 1)
        ;
        $reportmensilecategoria = $qb->getQuery()->getResult();
        $sheet = $objPHPExcel->createSheet();
        $this->writeReportMensileCategoria($reportmensilecategoria, $sheet);
        unset($reportmensilecategoria);
        unset($sheet);

        //REPORT TOTALE PER TIPOLOGIA
        /* @var $qb QueryBuilder */
        $qb = $em->createQueryBuilder('reports');
        $selectFields = 'm.utente_id utenteid, f.descrizione descrizionefamiglia, u.nome nomeutente, u.cognome cognomeutente, '
                .'c.descrizione descrizionecategoria, '
                .'t.descrizione descrizionetipologia,  tm.segno segnomovimento, '.$year.' anno, SUM(m.importo) as importototale';

        $groupbyFields = 'utenteid, descrizionefamiglia, nomeutente, cognomeutente,  '
                .'descrizionecategoria , segnomovimento, descrizionetipologia, anno';
        $orderbyFields = 'descrizionefamiglia,cognomeutente,nomeutente,anno, '
                .'descrizionecategoria, descrizionetipologia';

        $qb->select($selectFields)
            ->from('FiSpeseBundle:Movimento', 'm')
            ->leftJoin('FiSpeseBundle:Tipomovimento', 'tm', 'WITH', '(m.tipomovimento_id = tm.id)')
            ->leftJoin('FiSpeseBundle:Utente', 'u', 'WITH', '(m.utente_id = u.id)')
            ->leftJoin('FiSpeseBundle:Famiglia', 'f', 'WITH', '(u.famiglia_id = f.id)')
            ->leftJoin('FiSpeseBundle:Tipologia', 't', 'WITH', '(m.tipologia_id = t.id)')
            ->leftJoin('FiSpeseBundle:Categoria', 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
            ->groupBy($groupbyFields)
            ->orderby($orderbyFields)
        //->setParameter('utenteid', 1)
        ;
        $reporttotaletipologia = $qb->getQuery()->getResult();
        $sheet = $objPHPExcel->createSheet();
        $this->writeReportTotaleTipologia($reporttotaletipologia, $sheet);
        unset($reporttotaletipologia);
        unset($sheet);

        //REPORT MENSILE PER TIPOLOGIA
        /* @var $qb QueryBuilder */
        $qb = $em->createQueryBuilder('reports');
        $selectFields = 'm.utente_id utenteid, f.descrizione descrizionefamiglia, u.nome nomeutente, u.cognome cognomeutente, '
                .'c.descrizione descrizionecategoria, '
                .'t.descrizione descrizionetipologia,  tm.segno segnomovimento, '.$year.' anno, '.$month.' mese, '
                .'SUM(m.importo) as importototale';

        $groupbyFields = 'utenteid, descrizionefamiglia, nomeutente, cognomeutente, '
                .'descrizionecategoria, descrizionetipologia, segnomovimento, anno, mese';
        $orderbyFields = 'descrizionefamiglia, cognomeutente, nomeutente, anno desc, mese desc, '
                .'descrizionecategoria, descrizionetipologia';
        $qb->select($selectFields)
            ->from('FiSpeseBundle:Movimento', 'm')
            ->leftJoin('FiSpeseBundle:Tipomovimento', 'tm', 'WITH', '(m.tipomovimento_id = tm.id)')
            ->leftJoin('FiSpeseBundle:Utente', 'u', 'WITH', '(m.utente_id = u.id)')
            ->leftJoin('FiSpeseBundle:Famiglia', 'f', 'WITH', '(u.famiglia_id = f.id)')
            ->leftJoin('FiSpeseBundle:Tipologia', 't', 'WITH', '(m.tipologia_id = t.id)')
            ->leftJoin('FiSpeseBundle:Categoria', 'c', 'WITH', '(t.categoria_id = c.id)')
                //->andWhere('u.id = :utenteid')
            ->groupBy($groupbyFields)
            ->orderby($orderbyFields)
        //->setParameter('utenteid', 1)
        ;
        $reportmensiletipologia = $qb->getQuery()->getResult();
        $sheet = $objPHPExcel->createSheet();
        $this->writeReportMensileTipologia($reportmensiletipologia, $sheet);
        unset($reportmensiletipologia);
        unset($sheet);

        $objPHPExcel->setActiveSheetIndex(0);
        //Scrittura su file
        //Si crea un oggetto
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $todaydate = date('d-m-y');
        //$todaydate = $todaydate . '-' . date("H-i-s");
        $filename = 'report';
        $filename = $filename.'-'.$todaydate;
        $filename = $filename.'.xls';
        $filename = \sys_get_temp_dir().DIRECTORY_SEPARATOR.$filename;
        if (file_exists($filename)) {
            \unlink($filename);
        }
        $objWriter->save($filename);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.basename($filename).'"');
        $response->setContent(file_get_contents($filename));

        return $response;
    }

    public function writeReportTotale($resultset, $sheet)
    {
        $sheet->setTitle('Report totale');
        // Si imposta il font
        //Times new romans
        $sheet->getDefaultStyle()->getFont()->setName('Verdana');
        $sheet->getDefaultStyle()->getFont()->setSize(10);
        //Si imposta la larghezza delle colonne
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(25);
        //Si imposta il colore dello sfondo delle celle
        $sheet->getStyle('D')->getNumberFormat()->setFormatCode('#,##0.00');

        //Colore header
        $style_header = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '#642EFE'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ),
        );
        $sheet->getStyle('A1:D1')->applyFromArray($style_header);
        $col = 0;
        //Si scrive nelle celle
        $sheet->setCellValueByColumnAndRow($col, 1, 'FAMIGLIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'UTENTE');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'ANNO');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'IMPORTO');
        $col = $col + 1;
        $sheet->getRowDimension(1)->setRowHeight(20);
        /* @var $em EntityManager */
        $row = 2;
        foreach ($resultset as $record) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionefamiglia']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['nomeutente'].' '.$record['cognomeutente']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['anno']);
            $col = $col + 1;
            $segnomovimento = ($record['segnomovimento'] == '+' ? '' : $record['segnomovimento']).$record['importototale'];
            $sheet->setCellValueByColumnAndRow($col, $row, $segnomovimento);
            $col = $col + 1;
            ++$row;
        }
    }

    public function writeReportTotaleCategoria($resultset, $sheet)
    {
        $sheet->setTitle('Report totale categoria');
        // Si imposta il font
        //Times new romans
        $sheet->getDefaultStyle()->getFont()->setName('Verdana');
        $sheet->getDefaultStyle()->getFont()->setSize(10);
        //Si imposta la larghezza delle colonne
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        //Si imposta il colore dello sfondo delle celle
        $sheet->getStyle('E')->getNumberFormat()->setFormatCode('#,##0.00');
        //Colore header
        $style_header = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '#642EFE'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ),
        );
        $sheet->getStyle('A1:E1')->applyFromArray($style_header);
        $col = 0;
        //Si scrive nelle celle
        $sheet->setCellValueByColumnAndRow($col, 1, 'FAMIGLIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'UTENTE');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'ANNO');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'CATEGORIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'IMPORTO');
        $col = $col + 1;
        $sheet->getRowDimension(1)->setRowHeight(20);
        /* @var $em EntityManager */
        $row = 2;
        foreach ($resultset as $record) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionefamiglia']);
            $col = $col + 1;

            $sheet->setCellValueByColumnAndRow($col, $row, $record['nomeutente'].' '.$record['cognomeutente']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['anno']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionecategoria']);
            $col = $col + 1;
            $segnomovimento = ($record['segnomovimento'] == '+' ? '' : $record['segnomovimento']).$record['importototale'];
            $sheet->setCellValueByColumnAndRow($col, $row, $segnomovimento);
            $col = $col + 1;
            ++$row;
        }
    }

    public function writeReportMensileCategoria($resultset, $sheet)
    {
        $sheet->setTitle('Report mensile categoria');
        // Si imposta il font
        //Times new romans
        $sheet->getDefaultStyle()->getFont()->setName('Verdana');
        $sheet->getDefaultStyle()->getFont()->setSize(10);
        //Si imposta la larghezza delle colonne
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);

        $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0.00');
        //Si imposta il colore dello sfondo delle celle
        //Colore header
        $style_header = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '#642EFE'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ),
        );
        $sheet->getStyle('A1:F1')->applyFromArray($style_header);
        $col = 0;
        //Si scrive nelle celle
        $sheet->setCellValueByColumnAndRow($col, 1, 'FAMIGLIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'UTENTE');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'ANNO');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'MESE');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'CATEGORIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'IMPORTO');
        $col = $col + 1;
        $sheet->getRowDimension(1)->setRowHeight(20);
        /* @var $em EntityManager */
        $row = 2;
        foreach ($resultset as $record) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionefamiglia']);
            $col = $col + 1;

            $sheet->setCellValueByColumnAndRow($col, $row, $record['nomeutente'].' '.$record['cognomeutente']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['anno']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $this->getMonthName($record['mese']));
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionecategoria']);
            $col = $col + 1;
            $segnomovimento = ($record['segnomovimento'] == '+' ? '' : $record['segnomovimento']).$record['importototale'];
            $sheet->setCellValueByColumnAndRow($col, $row, $segnomovimento);
            $col = $col + 1;
            ++$row;
        }
    }

    public function writeReportTotaleTipologia($resultset, $sheet)
    {
        $sheet->setTitle('Report totale tipologia');
        // Si imposta il font
        //Times new romans
        $sheet->getDefaultStyle()->getFont()->setName('Verdana');
        $sheet->getDefaultStyle()->getFont()->setSize(10);
        //Si imposta la larghezza delle colonne
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);

        $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0.00');

        //Si imposta il colore dello sfondo delle celle
        //Colore header
        $style_header = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '#642EFE'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ),
        );
        $sheet->getStyle('A1:F1')->applyFromArray($style_header);
        $col = 0;
        //Si scrive nelle celle
        $sheet->setCellValueByColumnAndRow($col, 1, 'FAMIGLIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'UTENTE');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'ANNO');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'CATEGORIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'TIPOLOGIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'IMPORTO');
        $col = $col + 1;
        $sheet->getRowDimension(1)->setRowHeight(20);
        /* @var $em EntityManager */
        $row = 2;
        foreach ($resultset as $record) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionefamiglia']);
            $col = $col + 1;

            $sheet->setCellValueByColumnAndRow($col, $row, $record['nomeutente'].' '.$record['cognomeutente']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['anno']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionecategoria']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionetipologia']);
            $col = $col + 1;
            $segnomovimento = ($record['segnomovimento'] == '+' ? '' : $record['segnomovimento']).$record['importototale'];
            $sheet->setCellValueByColumnAndRow($col, $row, $segnomovimento);
            $col = $col + 1;
            ++$row;
        }
    }

    public function writeReportMensileTipologia($resultset, $sheet)
    {
        $sheet->setTitle('Report mensile tipologia');
        // Si imposta il font
        //Times new romans
        $sheet->getDefaultStyle()->getFont()->setName('Verdana');
        $sheet->getDefaultStyle()->getFont()->setSize(10);
        //Si imposta la larghezza delle colonne
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);

        $sheet->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
        //Si imposta il colore dello sfondo delle celle
        //Colore header
        $style_header = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '#642EFE'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ),
        );
        $sheet->getStyle('A1:G1')->applyFromArray($style_header);
        $col = 0;
        //Si scrive nelle celle
        $sheet->setCellValueByColumnAndRow($col, 1, 'FAMIGLIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'UTENTE');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'ANNO');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'MESE');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'CATEGORIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'TIPOLOGIA');
        $col = $col + 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'IMPORTO');
        $col = $col + 1;
        $sheet->getRowDimension(1)->setRowHeight(20);
        /* @var $em EntityManager */
        $row = 2;
        foreach ($resultset as $record) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionefamiglia']);
            $col = $col + 1;

            $sheet->setCellValueByColumnAndRow($col, $row, $record['nomeutente'].' '.$record['cognomeutente']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['anno']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $this->getMonthName($record['mese']));
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionecategoria']);
            $col = $col + 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $record['descrizionetipologia']);
            $col = $col + 1;
            $segnomovimento = ($record['segnomovimento'] == '+' ? '' : $record['segnomovimento']).$record['importototale'];
            $sheet->setCellValueByColumnAndRow($col, $row, $segnomovimento);
            $col = $col + 1;
            ++$row;
        }
    }

    public function getMonthName($monthNum)
    {
        $mesi = array('Gennaio',
            'Febbraio',
            'Marzo',
            'Aprile',
            'Maggio',
            'Giugno',
            'Luglio',
            'Agosto',
            'Settembre',
            'Ottobre',
            'Novembre',
            'Dicembre', );

        return ucfirst($mesi[$monthNum - 1]);
    }

    public function getDbfunctions($em)
    {
        $dbtype = $em->getConnection()->getDatabasePlatform()->getName();
        if ($dbtype == 'mysql') {
            $year = 'year(m.data)';
            $month = 'month(m.data)';
        } elseif ($dbtype == 'postgresql') {
            $config = $em->getConfiguration();
            $config->addCustomStringFunction('TO_CHAR', 'DoctrineExtensions\Query\Postgresql\DateFormat');
            $year = "TO_CHAR(m.data,'YYYY')";
            $month = "TO_CHAR(m.data,'MM')";
        } elseif ($dbtype == 'sqlite') {
            $config = $em->getConfiguration();
            $config->addCustomStringFunction('YEAR', 'DoctrineExtensions\Query\Sqlite\Year');
            $config->addCustomStringFunction('MONTH', 'DoctrineExtensions\Query\Sqlite\Month');
            $year = 'year(m.data)';
            $month = 'month(m.data)';
        } else {
            $year = 'year(m.data)';
            $month = 'month(m.data)';
        }

        return array('year' => $year, 'month' => $month);
    }
}
