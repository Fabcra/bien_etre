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
     * RENVOIE UNE LISTE DE PRESTATAIRE SUITE UTILISATION MODULE DE RECHERCHE
     *
     * @Route("/search", name="search")
     *
     */
    public function searchProviders(Request $request)
    {

        $params = $request->request->all();
        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Provider');
        $repo_service = $doctrine->getRepository('AppBundle:Service');

        $services = $repo_service->findAll();

        $providers = $repo->search($params);


        return $this->render('providers/providers.html.twig', ['providers' => $providers, 'services' => $services]);

    }

    /**
     *
     * @Route("/provider/{slug}", name="show_provider")
     *
     */
    public function viewProvider($slug)
    {
        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Provider');

        $provider = $repo->findOneBy(['slug'=>$slug]);

        return $this->render('providers/provider.html.twig', ['provider'=>$provider]);

    }
}