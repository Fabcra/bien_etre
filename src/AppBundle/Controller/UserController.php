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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/preregister", name="preregister")
     */
    public function preregister(Request $request, EncoderFactoryInterface $encoderFactory)
    {

        $tempuser = new TempUser();

        $form = $this->createForm(TempUserType::class, $tempuser);

        $form->handleRequest($request);

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $services = $repo->findAll();

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

            $mail = $tempuser->getEMail();
            $password = $tempuser->getPassword();
            $usertype = $tempuser->getUserType();
            $token = $tempuser->getToken();

            $message = \Swift_Message::newInstance()
                ->setSubject("Nouvelle inscription")
                ->setFrom("inscription@bien_etre.com")
                ->setTo($mail)
                ->setBody(
                    $this->renderView('records/mail.html.twig', array('tempuser' => $tempuser)
                    ), 'text/html'
                );

            $this->get('mailer')->send($message);

            return $this->redirectToRoute('homepage');

        }

        return $this->render('records/preregister.html.twig', [
            'form' => $form->createView(),
            'services' => $services
        ]);

    }


    /**
     *
     * @Route("/register/{token}/{id}/{usertype}", name="register")
     *
     */
    public function registerAction(Request $request, Message $message, $token, $id, $usertype)
    {

        $doctrine = $this->getDoctrine();
        $tempuser = $doctrine
            ->getRepository('AppBundle:TempUser')
            ->findOneBy(['id' => $id]);

        $repo = $doctrine->getRepository('AppBundle:Service');
        $services = $repo->findAll();

        $usertype = $request->get('usertype');




        //vérification token
        if ($token === $tempuser->getToken()) {
            $mail = $tempuser->getEMail();
            $registrationdate = $tempuser->getFirstRegisterDate();

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



                $image->setUrl('/bien_etre/web/uploads/images/d2efe41d3b4679de46d8ac93b28e7795.jpg');

                if ($usertype === 'member') {
                    $user->setRoles(['ROLE_MEMBER']);
                } else {
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
                'form' => $form->createView(),
                'services' =>$services
            ]);
        } else {
            return $this->render('records/member.html.twig', [
                'form' => $form->createView(),
                'services'=>$services
            ]);
        }


    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/profile", name="update_profile"   )
     */
    public function updateUser(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $id = $user->getId();

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');

        $services = $repo->findAll();

        if ($this->isGranted('ROLE_PROVIDER')) {
            $form = $this->createForm(ProviderType::class, $user);
        } else {
            $form = $this->createForm(MemberType::class, $user);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'update effectué avec succès');

            return $this->redirectToRoute('homepage');

        }

        if ($this->isGranted('ROLE_PROVIDER')) {
            return $this->render('security/update.html.twig', [
                'providerForm' => $form->createView(), 'id' => $id, 'services' => $services
            ]);
        } else {
            return $this->render('security/update.html.twig', [
                'memberForm' => $form->createView(), 'id' => $id, 'services' => $services
            ]);
        }

    }


}
