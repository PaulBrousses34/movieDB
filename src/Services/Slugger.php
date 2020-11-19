<?php

namespace App\Services;

class Slugger
{
    public function slugify($stringToConvert)
    {
        $stringToConvert = strtolower($stringToConvert);

        return preg_replace( '/\W+/', '-', trim(strip_tags($stringToConvert)));
    }
}