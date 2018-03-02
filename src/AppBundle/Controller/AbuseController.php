<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 6/11/17
 * Time: 20:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Abuse;
use AppBundle\Form\AbuseType;
use AppBundle\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AbuseController extends Controller
{

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/abuse/new/{id}", name="abuse_new")
     */
    public function newAbuse(Request $request, Mailer $mailer, $id){

        $newabuse = new Abuse();

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Comment');
        $comment = $repo->findOneBy(['id'=>$id]);

        $editor = $this->getUser();
        $nameeditor = $editor->getFirstName();


        $form = $this->createForm(AbuseType::class, $newabuse);

        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){

            $newabuse->setMember($editor);
            $newabuse->setComment($comment);
            $newabuse->setInsertDate(new \DateTime());

            $em =$this->getDoctrine()->getManager();
            $em->persist($newabuse);
            $em->flush();

            $mail = "abuse@bien-etre.com";
            $body = $newabuse->getDescription();
            $subject = "signalement de ".$nameeditor;

            $mailer->sendMail($mail, $subject, $body);



            $this->addFlash('success', 'Votre signalement a été envoyé, un administrateur prendra la décision qu\'il conviendra');

            return $this->redirectToRoute('homepage');

        }
        return $this->render('abus/new.html.twig',[
            'abuseForm'=>$form->createView(), 'id'=>$id
        ]);





    }
}