<?php

namespace Core\Controller\Helpers;

class Exercices
{
    /**
     * constructeur
     */
    public function __construct()
    {
    }


    public function exercice1()
    {
        return true;
    }

    public function exercice2(string $str): string
    {
        return $str;
    }

    public function exercice3(string $str1, string $str2): string
    {
        return $str1 . $str2;
    }

    public function exercice4(int $num1, int $num2): string
    {
        if ($num1 > $num2) {
            return ("Le premier nombre est plus grand");
        } elseif ($num1 < $num2) {
            return ("Le premier nombre est plus petit");
        } elseif ($num1 === $num2) {
            return ("Les deux nombres sont identiques");
        }
    }

    public function exercice5(int $num1, string $str2): string
    {
        return $num1 . $str2;
    }

    public function exercice6(string $nom, string $prenom, string $age): string
    {
        return "Bonjour " . $nom . " " . $prenom . ", tu as " . $age . " ans";
    }

    public function exercice7(int $age, string $genre): string
    {
        if ($genre == "homme" && $age >= 18) {
            return "Vous êtes un homme et vous êtes majeur";
        } elseif ($genre == "homme" && $age < 18) {
            return "Vous êtes un homme et vous êtes mineur";
        } elseif ($genre == "femme" && $age >= 18) {
            return "Vous êtes une femme et vous êtes majeure";
        } elseif ($genre == "femme" && $age < 18) {
            return "Vous êtes une femme et vous êtes mineure";
        } else {
            return "merci de choisir entre 'homme' ou 'femme'";
        }
    }
    public function exercice8(int $num1 = 0, int $num2 = 0, int $num3 = 0): int
    {
        return $num1 + $num2 + $num3;
    }

    public function exercice9(int ...$args): int
    {
        return array_sum($args);
        //return array_sum(func_get_args());
    }
}
