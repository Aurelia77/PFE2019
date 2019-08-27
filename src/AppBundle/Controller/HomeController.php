<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Track;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\User;
use AppBundle\Entity\Message;
use AppBundle\Form\CreateBookType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormTypeInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;                // Car on utilise le paramètre : EntityManagerInterface $em
use AppBundle\Repository\UserRepository;

class HomeController extends Controller
{


//     ET SANS LA PAGINATION (OK !! ):
//    /**
//     * Page d'accueil avec liste des tracks mis par les membres
//     * Accessible à tout le monde
//     * @Route("/", name="home")
//     * @return Response
//     */
//    public function homeAction()
//    {
//        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
//        $tracks = $trackRepository->findAll();
//
//        return $this->render('/Home/home.html.twig',
//              array('tracks' => $tracks));
//
//    }

    /**
     * Page d'accueil avec liste des tracks mis par les membres
     * Accessible à tout le monde
     * @Route("/", name="home")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function homeAction(Request $request, PaginatorInterface $paginator)
    {
        //$userRepository = $em->getRepository(User::class);        OK (en ajoutant $em ds les param) mais on peut faire sans !!! :
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);

        $pagination = $paginator->paginate(
            $trackRepository->findAll(),                        // La query que l'on veut paginer
            $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
            6                                             // Nombre d'éléments affichés par page
        );

        return $this->render('/Home/home.html.twig', [
            'pagination' => $pagination
        ]);

    }





//    /**
//     * ESSAI Audio Player
//     * @Route("/audioplayer", name="audioplayer")
//     */
//    public function audioplayerAction(Request $request, PaginatorInterface $paginator)
//    {
//
//        //$userRepository = $em->getRepository(User::class);        OK (en ajoutant $em ds les param) mais on peut faire sans !!! :
//        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
//
//        $pagination = $paginator->paginate(
//            $trackRepository->findAll(),                        // La query que l'on veut paginer
//            $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
//            3                                             // Nombre d'éléments affichés par page
//        );
//
//        return $this->render('/Home/home-audio-player-complet(commenté).html.twig', [
//            'pagination' => $pagination,
//        ]);
//
//
////        return $this->file($pdfPath);
//
//    }

    /**
     * ESSAI Page Accueil THEME XXXXX
     * @Route("/theme", name="home_theme")
     * @return Response
     */
    public function indexAction()
    {
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $tracks = $trackRepository->findAll();

        return $this->render('/Home/index.html.twig');

    }


}
