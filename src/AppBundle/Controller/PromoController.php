<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 1/11/17
 * Time: 11:55
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class PromoController extends Controller
{

    /**
     * AFFICHE LA LISTE DES PROMOTIONS
     *
     * @Route("promotions/list", name="list_promos")
     */
    public function listPromo(){

        $doctrine=$this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $reposervice = $doctrine->getRepository('AppBundle:Service');
        $services = $reposervice->findAll();
        $promos = $repo->findAll();


        return $this->render('promotions/promotions.html.twig', ['promotions'=>$promos, 'services'=>$services]);
    }


    /**
     * AFFICHE UNE PAGE PROMOTION TODO: A CONVERTIR EN PDF
     *
     * @Route("promotion/{slug}", name="show_promo")
     */
    public function showPromo($slug){

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $reposervice = $doctrine->getRepository('AppBundle:Service');
        $services = $reposervice->findAll();
        $promo = $repo->promoWithProvider($slug);

        return $this->render('promotions/promo.html.twig', ['promotion'=>$promo, 'services'=>$services]);

    }
}