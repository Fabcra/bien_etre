<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 6/11/17
 * Time: 20:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/comments/new/{id}", name="comments_new")
     * @Security("is_granted('ROLE_MEMBER')")
     */
    public function newComment(Request $request, $id){

        $newcomment = new Comment();

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Provider');

        $provider = $repo->findOneBy(['id'=>$id]);
        $member = $this->getUser();


        $form = $this->createForm(CommentType::class, $newcomment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $newcomment->setProvider($provider);
            $newcomment->setMember($member);
            $newcomment->setInsertDate(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($newcomment);
            $em->flush();

            $this->addFlash('success', 'Vous avez inséré un commentaire');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('comments/new.html.twig',[
            'commentForm'=>$form->createView(), 'id'=>$id
        ]);
    }



}