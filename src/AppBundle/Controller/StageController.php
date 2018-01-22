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
     * AFFICHE LA PAGE D'UN STAGE
     *
     * @Route("stage/{slug}", name="show_stage")
     */
    public function showStage($slug){

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Stage');
        $stage = $repo->stageWhithProvider($slug);
        $reposervice = $doctrine->getRepository('AppBundle:Service');
        $services = $reposervice->findAll();

        return $this->render('stages/stage.html.twig',['stage'=>$stage, 'services'=>$services]);

    }

    /**
     *
     * AFFICHE LA LISTE DES STAGES
     *
     * @Route("stages/list", name="list_stages")
     */
    public function listStages()
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Stage');
        $reposervice = $doctrine->getRepository('AppBundle:Service');
        $service = $reposervice->findAll();
        $stages = $repo->findAll();

        return $this->render('stages/stages.html.twig',['stages'=>$stages, 'services'=>$service]);

    }




}