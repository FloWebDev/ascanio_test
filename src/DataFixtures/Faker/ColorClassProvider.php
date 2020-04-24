<?php

namespace App\DataFixtures\Faker;

/**
 * Création d'un provider pour la génération de couleurs associées à des class Bootstrap
 * 
 * @link https://github.com/fzaninotto/Faker#user-content-faker-internals-understanding-providers
 */
class ColorClassProvider extends \Faker\Provider\Base {
  
    private static $colors = [
        'warning',
        'info',
        'primary',
        'success',
        'danger'
    ];
  
    /**
     * Permet d'obtenir une couleur par son numéro de clé
     * ou aléatoirement si null
     * 
     * @param int|null $i
     * 
     * @return string
     */
    public static function getColorClass(int $i = null) {
        if (is_null($i) || $i < 0 || $i >= count(self::$colors)) {
            shuffle(self::$colors);
            $color = self::$colors[0];
        } else {
            $color = self::$colors[$i];
        }

        return $color;
    }
}