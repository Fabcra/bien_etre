<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 1/11/17
 * Time: 11:55
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Promotion;
use AppBundle\Form\PromotionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class PromoController extends Controller
{

    /**
     * AFFICHE LA LISTE DES PROMOTIONS
     *
     * @Route("promotions", name="promotions")
     */
    public function listPromo()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promos = $repo->findPromoWithProvidersNotBanned();


        return $this->render('promotions/promotions.html.twig', ['promotions' => $promos]);
    }


    /**
     * création d'une promotion avec génération pdf knp snappy
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("promotions/new", name="promo_new")
     * @Method({"GET", "POST"})
     */
    public function newPromo(Request $request)
    {
        $promo = new Promotion();
        $user = $this->getUser();


        $form = $this->createForm(PromotionType::class, $promo, ['method'=>'POST']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $promo->setProvider($user);
            $pdf = sha1(uniqid(mt_rand(), true)) . '.pdf';

            $promo->setPdfDoc("/bien_etre/web/uploads/files/".$pdf);


            $em = $this->getDoctrine()->getManager();

            $em->persist($promo);
            $em->flush();

            $slug = $promo->getSlug();
            $this->addFlash('success', 'Promotion ' . $promo->getName() . ' créé avec succès');

            $this->get('knp_snappy.pdf')->generate('http://localhost:8888/bien_etre/web/app_dev.php/promotions/' . $slug, 'uploads/files/' . $pdf);


            return $this->redirectToRoute('promos_gestion');
        }

        return $this->render('promotions/new.html.twig',
            ['promoForm' => $form->createView()]);
    }


    /**
     * affiche la liste des promotions pour la gestion
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/promotions/gestion", name="promos_gestion")
     *
     */
    public function managePromo()
    {

        $user = $this->getUser();
        $slug = $user->getSlug();

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promos = $user->getPromotions();

        return $this->render('promotions/manage_promo.html.twig', ['promotions' => $promos]);


    }

    /**
     * modification d'une promotion
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("promotions/update/{id}", name="promos_update")
     * @Method({"GET", "POST"})
     */
    public function updatePromo(Request $request, $id)
    {

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promo = $repo->findOneById($id);

        $user = $this->getUser();
        $current_userId = $user->getId();
        $user_id = $promo->getProvider()->getId();


        $form = $this->createForm(PromotionType::class, $promo, ['method'=>'POST']);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $promo->setProvider($user);
            $pdf = sha1(uniqid(mt_rand(), true)) . '.pdf';

            $promo->setPdfDoc("/bien_etre/web/uploads/files/".$pdf);



            $em = $this->getDoctrine()->getManager();

            $em->persist($promo);
            $em->flush();
            $slug = $promo->getSlug();
            $this->addFlash('success', 'Promo modifiée avec succès');

            $this->get('knp_snappy.pdf')->generate('http://localhost:8888/bien_etre/web/app_dev.php/promotions/' . $slug, 'uploads/files/' . $pdf);

            return $this->redirectToRoute('promos_gestion');
        }


        if ($current_userId === $user_id) {
            return $this->render('promotions/update.html.twig', [
                'promoForm' => $form->createView(), 'id' => $id,
            ]);

        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * suppression d'une promotion
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("delete_promo/{id}", name = "delete_promo")
     * @Method("DELETE")
     */
    public function deletePromo($id)
    {

        $promo = $this->getDoctrine()->getRepository('AppBundle:Promotion')->findOneById($id);


        $em = $this->getDoctrine()->getManager();

        $em->remove($promo);
        $em->flush();

        $this->addFlash('success', 'Vous avez supprimé la promotion ' . $promo->getName());
        return $this->redirectToRoute('promos_gestion');

    }


    /**
     * AFFICHE UNE PAGE PROMOTION
     *
     * @Route("promotions/{slug}", name="promotion")
     */
    public function showPromo($slug)
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promo = $repo->promoWithProvider($slug);

        return $this->render('promotions/promo.html.twig', ['promotion' => $promo]);

    }

}