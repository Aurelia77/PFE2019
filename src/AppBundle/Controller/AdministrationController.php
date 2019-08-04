<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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


class AdministrationController extends Controller
{

    /**
     * Page accessible seulement aux admins
     *
     * @Route("e")
     */
    public function administrationAction()
    {
        return $this->render('/Administration/administration.html.twig', array('nompage' => 'Administration:administration'));
    }

    /**
     * Page accessible seulement aux admins, où sont listés tous les utilisateurs du site
     *
     * @Route("/private/administration/users", name="administrationusers")
     */
    public function administrationUsersAction(Request $request, PaginatorInterface $paginator) //, EntityManagerInterface $em)
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


//      LUCAS : pas d'utilisation de l'EntityManagerInterface $em, pourquoi ????? (je ne l'ai pas utilisé sans la pagination !!(?) et pourquoi pas de Querybuilder ?????
//    /**
//     * Page accessible seulement aux admins, où sont listés tous les utilisateurs du site
//     *
//     * @Route("/private/administration/users", name="administrationusers")
//     * @param Request $request
//     * @param PaginatorInterface $paginator
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function administrationUsersAction(Request $request, PaginatorInterface $paginator)
//    {
//        $userRepo = $this->getDoctrine()->getRepository(User::class);
//
//        $query = $request->query->get('query');
//
//        if (isset($query) && $query !== "")
//        {
//            $users = $userRepo->findByFirstNameOrLastName($query);
//        } else {
//            $users = $userRepo->findAll();
//        }
//
//        $pagination = $paginator->paginate(
//            $users, // donnees
//            $request->query->getInt('page', 1), // num page lors du chargement
//            5 // nb element par page
//        );
//
//        return $this->render('/Administration/users.html.twig', array(
//            'users'=>$users,
//            'pagination'=>$pagination,
//        ));
//    }


    /**
     * Page accessible seulement aux admins, pour créer un nouveau livre
     *
     * @Route("/private/administration", name="administration")
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