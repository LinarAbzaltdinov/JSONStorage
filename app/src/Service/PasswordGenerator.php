<?php
/**
 * Created by PhpStorm.
 * User: elama
 * Date: 21.03.18
 * Time: 17:00
 */

namespace App\Service;


class PasswordGenerator
{
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

    public static function generate(int $length)
    {
        $pass = array();
        $alphaLength = strlen(self::ALPHABET) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = self::ALPHABET[$n];
        }
        return implode($pass);
    }
}