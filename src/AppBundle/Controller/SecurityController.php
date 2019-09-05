<?php

namespace AppBundle\Controller;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\Security\UserEmailService;
use AppBundle\Form\RegisterUserType;
use AppBundle\Form\ResetPasswordType;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\EntityManagerInterface;


class SecurityController extends Controller
{
    const LOST_PASSWORD_VALIDITY_TIME = 1; // en heures
    const VALIDATE_EMAIL_VALIDITY_TIME = 0.5;

    /**
     * Use this function to authenticate a User "manually"
     *
     * @param User $user
     */
    private function authenticateUser(User $user)
    {
        $token = new UsernamePasswordToken($user, null, 'private', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
    }

    /**
     * Permet d'enregistrer un nouvel utilisateur
     * @Route("/register", name="register")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserEmailService $userEmailService
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function registerAction(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, UserEmailService $userEmailService)
    {
        // 1) Construire le form User
        $user = new User();

        // $registerForm = $this->get('form.factory')->create(RegisterUserType::class,$user); (idem ligne en dessous, avec le helper createForm)
        $registerForm = $this->createForm(RegisterUserType::class, $user);

        // 2) Hydrater l'objet User (on soumet le formulaire avec la méthode handleRequest())
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
//            $user->setActif(true);   Déjà dans le constructeur !!

            // 3) Encoder le mdp
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) Faut-il valider l'email ? (boolean dans le fichier parameters.yml)
            $needEmailValidation = $this->getParameter('verify_email_after_registration');
            # Si le boolean "verify_email_after_registration" dans le fichier "parameters.yml" est = true alors le rôle
            # [ROLE_USER_PENDING] sera attribué aux nouveaux utilisateurs le temps qu'ils vérifient leur adresse email avant de pouvoir se connecter
            if ($needEmailValidation) {
                $emailToken = md5(uniqid());
                $user->setRoles(array('ROLE_USER_PENDING'))->setEmailTemp($user->getEmail())->setEmailToken($emailToken);
                //  On utilise le service qu'on a créé dans UserEmailService.php : sendValidationEmail pour lui envoyer un mail
                $userEmailService->sendValidationEmail($user);
            }

            $userDatas = $registerForm->getData();

            // FICHIER IMAGE : fileImg contient l'image uploadée (stockée de manière temporaire)
            // @var Symfony\Component\HttpFoundation\File\UploadedFile $fileImg
            $fileImg = $userDatas->getPhoto();

            // Tester si $file est une instance de UploadedFile permet de savoir s'il s'agit d'un fichier qui vient d'être uploadé,
            // ou si il s'agit d'un fichier déjà stocké auparavant, qu'il ne faut donc pas modifier (si modif de user)
            if ($fileImg && $fileImg instanceof UploadedFile) {
                $fileName = md5(uniqid());

                // Déplacer le fichier temporaire dans le dossier prévu au stockage des images de profile
                $fileImg->move(
//                    'audio/'.$fileName
                    $this->getParameter('img_user_directory'), $fileName . '.jpg'
                );
                // Mettre à jour l'attribut fileName de l'entité User avec le nouveau nom du fichier
                $userDatas->setPhoto($fileName);
            }

            // 5) Sauvegarder l'utilisateur
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre inscription a bien été prise en compte. 
            Il ne vous reste plus qu\'à cliquer sur le lien dans le mail qui vient de vous être envoyé 
            (vérifiez vos spams !) pour pouvoir vous connecter.');

            return $this->redirectToRoute('home');              // render ne fonctionne pas !!!???
        }

        // Si il y a eu un problème (formulaire non valide) on reste sur la page d'inscription
        return $this->render(
            '/Security/Register/register.html.twig', array('registerForm' => $registerForm->createView())
        );
    }

    /**
     * Valide un email depuis le lien de validation
     * @Route("/validateemail/{emailTemp}/{emailToken}", name="validateemail")
     *
     * @param EntityManagerInterface $em
     * @param User $user
     *
     * @return Response
     * @throws \Exception
     */
    public function validateEmailAction(EntityManagerInterface $em, User $user = null)
    {
        // $validity = self::VALIDATE_EMAIL_VALIDITY_TIME;

        // SI le lien de validation d'email envoyé par mail a été cliqué dans les temps, on valide l'inscription, on connecte les membre et on affiche la page d'accueil
        //if ($user->getCreationDate()->modify("+$validity hour") >= new \DateTime()) {
        if ($user) {
            $user
//                ->setEmail($user->getEmailTemp())             // Déjà fait
                ->setEmailToken(null)
                ->setEmailTemp(null)
                ->addRole('ROLE_USER')
                ->removeRole('ROLE_USER_PENDING');
            $em->flush();

            // On authentifie l'utilisateur au cas où ce ne soit pas déjà fait
            $this->authenticateUser($user);
            $this->addFlash(
                "success", "Email validé avec succès, vous êtes maintenant connecté(e) !"
            );

            return $this->redirectToRoute('home');
        }
        // SINON on affiche une page qui va permettre de recevoir de nouveau le mail de validation
//        } else {
//            $this->addFlash(
//                "warning", "Le lien n'est plus valide, veuillez cliquer ici pour recevoir un nouveau mail et valider votre inscription."
//            );
//        }

        $this->addFlash(
//            "warning", "Cet email n'existe pas, ou le lien est expiré"
            "warning", "Cet email a déjà été validé, vous pouvez vous connecter !"
        );
        return $this->redirectToRoute('login');
    }

//    /**
//     * Affiche la page avec possibilité de ré-envoyer un email de validation (si l'utilisateur a cliqué trop tard sur le lien du mail)
//     * Créer une nouvelle date pour laisser ...
//     * @Route("/resendemailpagevalidation", name="resendemailpagevalidation")
//     *
//     * @return Response
//     */
//    public function resendemailvalidationpageAction()
//    {
//        return $this->render('Security/Email/resend-emailvalidation-email.html.twig');
//    }
//
//    /**
//     * Ré-envoie un email de validation si l'utilisateur clique sur la page redigigée ci-dessus
//     * @Route("/resendemailvalidation", name="resendemailvalidation")
//     *
//     * @param UserEmailService $userEmailService
//     * @return Response
//     * @throws \Twig\Error\LoaderError
//     * @throws \Twig\Error\RuntimeError
//     * @throws \Twig\Error\SyntaxError
//     */
//    public function resendemailvalidationAction(UserEmailService $userEmailService)
//    {
//        $user = new User();
//
//        $userEmailService->sendValidationEmail($user);
//
//        $this->addFlash('success', 'Un nouvel email vous a été envoyé  (vérifiez vos spams !).
//            Il ne vous reste plus qu\'à cliquer sur le lien pour pouvoir vous connecter. Vous avez 30 mintes pour le faire.');
//
//        return $this->redirectToRoute('home');
//    }


    /**
     * Permet de se connecter
     * @Route("/login", name="login")
     *
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {
        // Si on est déjà connecté, on arrive sur la page d'accueil
//        if ('IS_AUTHENTICATED_FULLY' == true) {
//        if ('IS_AUTHENTICATED_ANONYMOUSLY' == false) {
//            return $this->redirectToRoute('home');
//        }

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // Garde en mémoire l'identifiant (pas besoin de la retaper)
        $lastUsername = $authUtils->getLastUsername();

        // Retourne la même vue (login) mais si connexion OK amène vers la vue demandée dans security.yml (default_target_path: home)
        return $this->render('/Security/Login/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * Url de deconnexion
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Depuis la page Login, lien "Mot de passe oublié"
     * Affiche un formulaire pour redéfinir son MDP, et envoie un email de redéfinition du mdp
     * @Route("/lostpassword", name="lostpassword")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param \Swift_Mailer $mailer
     *
     * @return Response
     * @throws \Exception
     */
    public function sendLostPasswordMailAction(Request $request, EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $userMail = $request->get('email');
//        $responseParams = [];

        if ($userMail) {
            /* @var $userRepository UserRepository */   // ??? Pas besoin ???
            $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');

            /* @var $user User */
            $user = $userRepository->findOneByEmail($userMail);

            if ($user) {
                $resetToken = md5(uniqid());
                $user->setLostPasswordDate(new \DateTime())->setLostPasswordToken($resetToken);
                $em->flush();

                $message = (new \Swift_Message('Mot De Passe Oublié'))
                    ->setFrom($this->getParameter('mailer_user'))
                    ->setTo($userMail)
                    ->setBody(
                        $this->renderView(
                            '/Security/Password/lostpassword-email.html.twig',
                            array('link' => $this->generateUrl('resetpassword',
                                                                    ['lostPasswordToken' => $resetToken],
                                                        UrlGeneratorInterface::ABSOLUTE_URL),
                                                                    'validity' => self::LOST_PASSWORD_VALIDITY_TIME)
                        ), 'text/html'
                    );

                if ($mailer->send($message)) {
                    $this->addFlash(
                        "success", "Un email pour redéfinir votre mot de passe vous a été envoyé."
                    );
                } else {
                    $this->addFlash(
                        "danger", "Une erreur est survenue, merci d'essayer à nouveau."
                    );
                }

            } else {
                $this->addFlash(
                    "warning", "Email inconnu."
                );
            }
        }
        return $this->render('/Security/Password/lostpassword.html.twig');//, $responseParams);
    }

    /**
     * Redéfinir un mdp depuis le lien de redéfinition envoyé par mail (si mot de passe oublié)
     * @Route("/resetpassword/{lostPasswordToken}", name="resetpassword")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param User $user
     *
     * @return Response
     * @throws \Exception
     */
    public function resetPasswordAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user = null)
    {
        if ($user) {
            // Changer le MDP si form submitted
            $passwordForm = $this->createForm(ResetPasswordType::class, $user);
            $passwordForm->handleRequest($request);

            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                // Encode le password (you could also do this via Doctrine listener)
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);

                // Reset lostPassword token & date
                $user->setLostPasswordToken(null)->setLostPasswordDate(null);

                // On en profite pour valider l'email de l'utilisateur au cas où ce n'était pas déjà fait
                $user->removeRole('ROLE_USER_PENDING')->addRole('ROLE_USER')->setEmailTemp(null)->setEmailToken(null);
                // save the User!
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->authenticateUser($user);
                $this->addFlash(
                    "success", "Mot de passe changé avec succès"
                );
                return $this->redirectToRoute('dashboard');
            }
            // Afficher le formulaire
            $validity = self::LOST_PASSWORD_VALIDITY_TIME;
            if ($user->getLostPasswordDate()->modify("+$validity hour") >= new \DateTime()) {
                return $this->render('/Security/Password/resetpassword.html.twig', array('passwordForm' => $passwordForm->createView()));
            } else {
                $this->addFlash(
                    "warning", "Le lien n'est plus valide"
                );
            }
        }
        return $this->redirectToRoute('lostpassword');
    }

    /**
     * Afficher la page d'accès refusé (droits insufisants)
     * @Route("/private/accessdenied", name="accessdenied")
     *
     * @param Request $request
     * @param UserEmailService $userEmailService
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function accessDeniedAction(Request $request, UserEmailService $userEmailService)
    {
        // Si clic bouton : Envoyer à nouveau : réenvoie un mail de validation
        if ($request->get('resendEmailValidation') == 1) {
            if ($userEmailService->sendValidationEmail($this->getUser())) {
                $this->addFlash(
                    "success", "Email envoyé avec succès"
                );
            } else {
                $this->addFlash(
                    "danger", "Une erreur est survenue, merci d'essayer à nouveau"
                );
            }
        }
        return $this->render('/Security/AccessDenied/accessdenied.html.twig', array('nompage' => 'Security:accessDenied'));
    }
}

