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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class PromoController extends Controller
{

    /**
     * AFFICHE LA LISTE DES PROMOTIONS
     *
     * @Route("promotions/list", name="list_promos")
     */
    public function listPromo()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promos = $repo->findPromoWithProvidersNotBanned();


        return $this->render('promotions/promotions.html.twig', ['promotions' => $promos]);
    }


    /**
     * AFFICHE UNE PAGE PROMOTION TODO: A CONVERTIR EN PDF
     *
     * @Route("promotion/{slug}", name="show_promo")
     */
    public function showPromo($slug)
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promo = $repo->promoWithProvider($slug);

        return $this->render('promotions/promo.html.twig', ['promotion' => $promo]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("new_promo", name="new_promo")
     */
    public function newPromo(Request $request)
    {
        $promo = new Promotion();
        $user = $this->getUser();


        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $promo->setProvider($user);

            $em = $this->getDoctrine()->getManager();

            $em->persist($promo);
            $em->flush();

            $this->addFlash('success', 'Promotion ' . $promo->getName() . ' créé avec succès');

            return $this->redirectToRoute('manage_promos');
        }

        return $this->render('promotions/new.html.twig',
            ['promoForm' => $form->createView()]);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/manage_promos", name="manage_promos")
     *
     */
    public function managePromo()
    {

        $user = $this->getUser();
        $slug = $user->getSlug();

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promos = $repo->findPromoByProvider($slug);

        return $this->render('promotions/manage_promo.html.twig', ['promotions' => $promos]);


    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("promo/update/{id}", name="promo_update")
     */
    public function updatePromo(Request $request, $id)
    {

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promo = $repo->findOneById($id);

        $user = $this->getUser();
        $current_userId = $user->getId();
        $user_id = $promo->getProvider()->getId();


        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($promo);
            $em->flush();

            $this->addFlash('success', 'Promo modifiée avec succès');

            return $this->redirectToRoute('manage_promos');
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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("delete_promo/{id}", name = "delete_promo")
     */
    public function deletePromo($id){

        $promo = $this->getDoctrine()->getRepository('AppBundle:Promotion')->findOneById($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($promo);
        $em->flush();

        $this->addFlash('success', 'Vous avez supprimé la promotion '.$promo->getName());
        return $this->redirectToRoute('manage_promos');

    }

}