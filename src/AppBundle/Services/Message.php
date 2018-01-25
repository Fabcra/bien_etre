<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 9/01/18
 * Time: 11:30
 */

namespace AppBundle\Services;


class Message
{

    public function getSuccess()
    {
        $msg = ['Welcome ! ',
            'Bienvenue',
            'Herzlich Willkommen ! '
        ];

        $index = array_rand($msg);

        return $msg[$index];
    }

    public function getError()
    {
        $msg = 'erreur';

        return $msg;
    }


}