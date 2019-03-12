<?php

namespace App\Utils;

class Slugger
{
    /**
     * @return String
     */
    public function slugify($strToConvert) : string
    {
        $slugStr = preg_replace( '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '-', strtolower(trim(strip_tags($strToConvert))) );

        return $slugStr;
    }
}