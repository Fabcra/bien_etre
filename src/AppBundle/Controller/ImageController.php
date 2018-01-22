<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 17/01/18
 * Time: 10:50
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Form\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ImageController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/image", name="new_image")
     */
    public function insertImage(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $image = new Image();
        $user = $this->getUser();
        $id = $user->getId();

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $services = $repo->findAll();



        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $image->getUrl();

            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move($this->getParameter('images'), $filename);

            $image->setUrl('../../web/uploads/images/'.$filename);

            $em=$this->getDoctrine()->getManager();
            $em->persist($image);

            $em->flush();

            if ($this->isGranted('ROLE_PROVIDER')){

                $user->setLogo($image);

            } else{
                $user->setAvatar($image);
            }

            $em->persist($user);
            $em->flush();



            return $this->redirectToRoute("update_profile");
        }

        return $this->render('images/insertimage.html.twig', [
            'imgForm'=>$form->createView(),'id'=>$id, 'services'=>$services
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/gallerie", name="img-gallery")
     */
    public function addImageGallery(Request $request){

        $user = $this->getUser();
        $id = $user->getId();

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Service');
        $services = $repo->findAll();

        $image = new Image();



        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $file = $image->getUrl();

            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('images'), $fileName
            );

            $image->setProvider($user);
            $image->setUrl('../../../web/uploads/images/'.$fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            $this->addFlash('success', 'image ajoutée');


            return $this->redirectToRoute('homepage');
        }

        return $this->render('images/insertimage_gallery.html.twig', [
            'galleryForm' => $form->createView(), 'id' => $id, 'services'=>$services]);
    }


}