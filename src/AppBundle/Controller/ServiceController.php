<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Service;
use AppBundle\Form\ServiceType;
use AppBundle\Service\FileUploader;
use AppBundle\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ImageType;

class ServiceController extends Controller
{

    /**
     *
     * affiche la liste des services
     *
     * @Route("services", name="services")
     */
    public function listServices()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $services = $repo->findValidServicesWithImage();

        return $this->render('services/services.html.twig', ['services' => $services]);


    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * affiche la liste des services dans la barre de recherche
     */
    public function searchServices()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');

        $services = $repo->findValidServices();

        return $this->render('default/minisearchbar.html.twig', ['services' => $services]);
    }


    /**
     * demande de création d'un service
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("services/new", name="services_new")
     * @Method({"GET", "POST"})
     */
    public function newService(Request $request, FileUploader $fileUploader, Mailer $mailer)
    {

        $service = new Service();


        $form = $this->createForm(ServiceType::class, $service, ['method'=>'POST'])
            ->add('image', ImageType::class, array(
                    'label' => ' ',
                )
            )
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $service->getImage();
            $file = $image->getFile();
            $fileName = $fileUploader->upload($file);
            $image->setUrl('/bien_etre/web/uploads/files/' . $fileName);

            $em = $this->getDoctrine()->getManager();

            $service->setValid(false);
            $service->setHighlight(false);

            $em->persist($service);
            $em->flush();

            $mail = "Admin@annuaire-bien-etre.com";
            $subject = "Nouvelle inscription";
            $body = $this->renderView('services/mail.html.twig', array('service' => $service));

            $mailer->sendMail($mail, $subject, $body);

            $this->addFlash('success', 'La demande de création de service a été envoyé à un administrateur, 
            celle-ci sera traitée dans les plus brefs délais');

            return $this->redirectToRoute('update_profile');
        }

        return $this->render('services/new.html.twig',
            ['serviceForm' => $form->createView(), 'service' => $service
            ]);

    }

    /**
     *
     * affiche la description d'un service et la liste des providers liés à celui-ci
     *
     * @Route("services/{id}", name="service")
     *
     */
    public function showService(Request $request, $id)
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $repo_provider = $doctrine->getRepository('AppBundle:Provider');


        $service = $repo->findOneBy(['id' => $id]);
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
}