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
     * @Route("promotions/list", name="list_promos")
     */
    public function listPromo(){

        $doctrine=$this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promos = $repo->findAll();


        return $this->render('promotions/promotions.html.twig', ['promotions'=>$promos]);
    }


    /**
     *
     * @Route("promotion/{slug}", name="show_promo")
     */
    public function showPromo($slug){

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Promotion');
        $promo = $repo->findOneBy(['slug' => $slug]);

        return $this->render('promotions/promo.html.twig', ['promotion'=>$promo]);

    }
}