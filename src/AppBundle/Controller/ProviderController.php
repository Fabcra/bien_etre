<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 23/10/17
 * Time: 14:38
 */

namespace AppBundle\Controller;

use AppBundle\Form\ProviderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProviderController extends Controller
{


    /**
     *
     * @Route("/provider/{slug}", name="show_provider")
     *
     */
    public function viewProvider($slug)
    {
        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Provider');

        $provider = $repo->findOneBy(['slug' => $slug]);

        return $this->render('providers/provider.html.twig', ['provider' => $provider]);

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/provider/update/{slug}", name="update-provider"   )
     *
     */
    public function updateProvider(Request $request, $slug)
    {

        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:Provider')->findOneBy(['slug'=>$slug]);

        //vérification pwd
        $pwd = $user->getPassword();
        $useractif = $this->getUser();
        $pwdactif = $useractif->getPassword();


       if($pwd === $pwdactif) {

           $form = $this->createForm(ProviderType::class, $user);

           $form->handleRequest($request);

           if ($form->isSubmitted() && $form->isValid()) {

               $em = $this->getDoctrine()->getManager();
               $em->persist($user);
               $em->flush();

               $this->addFlash('success', 'update effectué avec succès');

               return $this->redirectToRoute('homepage');

           }

           return $this->render('providers/update.html.twig', [
               'providerForm' => $form->createView(), 'slug' => $slug
           ]);
       }
       else{
          return $this->redirectToRoute('homepage');
       }
    }
}