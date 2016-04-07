<?php

namespace Fi\SpeseBundle\Twig\Extension;

class DateExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('dayNumber', array($this, 'dayNumberFilter')),
            new \Twig_SimpleFilter('dayFirstLetterName', array($this, 'dayFirstLetterNameFilter')),
            new \Twig_SimpleFilter('dayName', array($this, 'dayNameFilter')),
            new \Twig_SimpleFilter('monthName', array($this, 'monthNameFilter'))
        );
    }

    public function getFunctions() {
        return array(
             new \Twig_SimpleFunction(
                    'daysInMonth', array($this, 'getDaysInMonth')),
             new \Twig_SimpleFunction(
                    'dayWeekNumber', array($this, 'getDayWeekNumber')),
        );
    }

    public function dayNumberFilter($date) {
        $dayNumber = date('N', strtotime($date));
        return $dayNumber;
    }

    public function dayFirstLetterNameFilter($date) {
        setlocale(LC_TIME, 'it_IT.utf8');
        $dayName = strftime('%a', strtotime($date));
        setlocale(LC_TIME, NULL);
        return strtoupper(substr($dayName, 0, 1));
    }

    public function dayNameFilter($date) {
        setlocale(LC_TIME, 'it_IT.utf8');
        $dayName = strftime('%A', strtotime($date));
        setlocale(LC_TIME, NULL);
        return ucfirst($dayName);
    }

    public function monthNameFilter($monthNum) {
        $mesi=array("Gennaio","Febbraio","Marzo","Aprile","Maggio","Giugno","Luglio","Agosto","Settembre","Ottobre","Novembre","Dicembre");
        return ucfirst($mesi[$monthNum -1]);
    }

    public function getDayWeekNumber($date) {
        setlocale(LC_TIME, 'it_IT.utf8');
        $dayNum = date("N", strtotime($date));
        setlocale(LC_TIME, NULL);
        return $dayNum;
    }

    public function getDaysInMonth($mese, $anno) {
        return cal_days_in_month(CAL_GREGORIAN, $mese, $anno);
    }

    public function getName() {
        return 'date_extension';
    }

}
