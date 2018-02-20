<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 26/10/17
 * Time: 11:31
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Stage;
use AppBundle\Form\StageType;
use function Sodium\add;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class StageController extends Controller
{
    /**
     * AFFICHE LA PAGE D'UN STAGE
     *
     * @Route("stage/{slug}", name="show_stage")
     */
    public function showStage($slug)
    {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('AppBundle:Stage');
        $stage = $repo->stageWithProvider($slug);

        return $this->render('stages/stage.html.twig', ['stage' => $stage]);

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
        $stages = $repo->findStagesWithProviderNotBanned();



        return $this->render('stages/stages.html.twig', ['stages' => $stages]);

    }


    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("new_stage", name="new_stage")
     */
    public function newStage(Request $request)
    {
        $stage = new Stage();
        $user = $this->getUser();


        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $stage->setProvider($user);

            $em = $this->getDoctrine()->getManager();

            $em->persist($stage);
            $em->flush();

            $this->addFlash('success', 'Stage '.$stage->getName() .' créé avec succès');

            return $this->redirectToRoute('manage_stages');
        }

        return $this->render('stages/new.html.twig',
            ['stageForm' => $form->createView()]);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/manage_stages", name="manage_stages")
     *
     */
    public function manageStages()
    {

        $user = $this->getUser();
        $slug = $user->getSlug();

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Stage');
        $stages = $repo->findStagesByProvider($slug);

        return $this->render('stages/manage_stage.html.twig', ['stages' => $stages]);


    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("stage/update/{id}", name="stage_update")
     */
    public function updateStage(Request $request, $id)
    {

        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository('AppBundle:Stage');
        $stage = $repo->findOneById($id);

        $user = $this->getUser();
        $current_userId = $user->getId();
        $user_id = $stage->getProvider()->getId();


        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($stage);
            $em->flush();

            $this->addFlash('success', 'Stage modifié avec succès');

            return $this->redirectToRoute('manage_stages');
        }


        if ($current_userId === $user_id) {
            return $this->render('stages/update.html.twig', [
                'stageForm' => $form->createView(), 'id' => $id,
            ]);

        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("delete_stage/{id}", name = "delete_stage")
     */
    public function deleteStage($id){

        $stage = $this->getDoctrine()->getRepository('AppBundle:Stage')->findOneById($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($stage);
        $em->flush();

        $this->addFlash('success', 'Vous avez supprimé le stage '.$stage->getName());
        return $this->redirectToRoute('manage_stages');

    }


}