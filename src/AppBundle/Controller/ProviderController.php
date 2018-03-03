<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 23/10/17
 * Time: 14:38
 */

namespace AppBundle\Controller;

use AppBundle\Entity\MailtoUser;
use AppBundle\Form\MailtoUserType;
use AppBundle\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class ProviderController extends Controller
{


    /**
     * affiche la page d'un provider
     *
     * @Route("/provider/{slug}", name="provider")
     *
     */
    public function viewProvider($slug, Request $request, Mailer $mailer)
    {
        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Provider');
        $provider = $repo->findOneBy(['slug' => $slug]);
        $provId = $provider->getId();



        $promotions = $provider->getPromotions();
        $stages = $provider->getStages();

        $comments = $provider->getComments();



        $newmail = new MailtoUser();

        $form = $this->createForm(MailtoUserType::class, $newmail);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($newmail);
            $em->flush();


            $mail = $provider->getEmail();
            $body = $newmail->getMessage();
            $subject = $newmail->getSubject();


            $mailer->sendMail($mail, $subject, $body);

            $this->addFlash('success', 'Message envoyé avec succès');
        }

        return $this->render('providers/provider.html.twig', [
            'provider' => $provider, 'promotions'=>$promotions, 'comments'=>$comments,
            'stages'=>$stages, 'mailForm' => $form->createView()]);

    }







}