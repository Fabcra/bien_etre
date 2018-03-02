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
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/users/{id}", name="admin_user_update")
     */
    public function updateUser(Request $request, Mailer $mailer, $id)
    {

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:User');

        $user = $repo->findOneById($id);

        $usertype = $user->getUsertype();

        $userwasbanned = $user->getBanned();


        if ($usertype == 'provider') {

            $form = $this->createForm(ProviderType::class, $user)
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                    ],

                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('banned', ChoiceType::class, [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false
                    ],
                    'multiple' => false,
                    'expanded' => true
                ]);


        } else {

            $form = $this->createForm(MemberType::class, $user)
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('banned', ChoiceType::class, [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false
                    ],
                    'multiple' => false,
                    'expanded' => true
                ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $userbanned = $user->getBanned();
            $mail = $user->getEmail();

            //si l'état de l'utilisateur a changé
            if ($userbanned !== $userwasbanned) {

                if ($userbanned == true) {
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
                }


                if ($userbanned == false) {

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

                }


            }

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
     * @Route("admin/services/new", name="admin_services_new")
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
                    'required'=>false
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
     * @Route("admin/services/{id}", name="admin_services_update")
     */
    public
    function updateService(Request $request, $id)
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
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/services/images/{id}", name="admin_services_images")
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

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("admin/abuses", name="admin_abuses")
     */
    public function abuseAction(){

        $doctrine = $this->getDoctrine();

     //   $repo = $doctrine->getRepository('AppBundle:Abuse');

//        $abuses = $repo->findAbuseswithMember();

        $repo = $doctrine->getRepository('AppBundle:Comment');

        $comments = $repo->findCommentsWithAbuses();

        return $this->render('admin/abuses_list.html.twig', ['comments'=>$comments]);

    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/comments/delete/{id}", name="admin_comments_delete")
     */
    public function removeComment(Mailer $mailer, $id)
    {
        $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->findOneById($id);

        $em = $this->getDoctrine()->getManager();

        $user = $comment->getMember();

        $user->setBanned(true);


        $mail = $user->getEmail();
        $body = "Suite à votre récent commentaire sur Bien-être.com, vous avez été banni des utilisateurs 
        du site.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec laoreet
         sed velit euismod pretium. Mauris eget ipsum magna. Vestibulum aliquam ultrices turpis,
          ut suscipit nisl feugiat sed. Nam non metus vel lorem viverra mattis. Duis consequat ligula
           orci, at accumsan mi ullamcorper in. Duis a turpis eget elit pretium posuere. Quisque 
           facilisis, sem sed tincidunt dapibus, nulla velit suscipit dolor, a volutpat risus er
           at id felis. Fusce imper ";

        $subject = "Suppression de votre commentaire et bannissement";

        $mailer->sendMail($mail, $subject, $body);

        $em->persist($user);
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('admin_abuses');
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/abuses/delete/{id}", name="admin_abuses_delete")
     */
    public function removeAbuse($id){

        $abuse = $this->getDoctrine()->getRepository('AppBundle:Abuse')->findOneById($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($abuse);
        $em->flush();

        return $this->redirectToRoute('admin_abuses');


    }

}