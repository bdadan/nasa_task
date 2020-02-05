<?php


namespace App\Service;


class DateCalculator
{
    function getHolidaysDates() {
//        $time = strtotime($date);
//        $dayOfWeek = (int)date('w',$time);
//        $year = (int)date('Y',$time);

        $year = 2019;
        #sprawdzenie czy to nie weekend
//        if( $dayOfWeek==6 || $dayOfWeek==0 ) {
//            return false;
//        }

        #lista swiat stalych
        $holiday=array(
            'nowy rok' => '01-01',
            'Trzech Króli' => '01-06',
            'święto pracy' => '05-01',
            'Święto Konstytucji 3 Maja' => '05-03',
            'Święto Wojska Polskiego' => '08-15',
            'Wszystkich Świętych' => '11-01',
            'święto Niepodległości' => '11-11',
            'Boże Narodzenie' => '12-25',
            'Boże Narodzenie dzień 2' => '12-26'
        );

        #dodanie listy swiat ruchomych
        #wialkanoc
        $easter = date('m-d', easter_date( $year ));
        #poniedzialek wielkanocny
        $easterSec = date('m-d', strtotime('+1 day', strtotime( $year . '-' . $easter) ));
        #boze cialo
        $cc = date('m-d', strtotime('+60 days', strtotime( $year . '-' . $easter) ));
        #Zesłanie Ducha Świętego
        $p = date('m-d', strtotime('+49 days', strtotime( $year . '-' . $easter) ));

        $holiday['wielkanoc'] = $easter;
        $holiday['poniedzialek wielkanocny'] = $easterSec;
        $holiday['boze cialo'] = $cc;
        $holiday['Zesłanie Ducha Świętego'] = $p;

//        $md = date('m-d',strtotime($date));
//        if(in_array($md, $holiday)) return false;

        return $holiday;
    }



}