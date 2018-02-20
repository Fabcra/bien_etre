<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 9/01/18
 * Time: 19:48
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchController extends Controller {
    /**
     *
     * RENVOIE UNE LISTE DE PRESTATAIRE SUITE UTILISATION MODULE DE RECHERCHE
     *
     * @Route("/search", name="search")
     *
     */
    public function searchProviders(Request $request)
    {


        $params['by_name'] = $request->query->get('by_name');
        $params['by_location'] = $request->query->get('by_locality');
        $params['by_service'] = $request->query->get('by_service');


        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Provider');
        $repo_service = $doctrine->getRepository('AppBundle:Service');

        $services = $repo_service->findValidServices();

        $providers = $repo->search($params);


        $paginator = $this->get('knp_paginator');


        $result = $paginator->paginate(
            $providers,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 4)
        );


        return $this->render('providers/providers.html.twig',
            ['providers' => $result, 'services' => $services, 'params' => $params]);

    }
}