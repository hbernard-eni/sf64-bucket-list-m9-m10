<?php

namespace App\Service;
	
use App\Util\Censurator;

class Capitalize
{
    private Censurator $censurator;

    public function __construct(Censurator $censurator)  {
        $this->censurator = $censurator;
    }

    public function toUpper(string $s): string
    {
        //  Ne gère pas les majuscules avec les accents
        // $s = strtoupper($s);

        // Gère les majuscules avec les accents
        $s = mb_strtoupper($s);
        return $s;
    } // -- toUpper()

} // -- class