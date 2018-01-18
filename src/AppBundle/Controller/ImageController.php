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

class ImageController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/image/new/{slug}", name="new_image")
     */
    public function insertImage(Request $request, $slug)
    {



        $image = new Image();
        $user = $this->getDoctrine()->getRepository('AppBundle:Provider')->findOneBy(['slug'=>$slug]);

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $image->getUrl();

            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move($this->getParameter('images'), $filename);

            $image->setUrl($filename);

            $em=$this->getDoctrine()->getManager();
            $em->persist($image);

            $em->flush();

            $user->setLogo($image);

            $em->persist($user);
            $em->flush();



            return $this->redirectToRoute("homepage");
        }

        return $this->render('images/insertimage.html.twig', [
            'imgForm'=>$form->createView(),'slug'=>$slug
        ]);
    }


}