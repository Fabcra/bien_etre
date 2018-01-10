<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 3/01/18
 * Time: 16:06
 */

namespace AppBundle\Controller;

use AppBundle\Form\registerType;
use AppBundle\Services\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request, Message $message)
    {
        $form = $this->createForm(registerType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {

            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $msg = $message->getSuccess();

            $this->addFlash('success', $msg);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('records/register.html.twig', [
            'form' => $form->createView()
        ]);
    }



}
