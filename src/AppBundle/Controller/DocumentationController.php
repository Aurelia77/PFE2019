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
     * @Route("/faq", name="faq")
     *
     * @return Response
     */
    public function faqAction()
    {
        return $this->render('/Documentation/faq.html.twig');
    }

    /**
     * @Route("/rgpd", name="rgpd")
     *
     * @return Response
     */
    public function rgpdAction()
    {
        return $this->render('/Documentation/rgpd.html.twig');
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
}
