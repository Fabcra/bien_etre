<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 3/01/18
 * Time: 16:06
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Entity\Member;
use AppBundle\Entity\Provider;
use AppBundle\Form\MemberType;
use AppBundle\Form\ProviderType;
use AppBundle\Service\FileUploader;
use AppBundle\Service\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TempUser;
use AppBundle\Form\TempUserType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use AppBundle\Service\Mailer;

class UserController extends Controller
{
    /**
     * création d'un utilisateur temporaire et envoi du message de préinscription
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/preregister", name="preregister")
     * @Method({"GET", "POST"})
     */
    public function preregister(Request $request, EncoderFactoryInterface $encoderFactory, Mailer $mailer)
    {

        $tempuser = new TempUser();

        $form = $this->createForm(TempUserType::class, $tempuser, ['method'=>'POST']);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $tempuser->getPassword();
            $encoder = $encoderFactory->getEncoder($tempuser);
            $encoded = $encoder->encodePassword($plainPassword, '');


            $tempuser->setToken();
            $tempuser->setPassword($encoded);


            $em = $this->getDoctrine()->getManager();
            $em->persist($tempuser);
            $em->flush();


            $this->addFlash('success', 'Veuillez confirmer votre inscription via le mail envoyé');

            //paramètres nécessaires au service Mailer
            $mail = $tempuser->getEMail();
            $subject = "Nouvelle inscription";
            $body = $this->renderView('records/mail.html.twig', array('tempuser' => $tempuser));

            //appel au service mailer
            $mailer->sendMail($mail, $subject, $body);


            return $this->redirectToRoute('homepage');

        }

        return $this->render('records/preregister.html.twig', [
            'form' => $form->createView(),
        ]);

    }


    /**
     * Confirmation de création d'un utilisateur
     *
     * @Route("/register/{token}/{id}/{usertype}", name="register")
     * @Method({"GET", "POST"})
     */
    public function registerAction(Request $request, Message $message, $token, $id)
    {

        $doctrine = $this->getDoctrine();
        $tempuser = $doctrine
            ->getRepository('AppBundle:TempUser')
            ->findOneBy(['id' => $id]);



        $usertype = $request->get('usertype');



        //vérification token
        if ($token === $tempuser->getToken()) {
            $mail = $tempuser->getEMail();
            $registrationdate = $tempuser->getFirstRegisterDate();

            if ($usertype === 'provider') {

                $user = new Provider();
                $user->setRoles(['ROLE_PROVIDER']);
                $user->setEMail($mail);
                $user->setUsertype('provider');
                $form = $this->createForm(ProviderType::class, $user, ['method'=>'POST']);

            } else if ($usertype === 'member') {

                $user = new Member();
                $user->setRoles(['ROLE_MEMBER']);
                $user->setEMail($mail);
                $user->setUsertype('member');
                $form = $this->createForm(MemberType::class, $user, ['method'=>'POST']);
            }

            $password = $tempuser->getPassword();
            $user->setPassword($password);


            $form->handleRequest($request);




            if ($form->isSubmitted() && $form->isValid()) {


                $image = new Image();


                $image->setUrl('/bien_etre/web/assets/img/default.jpg');

                $em = $this->getDoctrine()->getManager();

                $em->persist($image);
                $em->flush();
                if ($usertype === 'member') {
                    $user->setAvatar($image);
                } else {
                    $user->setLogo($image);
                }

                $user->setBanned(false);
                $user->setConfirmed(true);
                $user->setRegistrationDate($registrationdate);

                //suppression de l'utilisateur temporaire
                $em->remove($tempuser);

                $em->persist($user);
                $em->flush();

                //log in après inscription
                $mytoken = new UsernamePasswordToken(
                    $user,
                    $password,
                    'main',
                    $user->getRoles()
                );

                $this->get('security.token_storage')->setToken($mytoken);
                $this->get('session')->set('_security_main', serialize($mytoken));

                $msg = $message->getSuccess();
                $this->addFlash('success', $msg);


                return $this->redirectToRoute('homepage', array('slug' => $user->getSlug()));
            }
        }

        if ($usertype === "provider") {
            return $this->render('records/provider.html.twig', [
                'providerForm' => $form->createView()
            ]);
        } elseif ($usertype === 'member') {
            return $this->render('records/member.html.twig', [
                'memberForm' => $form->createView()
            ]);
        }


    }

    /**
     * modification profile
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/profile", name="update_profile")
     * @Method({"GET", "POST"})
     */
    public function updateUser(Request $request, FileUploader $fileUploader)
    {

        $user = $this->getUser();


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $user->getId();


        $usertype = $user->getUsertype();



        if ($usertype === 'provider') {

            $form = $this->createForm(ProviderType::class, $user, ['method'=>'POST']);

        } elseif ($usertype === 'member') {
            $form = $this->createForm(MemberType::class, $user, ['method'=>'POST']);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();


            $this->addFlash('success', 'update effectué avec succès');


        }

        if ($usertype ==='provider') {
            return $this->render('security/update.html.twig', [
                'providerForm' => $form->createView(), 'id' => $id,
            ]);
        } elseif ($usertype ==='member') {
            return $this->render('security/update.html.twig', [
                'memberForm' => $form->createView(), 'id' => $id,
            ]);
        }

    }

}
