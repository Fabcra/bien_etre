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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/image", name="new_image")
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
     * @Route("/image_gallery", name="img-gallery")
     */
    public function addImageGallery(Request $request, FileUploader $fileUploader)
    {

        $user = $this->getUser();
        $id = $user->getId();


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


            return $this->redirectToRoute('update_profile');
        }

        return $this->render('images/insertimage_gallery.html.twig', [
            'galleryForm' => $form->createView(), 'id' => $id]);

    }


}