<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 23/10/17
 * Time: 14:38
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProviderController extends Controller
{



    /**
     *
     * @Route("/provider/{slug}", name="show_provider")
     *
     */
    public function viewProvider($slug)
    {
        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Provider');

        $provider = $repo->findOneBy(['slug' => $slug]);

        return $this->render('providers/provider.html.twig', ['provider' => $provider]);

    }
}