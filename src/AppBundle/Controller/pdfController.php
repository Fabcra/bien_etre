<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 2/03/18
 * Time: 22:46
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Pdf;
use AppBundle\Form\PdfType;
use AppBundle\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class pdfController extends Controller
{

    /**
     * insertion de pdf
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/pdf/new", name="pdf_new")
     * @Method({"GET", "POST"})
     */
    public function addPdftoNewsletter(Request $request, FileUploader $fileUploader){

        $pdf = new Pdf();

        $form = $this->createForm(PdfType::class, $pdf, ['method'=>'POST']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $file = $pdf->getFile();
            $fileName = $fileUploader->upload($file);
            $pdf->setName('/bien_etre/web/uploads/files/'.$fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($pdf);
            $em->flush();

            $this->addFlash('success', 'pdf inséré avec succès');

        }

    }

}