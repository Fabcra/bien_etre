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
use AppBundle\Services\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TempUser;
use AppBundle\Form\TempUserType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


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
                ->setFrom($mail)
                ->setTo("newuser@bien_etre.com")
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
     *
     * @Route("/register/{token}/{id}/{usertype}", name="register")
     *
     */
    public function registerAction(Request $request, Message $message, $token, $id, $usertype)
    {

        $tempuser = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:TempUser')
            ->findOneBy(['id' => $id]);

        $usertype = $request->get('usertype');


        //vérification token
        if ($token === $tempuser->getToken()) {
            $mail = $tempuser->getEMail();

            if ($usertype === 'provider') {

                $user = new Provider();
                $user->setEMail($mail);
                $form = $this->createForm(ProviderType::class, $user);

            } else if ($usertype === 'member') {

                $user = new Member();
                $user->setEMail($mail);
                $form = $this->createForm(MemberType::class, $user);
            }

            $password = $tempuser->getPassword();
            $user->setPassword($password);


            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {



                $image = new Image();


                if ($usertype === 'member') {
                   $user->setRoles(['ROLE_MEMBER']);
                    $image->setUrl('http://www.rammandir.ca/sites/default/files/default_images/defaul-avatar_0.jpg');
                } else {
                    $image->setUrl('https://www.logaster.com/blog/wp-content/uploads/2013/06/jpg.png');
                    $user->setRoles(['ROLE_PROVIDER']);
                }

                $em = $this->getDoctrine()->getManager();

                $em->persist($image);
                $em->flush();
                if ($usertype === 'member') {
                    $user->setAvatar($image);
                } else {
                    $user->setLogo($image);
                }
                //

                $user->setBanned(false);
                $user->setConfirmed(true);

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
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('records/member.html.twig', [
                'form' => $form->createView()
            ]);
        }


    }


}
