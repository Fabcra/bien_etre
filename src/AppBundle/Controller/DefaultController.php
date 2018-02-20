<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Service\Mailer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Mailer $mailer)
    {

        $doctrine = $this->getDoctrine();

        $user = $this->getUser();

        //deconnexion automatique d'un compte banni
        if (!empty($user) ){
            $banned = $user->getBanned();
            if ($banned === true) {


                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $mail = $user->getEmail();
                $body = "Bonjour, vous avez tenté de vous connecter à annuaire-bien-etre.com, sachez que vous avez été banni de ce site. <br>
                           Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                           Morbi vitae sem sit amet neque commodo bibendum nec non felis.<br>
                            Integer condimentum vel tellus vitae ornare. Vivamus elementum porta lacus in placerat. 
                            Aenean vitae convallis leo. Nulla facilisi. <br>Quisque ut tincidunt neque, in pretium nisi.";
                $subject = "Compte banni";
                $mailer->sendMail($mail, $subject, $body);


               return $this->redirectToRoute('logout');
            }
        }


        $repo_providers = $doctrine->getRepository('AppBundle:Provider');
        $repo_services = $doctrine->getRepository('AppBundle:Service');

        $providers = $repo_providers->findProvidersWithLogo(); //affiche 8 providers avec logo
        $services = $repo_services->findValidServices();// affiche les services valides
        $providers_highlight_services = $repo_providers->findProvidersWithHighlightedServices();




        return $this->render('home/home.html.twig', ['providers'=>$providers, 'services'=>$services,
            'highlight'=>$providers_highlight_services]);
    }
}
