<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends Controller
{

    /**
     *
     * affiche la description d'un service et la liste des providers liés à celui-ci
     *
     * @Route("service/{slug}", name="show_service")
     *
     */
    public function showService(Request $request, $slug)
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $repo_provider = $doctrine->getRepository('AppBundle:Provider');


        $service = $repo->findOneBy(['slug' => $slug]);
        $services = $repo->findAll();

        $id = $service->getId();


        //requête pour lister les provider de ce service
        $providers = $repo_provider->myFindBy($id);

        $paginator = $this->get('knp_paginator');

        $result = $paginator->paginate(
            $providers,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 4)
        );

        return $this->render('services/service.html.twig', ['services' => $services, 'service' => $service, 'providers' => $result, 'id' => $id]);


    }


    /**
     *
     * affiche la liste des services
     *
     * @Route("services/list", name="list_services")
     */
    public function listServices()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $services = $repo->findServicesWithImage();

        return $this->render('services/services.html.twig', ['services' => $services]);


    }


    public function searchServices()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');

        $services = $repo->findAll();

        return $this->render('default/minisearchbar.html.twig', ['services' => $services]);
    }
}