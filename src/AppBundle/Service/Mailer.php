<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 11/02/18
 * Time: 14:46
 */

namespace AppBundle\Service;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Mailer
{

    private $mailer;


    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function sendMail($mail, $subject, $body)
    {

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom("administration@bien_etre.com")
            ->setTo($mail)
            ->setBody($body)
            ->setContentType("text/html");


        return $this->mailer->send($message);

    }



}
