<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Provider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

        $doctrine = $this->getDoctrine();
        $repo_providers = $doctrine->getRepository('AppBundle:Provider');
        $repo_services = $doctrine->getRepository('AppBundle:Service');

        $providers = $repo_providers->findProvidersWithLogo(); //affiche 8 providers avec logo
        $services = $repo_services->findAll();

        return $this->render('home/home.html.twig', ['providers'=>$providers, 'services'=>$services]);
    }
}
