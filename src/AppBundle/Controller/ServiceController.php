<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ServiceController extends Controller {

    /**
     *
     * affiche la description d'un service et la liste des providers liés à celui-ci
     *
     * @Route("service/{slug}", name="show_service")
     *
     */
    public function showService($slug){

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $repo_provider = $doctrine->getRepository('AppBundle:Provider');


        $service = $repo->findOneBy(['slug'=>$slug]);

        $id = $service->getId();


        $providers = $repo_provider->myFindBy($id);



        return $this->render('services/description.html.twig', ['service'=>$service,  'providers'=>$providers]);


    }


    /**
     *
     * affiche la liste des services
     *
     * @Route("services/list", name="list_services")
     */
    public function listServices(){

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $services = $repo->findAll();

        return $this->render('services/services.html.twig',['services'=>$services]);


    }

}