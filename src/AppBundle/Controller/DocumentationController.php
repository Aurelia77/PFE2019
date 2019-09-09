<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;


class DocumentationController extends Controller
{
    /**
     * @Route("/tuto", name="tuto")
     *
     * @return Response
     */
    public function tutoAction()
    {
        return $this->render('/Documentation/tuto.html.twig');
    }

    /**
     * @Route("/poldeconf", name="poldeconf")
     *
     * @return Response
     */
    public function poldeconfAction()
    {
        return $this->render('/Documentation/poldeconf.html.twig');
    }

    /**
     * @Route("/mentionsleg", name="mentionsleg")
     *
     * @return Response
     */
    public function mentionslegAction()
    {
        return $this->render('/Documentation/mentionsleg.html.twig');
    }


    /**
     * Dans pied de page : pour contacter le propriétaire du site
     * @Route("/contact", name="contact")
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return Response
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer)
    {
        $userMail = $request->get('email');
        $userMessage = $request->get('message');


        if ($userMail) {

                $message = (new \Swift_Message('Message utilisateur Entre Dièse et Bémols'))
                    ->setFrom($userMail)
                    ->setTo("aurelia.h+d&b@hotmail.fr")
                    ->setBody(
                        $this->renderView(
                            '/Documentation/contact-email.html.twig',
                            array(
//                                'userMail' =>$userMail,
                                'userMessage' =>$userMessage,
                                )
                        ), 'text/html'
                    );

                if ($mailer->send($message)) {
                    $this->addFlash(
                        "success", "Votre message a été envoyé."
                    );
                } else {
                    $this->addFlash(
                        "danger", "Une erreur est survenue, merci d'essayer à nouveau."
                    );
                }
        }
        return $this->render('/Documentation/contact.html.twig' );

    }

}
