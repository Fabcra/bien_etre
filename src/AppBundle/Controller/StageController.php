<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 26/10/17
 * Time: 11:31
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class StageController extends Controller
{
    /**
     * @Route("stage/{slug}", name="show_stage")
     */
    public function showStage($slug){

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Stage');
        $stage = $repo->findOneBy(['slug' => $slug]);

        return $this->render('stages/stage.html.twig',['stage'=>$stage]);

    }

    /**
     * @Route("stages/list", name="list_stages")
     */
    public function listStages()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Stage');
        $stages = $repo->findAll();

        return $this->render('stages/stages.html.twig',['stages'=>$stages]);

    }




}