<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 2/01/18
 * Time: 12:20
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {


        $authenticationUtils = $this->get('security.authentication_utils');


        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,
            ));

    }
}