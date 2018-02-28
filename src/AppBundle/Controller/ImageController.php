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
use AppBundle\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{

    /**
     * @Route("/image/new", name="image_new")
     */
    public function insertImage(Request $request, FileUploader $fileUploader)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $image = new Image();
        $user = $this->getUser();
        $id = $user->getId();

        $usertype = $user->getUsertype();


        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $file = $image->getFile();
            $filename = $fileUploader->upload($file);
            $image->setUrl('/bien_etre/web/uploads/images/' . $filename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            if ($usertype === 'provider') {

                $user->setLogo($image);
            } elseif ($usertype === 'member') {
                $user->setAvatar($image);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Image insérée avec succès');

            return $this->redirectToRoute("update_profile");
        }

        return $this->render('images/insertimage.html.twig', [
            'imgForm' => $form->createView(), 'id' => $id,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/gallery/image/new", name="gallery_image_new")
     */
    public function addImageGallery(Request $request, FileUploader $fileUploader)
    {

        $user = $this->getUser();
        $id = $user->getId();

        $gallery = $this->getDoctrine()->getRepository('AppBundle:Image')->findImagesByProvider($id);


        $image = new Image();


        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $file = $image->getFile();

            $fileName = $fileUploader->upload($file);

            $image->setProvider($user);
            $image->setUrl('/bien_etre/web/uploads/images/' . $fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            $this->addFlash('success', 'image ajoutée dans la galerie');


            return $this->redirectToRoute('gallery_image_new');
        }

        return $this->render('images/insertimage_gallery.html.twig', [
            'galleryForm' => $form->createView(), 'id' => $id, 'gallery' => $gallery]);

    }

    /**
     * @Route("/gallery/image/delete/{imgId}", name="gallery_image_delete")
     * @Method("DELETE")
     */
    public function removeImageGallery($imgId)
    {

        $em = $this->getDoctrine()->getManager();

        $image = $em->getRepository('AppBundle:Image')->find($imgId);


        $provider = $this->getUser();


        $em->remove($image);

        $em->persist($provider);
        $em->flush();


        return new Response(null, 204);


    }


}