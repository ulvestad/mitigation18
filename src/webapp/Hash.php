<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{

    //Trenger ikke et eget salt når BCRYPT brukes
    //static $salt = "password";


    public function __construct()
    {
    }

    public static function make($plaintext)
    {
        //Bruker BCRYPT til hashing isteden for SHA1
        return password_hash($plaintext, PASSWORD_BCRYPT);

    }

    public function check($plaintext, $hash)
    {
        //password_verify returnerer true dersom plaintext parametret stemmer med $hash parametret
        return password_verify($plaintext, $hash);
    }

}
