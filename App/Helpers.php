<?php


namespace App;


class Helpers
{
    public static function getBalancePeriodTranslations(){


        $periodsTranslations = [
            'current-month' => 'Bieżący miesiąc',
            'last-month' => 'Poprzedni miesiąc',
            'current-year' => 'Bieżący rok',
            'custom' => 'Niestandardowy',
        ];

        return $periodsTranslations;
    }

}