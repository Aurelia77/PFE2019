<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Track;
use AppBundle\Entity\Message;
use AppBundle\Form\CreateBookType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormTypeInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;                // Car on utilise le paramètre : EntityManagerInterface $em
use AppBundle\Repository\UserRepository;


/**
 * @Route("/admin")
 */
class AdministrationController extends Controller
{

    /**
     * Page accessible seulement aux admins
     *
     * @Route("/", name="admin")
     */
    public function administrationAction()
    {

        return $this->render('/Administration/administration.html.twig', array('nompage' => 'Administration:administration'));
    }


    /**
     * Affiche les membres du site
     * Possibilité de faire une recherche par nom ou prénom
     *
     * @Route("/users", name="adminlistmembers")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function administrationUsersAction(Request $request, PaginatorInterface $paginator)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);

        $name = $request->query->get('query');

        if (isset($name) && $name !== "")
        {
            $users = $userRepo->findByFirstNameOrLastName($name);
        } else {
            $users = $userRepo->findAll();
        }

        $pagination = $paginator->paginate(
            $users,                                           // données
            $request->query->getInt('page', 1),  // numéro de la page lors du chargement
            3                                           // nombre d'élements par page
        );

        return $this->render('/Administration/listmembers.html.twig', array(
            'pagination'=>$pagination,
            'recherche' => $name,

        ));
    }


    /**
     * Affiche les tracks du site
     *
     * @Route("/tracks", name="adminlisttracks")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function administrationTracksAction(Request $request, PaginatorInterface $paginator)
    {
        $trackRepo = $this->getDoctrine()->getRepository(Track::class);

        $name = $request->query->get('query');

//        if (isset($name) && $name !== "")
//        {
//            $users = $userRepo->findByFirstNameOrLastName($name);
//        } else {
        $tracks = $trackRepo->findAll();
//        }

        $pagination = $paginator->paginate(
            $tracks,                                         // données
            $request->query->getInt('page', 1),  // numéro de la page lors du chargement
            3                                           // nombre d'élements par page
        );

        return $this->render('/Administration/listtracks.html.twig', array(
            'pagination'=>$pagination,
//            'recherche' => $name,
        ));
    }


//    public function administrationUsersAction(Request $request, PaginatorInterface $paginator) //, EntityManagerInterface $em)
//    {
//        // Si un utilisateur a lancé la requête = si le name est récupéré ( = ce qui a été saisi dans le champ du formulaire)
//
//        $name = "Aucune recherche pour l'instant";
//
//        if ($request->get('name')) {
//
//            // On la récupère : on aura donc le nom ou le prénom recherché
//            $name = $request->get('name');
//
//            // On l'exécute avec notre fonction findByFirstNameOrLastName() avec la recherche ($name)
//            //$userRepository = $em->getRepository(User::class);        OK (en ajoutant $em ds les param) mais on peut faire sans !!! :
//            $userRepository = $this->getDoctrine()->getRepository(User::class);
//
//            //    Avec la pagination
//            $pagination = $paginator->paginate(
//                $userRepository->findByFirstNameOrLastName($name),    // La query que l'on veut paginer
//                $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
//                5                                             // Nombre d'éléments affichés par page
//            );
//
//        } else {
//
//            // Sinon on recherche tous les membres (findAll)
//            //$userRepository = $em->getRepository(User::class);        OK (en ajoutant $em ds les param) mais on peut faire sans !!! :
//            $userRepository = $this->getDoctrine()->getRepository(User::class);
//
//            $pagination = $paginator->paginate(
//                $userRepository->findAll(),                         // La query que l'on veut paginer
//                $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
//                3                                             // Nombre d'éléments affichés par page
//            );
//        }
//
//
//        // Le return retourne une variable et un tableau (array ou [])
//        return $this->render('/Administration/listmember.html.twig', [
//            'pagination' => $pagination,
//            'recherche' => $name,
//        ]);
//    }

    // ET SANS LA PAGINATION (OK !! ):
//    public function administrationUsersAction() //(Request $request, EntityManagerInterface $em)
//    {
//        $userRepository = $this->getDoctrine()->getRepository(User::class);
//        $users = $userRepository->findAll();
//
//     //Le return retourne une variable et un tableau (array ou [])
//
//        return $this->render('/Administration/users.html.twig',
//              array('nompage' => 'Administration:administrationUsers',
//                    'users' => $users));
//    }










    /**
     * Page accessible seulement aux admins, pour créer un nouveau livre
     *
     * @Route("/private/administration", name="administrationBook")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function administrationBooksAction(Request $request, PaginatorInterface $paginator) //, EntityManagerInterface $em)
    {
        // Si un utilisateur a lancé la requête = si le name est récupéré ( = ce qui a été saisi dans le champ du formulaire)

        $name = "Aucune recherche pour l'instant";

        if ($request->get('name')) {

            // On la récupère : on aura donc le nom ou le prénom recherché
            $name = $request->get('name');

            // On l'exécute avec notre fonction findByFirstNameOrLastName() avec la recherche ($name)
            //$userRepository = $em->getRepository(User::class);        OK (en ajoutant $em ds les param) mais on peut faire sans !!! :
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            //    Avec la pagination
            $pagination = $paginator->paginate(
                $userRepository->findByFirstNameOrLastName($name),    // La query que l'on veut paginer
                $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
                5                                             // Nombre d'éléments affichés par page
            );

        } else {

            // Sinon on recherche tous les utilisateurs (findAll)
            //$userRepository = $em->getRepository(User::class);        OK (en ajoutant $em ds les param) mais on peut faire sans !!! :
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            $pagination = $paginator->paginate(
                $userRepository->findAll(),                         // La query que l'on veut paginer
                $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
                5                                             // Nombre d'éléments affichés par page
            );
        }


        // Le return retourne une variable et un tableau (array ou [])
        return $this->render('/Administration/users.html.twig', [
            'nompage' => 'Administration:administrationUsers',
            'pagination' => $pagination,
            'recherche' => $name,
        ]);
    }




//    /**
//     * Page accessible seulement aux admins, pour créer un nouveau livre
//     *
//     * @Route("/private/administration", name="administration")
//     * @param Request $request
//     * @param EntityManagerInterface $em
//     * @return Response
//     */
//    public function administrationBooksActionRequest $request) //(Message $book = null, EntityManagerInterface $em, Request $request)
//    {
//        // Création d'une entité Message (vide)
//        $book = new Message();
//
//        // Création du formulaire
//        $formBook = $this->createForm(CreateBookType::class, $book);
//
//        // LUCAS (Pour ajouter le bouton SUBMIT avec écrit : Ajouter)
//        $formBook->add('Ajouter', SubmitType::class);
//
//        // Hydratation de $book depuis la requete
//        $formBook->handleRequest($request);
//
//        // Validation du form
//        if ($formBook->isSubmitted() && $formBook->isValid()) {
//
//            //  !!! PROBLEME !!!
//
//            // Création du livre
////            $this->persist($book);
////            $this->flush();
////            $this->addFlash('success', 'Livre modifié avec succès');
////
////            return $this->redirectToRoute("administrationbooks");
//
//            try {
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($book);
//                $em->flush();
//                $this->addFlash('success', 'Livre ajouté avec succès !');
//            } catch (\Exception $e) {
//                $this->addFlash('danger', 'Une erreur est survenue : ' . $e->getMessage());
//            }
//        }
//
//        return $this->render('Administration/administrationbooks.html.twig', [
//            'formBook' => $formBook->createView(),
//        ]);
//    }


}