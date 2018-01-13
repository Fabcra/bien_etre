<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 3/01/18
 * Time: 16:06
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\registerType;
use AppBundle\Services\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TempUser;
use AppBundle\Form\TempUserType;


class UserController extends Controller
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/preregister", name="preregister")
     */
    public function preregister(Request $request)
    {

        $tempuser = new TempUser();

        $form = $this->createForm(TempUserType::class, $tempuser);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $tempuser
                ->setToken();


            /** @var TempUser $tempuser */
            $tempuser = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($tempuser);
            $em->flush();


            $this->addFlash('success', 'Veuillez confirmer votre inscription via le mail envoyé');

            $mail = $tempuser->getEMail();
            $password = $tempuser->getPassword();
            $usertype = $tempuser->getUserType();
            $token = $tempuser->getToken();

            $message = \Swift_Message::newInstance()
                ->setSubject("Nouvelle inscription")
                ->setFrom("newuser@bien_etre.com")
                ->setTo($mail)
                ->setBody(
                    $this->renderView('records/mail.html.twig', array('tempuser' => $tempuser)
                    ), 'text/html'
                );

            $this->get('mailer')->send($message);

            return $this->redirectToRoute('homepage');

        }

        return $this->render('records/preregister.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/register/{token}/{id}", name="register")
     */
    public function registerAction(Request $request, Message $message, $token, $id)
    {

        $tempuser = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:TempUser')
            ->findOneBy(['id' => $id]);


        //vérification token
        if ($token === $tempuser->getToken()) {
            $user = new User();

            $mail = $tempuser->getEMail();
            $user->setEMail($mail);

            $password = $tempuser->getPassword();
            $user->setPassword($password);

            $usertype = $tempuser->getUserType();
            $user->setUserType($usertype);

            $form = $this->createForm(registerType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {



                $em = $this->getDoctrine()->getManager();


                $user->setBanned(false);
                $user->setConfirmed(true);

                $em->persist($user);
                $em->flush();

                $msg = $message->getSuccess();

                $this->addFlash('success', $msg);

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('records/register.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
