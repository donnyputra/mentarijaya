<?php
namespace App\Helpers;

class StringHelper {

    public static function formatDecimalDisplay($number) {
        return number_format($number, 2, ',', '.');
    }

}