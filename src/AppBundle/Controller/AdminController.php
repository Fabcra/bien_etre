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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
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
    public function banUser($id)
    {

        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->findOneById($id);

        $em = $doctrine->getManager();
        $user->setBanned(true);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin');

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/active_user/{id}", name="active_user")
     */
    public function activeUser($id)
    {

        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->findOneById($id);

        $em = $doctrine->getManager();
        $user->setBanned(false);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin');
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

        $roleuser = $user->getRoles();


        if ($roleuser[0] == 'ROLE_PROVIDER') {

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


            return $this->redirectToRoute('admin');
        }

        if ($roleuser[0] == 'ROLE_PROVIDER') {
            return $this->render('admin/update_provider.html.twig', ['adminProviderForm' => $form->createView(), 'id' => $id]);
        } else {
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
    public function newService(Request $request)
    {

        $service = new Service();

        $image = new Image();

        $form = $this->createForm(ServiceType::class, [$service, $image]);



        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            var_dump($form);
            die();

        }

        if ($form->isSubmitted() && $form->isValid()) {




            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();





            $file = $image->getUrl();



            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('images'), $filename
            );


            $image->setUrl('/bien_etre/web/uploads/images/' . $filename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);

            $em->flush();

            $service->setImage($image);


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
        $image = $service->getImage();

        $form = $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $service->setImage($image);
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();

            $this->addFlash('notice', 'update effectué avec succès');

            return $this->redirectToRoute('homepage');

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

        return $this->render('admin/manage_services.html.twig', ['services' => $services]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/img_service/{id}", name="img_service")
     */
    public function imgService(Request $request, $id)
    {


        $image = new Image();

        $service = $this->getDoctrine()->getManager()->getRepository('AppBundle:Service')->findOneById($id);

        $id = $service->getId();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $image->getUrl();

            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('images'), $filename
            );

            $image->setUrl('/bien_etre/web/uploads/images/' . $filename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);

            $em->flush();

            $service->setImage($image);

            $em = $this->getDoctrine()->getManager();
            $em->persist($service);

            $em->flush();

            $newform = $this->createForm(ServiceType::class, $service);

            return $this->render('admin/update_service.html.twig', [
                'serviceForm' => $newform->createView(), 'id' => $id, 'service' => $service
            ]);

        }

        return $this->render('admin/insertimage.html.twig', [
            'ServImgForm' => $form->createView(), 'id' => $id]);

    }


}