<?php

namespace AppBundle\Controller;

use AppBundle\Service\Security\UserEmailService;
use AppBundle\Form\RegisterUserType;
use AppBundle\Form\ResetPasswordType;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * Connecte un utilisateur "manuellement" (quand il clic sur le lien d'activation de compte)
     *
     * @param User $user
     */
    private function authenticateUser(User $user)
    {
        $token = new UsernamePasswordToken($user, null, 'private', $user->getRoles());

        $this->get('security.token_storage')->setToken($token);
    }

    /**
     * Permet d'enregistrer un nouveau membre
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

            // 3) Encoder le mdp
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) Faut-il valider l'email ? (boolean dans le fichier parameters.yml)
            $needEmailValidation = $this->getParameter('verify_email_after_registration');

            if ($needEmailValidation) {
                $emailToken = md5(uniqid());
                $user->setRoles(array('ROLE_USER_PENDING'))->setEmailTemp($user->getEmail())->setEmailToken($emailToken);
                //  On utilise le service qu'on a créé dans UserEmailService.php : sendValidationEmail pour lui envoyer un mail
                $userEmailService->sendValidationEmail($user);
            }

            // 5) Sauvegarder l'utilisateur
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre inscription a bien été prise en compte. 
            Il ne vous reste plus qu\'à cliquer sur le lien dans le mail qui vient de vous être envoyé 
            (vérifiez vos spams !) pour pouvoir vous connecter. Vous avez 30 mintes pour le faire.');

            // 6) On authentifie l'utilisateur directement afin de lui éviter de saisir à nouveau ses identifiants
            // $this->authenticateUser($user);

            return $this->redirectToRoute('home');          // render ne fonctionne pas !!!???
        }

        return $this->render(
            '/Security/Register/register.html.twig', array('registerForm' => $registerForm->createView())
        );
    }

    /**
     * Valide un email depuis le lien de validation
     * @Route("/validateemail/{emailTemp}/{emailToken}", name="validateemail")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param User $user
     *
     * @return Response
     * @throws \Exception
     */
    public function validateEmailAction(EntityManagerInterface $em, User $user = null)
    {
        if ($user) {
            $user
//                ->setEmail($user->getEmailTemp())
                ->setEmailToken(null)
                ->setEmailTemp(null)
                ->addRole('ROLE_USER')
                ->removeRole('ROLE_USER_PENDING');
            $em->flush();
            // On authentifie l'utilisateur pour lui éviter de la faire
            $this->authenticateUser($user);
            $this->addFlash(
                "success", "Email validé avec succès"
            );

            return $this->redirectToRoute('home');
        }


        // Si le lien de validation d'email envoyé par mail a été cliqué dans les temps, on affiche la page d'accueil
        $validity = self::VALIDATE_EMAIL_VALIDITY_TIME;

        if ($user->getCreationDate()->modify("+$validity hour") >= new \DateTime()) {

            return $this->render('/Home/home.html.twig');
        } else {
            $this->addFlash(
                "warning", "Le lien n'est plus valide, veuillez cliquer ici pour recevoir un nouveau mail et valider  de vous réinsc..............."
            );
        }

//        $this->addFlash(
//            "warning", "Cet email n'existe pas, ou le lien est expiré"
//        );

        return $this->render('resendemailpagevalidation');
    }

    /**
     * Affiche la page avec possibilité de ré-envoyer un email de validation (si l'utilisateur a cliqué trop tard sur le lien du mail)
     * @Route("/resendemailpagevalidation", name="resendemailpagevalidation")
     *
     * @return Response
     */
    public function resendemailvalidationpageAction()
    {
        return $this->render('/Email/resend-emailvalidation-email.html.twig');
    }

    /**
     * Ré-envoie un email de validation si l'utilisateur clic sur la page redigigée ci-dessus
     * @Route("/resendemailvalidation", name="resendemailvalidation")
     *
     * @param UserEmailService $userEmailService
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function resendemailvalidationAction(UserEmailService $userEmailService)
    {
        $user = new User();

        $userEmailService->sendValidationEmail($user);

        $this->addFlash('success', 'Un nouvel email vous a été envoyé  (vérifiez vos spams !).
            Il ne vous reste plus qu\'à cliquer sur le lien pour pouvoir vous connecter. Vous avez 30 mintes pour le faire.');

        return $this->render('home');
    }

    /**
     * Permet de se connecter
     * @Route("/login", name="login")
     *
     * @param AuthenticationUtils $authUtils
     *
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authUtils)     // Request $request, (Dans Td11 prof mais ne sert à rien !)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // Retourne la même vue (login) mais si connexion OK amène vers la vue demandée dans security.yml (default_target_path: xxxxx)
        return $this->render('/Security/Login/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * Url de deconnexion
     *
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
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
        $responseParams = [];
        if ($userMail) {
            $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
            /* @var $user User */
            $user = $userRepository->findOneByEmail($userMail);
            if ($user) {
                $resetToken = md5(uniqid());
                $user->setLostPasswordDate(new \DateTime())->setLostPasswordToken($resetToken);
                $em->flush();
                $message = (new \Swift_Message('MDP Perdu'))
                    ->setFrom($this->getParameter('mailer_user'))
                    ->setTo($userMail)
                    ->setBody(
                        $this->renderView(
                            '/Security/Password/lostpassword-email.html.twig', array('link' => $this->generateUrl('resetpassword', ['lostPasswordToken' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL), 'validity' => self::LOST_PASSWORD_VALIDITY_TIME)
                        ), 'text/html'
                    );

                if ($mailer->send($message)) {
                    $this->addFlash(
                        "success", "Un email pour redéfinir votre mdp vous a été envoyé"
                    );
                } else {
                    $this->addFlash(
                        "danger", "Une erreur est survenue, merci d'essayer à nouveau"
                    );
                }
            } else {
                $this->addFlash(
                    "warning", "Email inconnu"
                );
            }
        }

        return $this->render('/Security/Password/lostpassword.html.twig', $responseParams);
    }

    /**
     * Redéfinir un mdp depuis le lien de redéfinition envoyé par mail
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

                // Encode the password (you could also do this via Doctrine listener)
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

                return $this->render('dashboard');
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

        return $this->render('/Security/AccessDenied/accessdenied.html.twig'
//            , array('nompage' => 'Security:accessDenied')
        );
    }
}
