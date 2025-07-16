<?php

namespace App\Helpers;

class NumberHelper
{
    public static function numberToWords($number)
    {
        $units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $teens = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $tens = ['', '', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];

        if ($number == 0) return 'Nol';
        if ($number < 10) return $units[$number];
        if ($number < 20) return $teens[$number - 10];
        if ($number < 100) {
            $unit = $number % 10;
            $ten = floor($number / 10);
            return $tens[$ten] . ($unit ? ' ' . $units[$unit] : '');
        }
        if ($number == 100) return 'Seratus';
        return '';
    }
}
