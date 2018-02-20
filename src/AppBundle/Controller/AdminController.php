<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 25/01/18
 * Time: 10:06
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Image;
use AppBundle\Entity\Service;
use AppBundle\Form\ImageType;
use AppBundle\Form\MemberType;
use AppBundle\Form\ProviderType;
use AppBundle\Form\ServiceType;
use AppBundle\Service\FileUploader;
use AppBundle\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class AdminController
 * @package AppBundle\Controller
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function adminAction()
    {

        $doctrine = $this->getDoctrine();

        $repoprovider = $doctrine->getRepository('AppBundle:Provider');
        $providers = $repoprovider->findAllProvidersWithLogo();

        $repomember = $doctrine->getRepository('AppBundle:Member');
        $members = $repomember->findAllMembersWithAvatar();

        $reposervice = $doctrine->getRepository('AppBundle:Service');
        $services = $reposervice->findAll();

        return $this->render('admin/manage_users.html.twig', ['provider' => $providers, 'member' => $members, 'services' => $services]);

    }

    /**
     * @Route("/admin/ban_user/{id}", name="ban_user")
     */
    public function banUser($id, Mailer $mailer)
    {

        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->findOneById($id);

        $em = $doctrine->getManager();
        $user->setBanned(true);
        $usertype = $user->getUsertype();

        $em->persist($user);
        $em->flush();

        $mail = $user->getEmail();


        $body = "Bonjour, vous avez été banni du site annuaire bien être <br>
                           Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                           Morbi vitae sem sit amet neque commodo bibendum nec non felis.<br>
                            Integer condimentum vel tellus vitae ornare. Vivamus elementum porta lacus in placerat. 
                            Aenean vitae convallis leo. Nulla facilisi. <br>Quisque ut tincidunt neque, in pretium nisi.";
        $subject = "Compte banni";
        $mailer->sendMail($mail, $subject, $body);


        if ($usertype === 'provider') {
            $this->addFlash('success', 'Vous avez banni ' . $user->getName());
        } else {
            $this->addFlash('success', 'Vous avez banni ' . $user->getFirstName() . ' ' . $user->getLastName());
        }
        return $this->redirectToRoute('admin_users');

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/active_user/{id}", name="active_user")
     */
    public function activeUser($id, Mailer $mailer)
    {

        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->findOneById($id);
        $usertype = $user->getUsertype();

        $em = $doctrine->getManager();
        $user->setBanned(false);
        $em->persist($user);
        $em->flush();

        $mail = $user->getEmail();


        $body = "Bonjour, votre compte a été réactivé sur le site annuaire bien être <br>
                           Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                           Morbi vitae sem sit amet neque commodo bibendum nec non felis.<br>
                            Integer condimentum vel tellus vitae ornare. Vivamus elementum porta lacus in placerat. 
                            Aenean vitae convallis leo. Nulla facilisi. <br>Quisque ut tincidunt neque, in pretium nisi.";
        $subject = "Compte réactivé";
        $mailer->sendMail($mail, $subject, $body);

        if ($usertype == 'provider') {
            $this->addFlash('success', 'Vous avez activé le compte de ' . $user->getName());
        } else {
            $this->addFlash('success', 'Vous avez activé le compte de ' . $user->getFirstName() . ' ' . $user->getLastName());
        }
        return $this->redirectToRoute('admin_users');
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/update_user/{id}", name="admin_user_update")
     */
    public function updateUser(Request $request, $id)
    {

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:User');

        $user = $repo->findOneById($id);

        $usertype = $user->getUsertype();


        if ($usertype == 'provider') {

            $form = $this->createForm(ProviderType::class, $user)
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',

                    ],

                    'multiple' => true,
                    'expanded' => true,
                ]);


        } else {

            $form = $this->createForm(MemberType::class, $user)
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'update effectué avec succès');


            return $this->redirectToRoute('admin_users');
        }

        if ($usertype === 'provider') {
            return $this->render('admin/update_provider.html.twig', ['adminProviderForm' => $form->createView(), 'id' => $id]);
        } elseif ($usertype === 'member') {
            return $this->render('admin/update_member.html.twig', ['adminMemberForm' => $form->createView(), 'id' => $id]);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("admin/services", name="admin_services")
     */
    public function servicesList()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');

        $services = $repo->findServicesWithImage();

        return $this->render('admin/manage_services.html.twig', ['services' => $services]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/service/new", name="new_service")
     */
    public function newService(Request $request, FileUploader $fileUploader)
    {

        $service = new Service();


        $form = $this->createForm(ServiceType::class, $service)
            ->add('highlight', ChoiceType::class, array(
                'choices' => array(
                    'oui' => true,
                    'non' => false
                ),
                'multiple' => false,
                'expanded' => true
            ))
            ->add('valid', ChoiceType::class, array(
                'choices' => array(
                    'oui' => true,
                    'non' => false
                ),
                'multiple' => false,
                'expanded' => true
            ))
            ->add('image', ImageType::class, array(
                    'label' => ' ',
                )
            );


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $image = $service->getImage();
            $file = $image->getFile();
            $fileName = $fileUploader->upload($file);
            $image->setUrl('/bien_etre/web/uploads/images/' . $fileName);


            $em = $this->getDoctrine()->getManager();

            $em->persist($service);
            $em->flush();

            $this->addFlash('success', 'Vous avez créé le service ' . $service->getName());

            return $this->redirectToRoute('admin_services');

        }

        return $this->render('admin/new_service.html.twig', [
            'serviceForm' => $form->createView(), 'service' => $service
        ]);


    }


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/service/{id}", name="service_update")
     */
    public function updateService(Request $request, $id)
    {

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Service');

        $service = $repo->findOneById($id);


        $form = $this->createForm(ServiceType::class, $service)
            ->add('highlight', ChoiceType::class, array(
                'choices' => array(
                    'oui' => true,
                    'non' => false
                ),
                'multiple' => false,
                'expanded' => true
            ))
            ->add('valid', ChoiceType::class, array(
                'choices' => array(
                    'oui' => true,
                    'non' => false
                ),
                'multiple' => false,
                'expanded' => true
            ));


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();

            $this->addFlash('success', 'update effectué avec succès');

            return $this->redirectToRoute('admin_services');

        }

        return $this->render('admin/update_service.html.twig', [
            'serviceForm' => $form->createView(), 'id' => $id, 'service' => $service
        ]);


    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("admin/delete_service/{id}", name="delete_service")
     */
    public function deleteService($id)
    {

        $service = $this->getDoctrine()->getRepository('AppBundle:Service')->findOneById($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($service);
        $em->flush();

        $services = $this->getDoctrine()->getRepository('AppBundle:Service')->findServicesWithImage();

        $this->addFlash('success', 'Vous avez supprimé le service ' . $service->getName());

        return $this->render('admin/manage_services.html.twig', ['services' => $services]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/img_service/{id}", name="img_service")
     */
    public function imgService(Request $request, FileUploader $fileUploader, $id)
    {

        $image = new Image();
        $service = $this->getDoctrine()->getManager()->getRepository('AppBundle:Service')->findOneById($id);

        $id = $service->getId();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $file = $image->getFile();
            $fileName = $fileUploader->upload($file);
            $image->setUrl('/bien_etre/web/uploads/images/' . $fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            $service->setImage($image);

            $em = $this->getDoctrine()->getManager();
            $em->persist($service);

            $em->flush();


            return $this->redirectToRoute('admin_services');

        }

        return $this->render('admin/insertimage.html.twig', [
            'ServImgForm' => $form->createView(), 'id' => $id]);

    }


}