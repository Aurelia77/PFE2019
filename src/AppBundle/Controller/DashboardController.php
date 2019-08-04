<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;


class DashboardController extends Controller
{
    /**
     * @Route("/private", name="dashboard")
     *
     * @return Response
     */
    public function dashboardAction() //User $user, Request $request, EntityManagerInterface $em)
    {
//        if (!$user) {
//            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
//        }
//
//        //* @var $userRepository UserRepository */
//        $userRepository = $em->getRepository(User::class);
//        //$minDate = $request->query->get('minCreationDate') ? new \DateTime($request->query->get('minCreationDate')) : null;
//        //$maxDate = $request->query->get('maxCreationDate') ? new \DateTime($request->query->get('maxCreationDate')) : null;
//
//        //$pagination = $paginator->paginate(
//            //$userRepository->findByCreationDatesQuery($minDate, $maxDate), // La query que l'on veut paginer
//            //$request->query->getInt('page', 1), // On récupère le numéro de la page et on le défini à 1 par défaut
//           // 20 // Nombre d'éléments affiché par page
//       // );
//
//        return $this->render('/Dashboard/dashboard.html.twig', [
//            'user' => $user,
//            'userRepository' => $userRepository,
//            //'baseUrlToSendMail' => $this->generateUrl('users_mail', []),
//        ]);
        //$essai = "essai";

        return $this->render('/Dashboard/dashboard.html.twig', [
            //'essai' => $essai,
        ]);
    }

}
