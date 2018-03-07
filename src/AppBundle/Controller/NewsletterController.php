<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 2/03/18
 * Time: 18:34
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Newsletter;
use AppBundle\Form\NewsletterType;
use AppBundle\Form\PdfType;
use AppBundle\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends Controller
{

    /**
     * création et envoi de newsletter (fonctionnement partiel, bug pour pièce jointe à mettre au point)
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("admin/newsletter/new", name="newsletter_new")
     * @Method({"GET", "POST"})
     */
    public function createNewsletter(Request $request, FileUploader $fileUploader)
    {

        $newsletter = new Newsletter();

        $doctrine = $this->getDoctrine();


        $form = $this->createForm(NewsletterType::class, $newsletter, ['method' => 'POST'])
            ->add('pdffile', PdfType::class, array());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newsletter->setDate(new \DateTime());

            $pdf = $newsletter->getPdffile();
            $file = $pdf->getFile();
            $fileName = $fileUploader->upload($file);


            $pdf->setName('/bien_etre/web/uploads/files/' . $fileName);

            $repo = $doctrine->getRepository('AppBundle:Member');


            $subscribers = $repo->findNewsletterSubscribers();

            $em = $this->getDoctrine()->getManager();


            $em->persist($newsletter);
            $em->flush();


            foreach ($subscribers as $subscriber) {

                $mails = $subscriber->getEmail();

                $subject = $newsletter->getTitle();
                $body = $newsletter->getMessage();
                $filepathUrl = $pdf->getName();



                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom("administration@bien_etre.com")
                    ->setTo($mails)
                    ->setBody($body)
                    ->setContentType("text/html")
                   // ->attach(\Swift_Attachment::fromPath($filepathUrl))

                ;


                $this->get('mailer')->send($message);

            }
            $this->addFlash('success', 'Vous avez envoyé la newsletter');
            return $this->redirectToRoute('admin_users');

        }
        return $this->render('newsletter/new.html.twig',
            ['newsletterForm' => $form->createView()]);


    }

}