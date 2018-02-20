<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 2/01/18
 * Time: 12:20
 */

namespace AppBundle\Controller;


use AppBundle\Form\UserType;
use AppBundle\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {

        $authenticationUtils = $this->get('security.authentication_utils');


        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,
            ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/password", name="update_password")
     * @Security("is_granted('ROLE_USER')")
     */
    public function modifPwd(Request $request, EncoderFactoryInterface $encoderFactory, Mailer $mailer)
    {

        $user = $this->getUser();
        $id = $user->getId();

        $pwd = $user->getPassword();


        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //vérification ancien mot de passe
            $oldpwd = $user->getOldPassword();
            if (password_verify($oldpwd, $pwd)) {

                //cyptage nouveau mot de passe
                $plainPassword = $user->getPassword();
                $encoder = $encoderFactory->getEncoder($user);
                $encoded = $encoder->encodePassword($plainPassword, '');



                $user->setPassword($encoded);



                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $mail = $user->getEmail();


                $body = "Bonjour, votre mot de passe a été modifié sur www.annuaire-bien-etre.com <br>
                           Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                           Morbi vitae sem sit amet neque commodo bibendum nec non felis.<br>
                            Integer condimentum vel tellus vitae ornare. Vivamus elementum porta lacus in placerat. 
                            Aenean vitae convallis leo. Nulla facilisi. <br>Quisque ut tincidunt neque, in pretium nisi.";
                $subject = "modification de votre mot de passe";
                $mailer->sendMail($mail, $subject, $body);

                $this->addFlash('success', 'Password modifié avec succès');

                return $this->redirectToRoute('update_profile');

            } else {
                $this->addFlash('danger', 'L\'ancien mot de passe est incorrect');
                $this->redirectToRoute('update_password');
            }
        }

        return $this->render('security/change_password.html.twig', [
            'pwdForm' => $form->createView(), 'id' => $id
        ]);


    }
}
