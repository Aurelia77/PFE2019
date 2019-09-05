<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MotClef;
use AppBundle\Entity\User;
use AppBundle\Entity\Track;
use AppBundle\Entity\Message;
use AppBundle\Form\CreateBookType;
use AppBundle\Form\CreateMotClefType;
use AppBundle\Repository\MessageRepository;
use AppBundle\Repository\MotClefRepository;
use AppBundle\Repository\TrackRepository;
use Doctrine\ORM\EntityManager;
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
        /* @var $userRepository UserRepository */
        $userRepo = $this->getDoctrine()->getRepository(User::class);

        $name = $request->query->get('query');

        if (isset($name) && $name !== "") {
            $users = $userRepo->findByFirstNameOrLastName($name);
        } else {
            $users = $userRepo->findBy(array(), array('creationdate' => 'DESC'));
        }

        $pagination = $paginator->paginate(
            $users,                                           // données
            $request->query->getInt('page', 1),  // numéro de la page lors du chargement
            3                                           // nombre d'élements par page
        );

        return $this->render('/Administration/listmembers.html.twig', array(
            'pagination' => $pagination,
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

        if (isset($name) && $name !== "") {
            $tracks = $trackRepo->findByTitleOrName($name);
        } else {
            $tracks = $trackRepo->findBy(array(), array('creationdate' => 'DESC'));
        }

        $pagination = $paginator->paginate(
            $tracks,                                         // données
            $request->query->getInt('page', 1),  // numéro de la page lors du chargement
            3                                           // nombre d'élements par page
        );

        return $this->render('/Administration/listtracks.html.twig', array(
            'pagination' => $pagination,
            'recherche' => $name,
        ));
    }

    /**
     * Affiche les mots clefs du site
     *
     * @Route("/motsclefs", name="adminlistmotsclefs")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function administrationMotsclefsAction(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {

        $motclefRepo = $this->getDoctrine()->getRepository(MotClef::class);

        $name = $request->query->get('query');

        if (isset($name) && $name !== "") {
            $motsclefs = $motclefRepo->findBy(array('mot' => $name));
        } else {
            $motsclefs = $motclefRepo->findBy(array(), array('mot' => 'ASC'));
        }

        $pagination = $paginator->paginate(
            $motsclefs,
//            $motsclefsRepository->findBy(array(), array('mot' => 'ASC')),   // La query que l'on veut paginer
            $request->query->getInt('page', 1),  // numéro de la page lors du chargement
            3                                           // nombre d'élements par page
        );

        // Formulaire d'ajout
        // 1) Construire le form CreateMotClefType
        $motclef = new MotClef();
        // $newTrackForm = $this->get('form.factory')->create(NewTrackType::class, $track);      // Ok mais mieux avec le helper ci-dessous :
        $newMotclefForm = $this->createForm(CreateMotClefType::class, $motclef);

        // 2) Hydrater l'objet Track (avec ce qui est rentré dans le formulaire et le reste ci-dessous)
        $newMotclefForm->handleRequest($request);

        // 3) Validation du Form
        if ($newMotclefForm->isSubmitted() && $newMotclefForm->isValid()) {

            // Sauvegarder le track ds la BDD
            $em->persist($motclef);
            $em->flush();
            $this->addFlash('success', 'Le mot de clef a bien été ajouté !');

            return $this->redirectToRoute('adminlistmotsclefs');
        }


        return $this->render('/Administration/listmotsclefs.html.twig', array(
            'pagination' => $pagination,
            'recherche' => $name,
            'newMotclefForm' => $newMotclefForm->createView(),
            'request' => $request,));
    }

    /**
     * Affiche les messages (commentaires) du site
     *
     * @Route("/messages", name="adminlistmessages")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function administrationMessagesAction(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $messageRepo = $this->getDoctrine()->getRepository(Message::class);
        $message = $messageRepo->findBy(array(), array('creationdate' => 'DESC'));


//      Recherche
//        $name = $request->query->get('query');
//
//        if (isset($name) && $name !== "") {
//            $motsclefs = $motclefRepo->findBy(array('mot' => $name));
//        } else {
//            $motsclefs = $motclefRepo->findBy(array(), array('mot' => 'ASC'));
//        }
//
        $pagination = $paginator->paginate(
            $message,
//            $motsclefsRepository->findBy(array(), array('mot' => 'ASC')),   // La query que l'on veut paginer
            $request->query->getInt('page', 1),  // numéro de la page lors du chargement
            3                                           // nombre d'élements par page
        );

        return $this->render('/Administration/listmessages.html.twig', array(
                'pagination' => $pagination,
//            'recherche' => $name,
//            'newMotclefForm' => $newMotclefForm->createView(),
                'request' => $request,
            )
        );
    }

    /**
     * Modifie les rôles d'un utilisateur
     *
     * @Route("/modifRolesUser/{id}/{role}", name="modifRolesUser")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function modifRolesUser(User $user, Request $request, EntityManagerInterface $em)
    {
        $id = $request->get('id');

        if ($id == $this->getUser()->getId()){
            $this->addFlash('success', 'Impossible de modifier son propre role !');

            return $this->redirectToRoute('adminlistmembers');
    }
        $role = $request->get('role');

        if ($role == 1) {
            $user->removeRole('ROLE_ADMIN');
        }

        else if ($role == 2){
            $user->addRole('ROLE_ADMIN');
        }

        /* @var $userRepository UserRepository */
//       $userRepository = $em->getRepository(User::class);
        // On recherche l'utilisateur avec l'id donné
//        $user = $userRepository->find($id);


//        $userRepository->modifRolesUserActionQuery($id, $role); // La query que l'on veut paginer
        $em->persist($user);
        $this->addFlash('success', 'Le role de l\'utilisateur a été modifié !');

        $em->flush();

        return $this->redirectToRoute('adminlistmembers');

    }


    /**
     * Modifie le fait que l'utilisateur/track/mot clef/message soit actifs ou non
     *
     * @Route("/switchActif/{entity}/{actif}/{id}", name="switchActif")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function switchActifAction(Request $request, EntityManagerInterface $em)
    {
        // On récupère les paramètres entity, id et actif dans l'URL
        $entity = $request->get('entity');
        $id = $request->get('id');
        $actif = $request->get('actif');

//        $route = $request->attributes->get('_route');
        $route = 'adminlistmembers';
//        $route = $this ->get( 'router' );

        if ($entity == 'user') {
            /* @var $userRepository UserRepository */          // Pour le reconnaitre et utiliser ses méthode (ici switchActifUserActionQuery, sinon IDE ne reconnait pas)
            $userRepository = $em->getRepository(User::class);
            // On recherche l'utilisateur avec l'id donné
            $user = $userRepository->find($id);
            $userRepository->switchActifUserActionQuery($actif, $id); // La query que l'on veut paginer
            $em->persist($user);
            $this->addFlash('success', 'Le statut actif de l\'utilisateur a été modifié !');

        } elseif ($entity == 'track') {
            /* @var $trackRepository TrackRepository */
            $trackRepository = $em->getRepository(Track::class);
            $track = $trackRepository->find($id);
            $trackRepository->switchActifTrackActionQuery($actif, $id);
            $em->persist($track);
            $route = 'adminlisttracks';
            $this->addFlash('success', 'Le statut actif du track a été modifié ! Si un track est mis en inactif, tous les tracks avec un son en plus sont alors désactivés.');
        } elseif ($entity == 'motclef') {
            /* @var $motclefRepository MotclefRepository */
            $motclefRepository = $em->getRepository(MotClef::class);
            $motclef = $motclefRepository->find($id);
            $motclefRepository->switchActifMotclefActionQuery($actif, $id);
            $em->persist($motclef);
            $route = 'adminlistmotsclefs';
            $this->addFlash('success', 'Le statut actif du mot clef a été modifié !');
        } elseif ($entity == 'message') {
            /* @var $messageRepository MessageRepository */
            $messageRepository = $em->getRepository(Message::class);
            $message = $messageRepository->find($id);
            $messageRepository->switchActifMessageActionQuery($actif, $id);
            $em->persist($message);
            $route = 'adminlistmessages';
            $this->addFlash('success', 'Le statut actif du message a été modifié !');
        }
        $em->flush();

        return $this->redirectToRoute($route);
    }
}