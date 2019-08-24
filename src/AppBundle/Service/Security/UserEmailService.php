<?php

namespace AppBundle\Service\Security;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\User;

/**
 * Une classe qui possède des méthodes liées à la sécurité des emails utilisateurs
 */
class UserEmailService
{
    
    private $mailer;
    private $mailerUser;
    private $urlGenerator;
    private $twig;
    
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, string $mailerUser)
    {
        $this->mailer = $mailer;
        $this->mailerUser = $mailerUser;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    /**
     * Envoie un email à l'utilisateur pour vérifier son adresse mail
     *
     * @param User $user
     *
     * @return boolean
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendValidationEmail(User $user){
        if(!$user->getEmailTemp()){
            
            return false;
        }

        // On créé le message
        $message = (new \Swift_Message('Validation email'))
            ->setFrom($this->mailerUser)
            ->setTo($user->getEmailTemp())
            ->setBody(
            $this->twig->render(
                '/Security/Email/emailvalidation-email.html.twig',
                array('link' => $this->urlGenerator->generate('validateemail',
                                                                    ['emailToken' => $user->getEmailToken(),
                                                                    'emailTemp' => $user->getEmailTemp()],
                                                                    UrlGeneratorInterface::ABSOLUTE_URL))
                ),
                'text/html'
            )
        ;

        // Et on envoie le mail qui contient le message créé ci-dessus
        return (bool) $this->mailer->send($message);
    }


}
