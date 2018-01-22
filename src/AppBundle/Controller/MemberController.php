<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 18/01/18
 * Time: 09:51
 */

namespace AppBundle\Controller;


use AppBundle\Form\MemberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends Controller
{

   /* /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/profile/member", name="update-member"   )
     *
     */
  /*  public function updateMember(Request $request)
    {

        $user = $this->getUser();



        $id = $user->getId();


        $form = $this->createForm(MemberType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'update effectué avec succès');

            return $this->redirectToRoute('homepage');

        }

        return $this->render('members/update.html.twig', [
            'memberForm' => $form->createView(), 'id' => $id
        ]);
    }*/
}