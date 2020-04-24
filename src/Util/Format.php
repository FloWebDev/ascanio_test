<?php

class Format {
    /**
     * Permet de mettre la 1ère lettre en majuscule, y compris si accentuée
     * @link https://www.php.net/manual/fr/function.ucfirst.php#57298
     * 
     * @param string $str
     * 
     * @return string
     */
    public static function mb_ucfirst($str) {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc.mb_substr($str, 1);
    }
}