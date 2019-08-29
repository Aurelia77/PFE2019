<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 3.0.3 (doctrine2-annotation) on 2018-02-05 23:58:28.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AppBundle\Entity\User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="`user`", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_user_email", columns={"email"})})
 * @UniqueEntity(fields="email", message="Ce mail est déjà utilisé") 
 */
class User implements UserInterface, \Serializable
{
//      Si non spécifié, le type sera String et le nom de la colonne le même que le champ (ici id)
//      @ORM\Id = Clef primaire
//      @ORM\GeneratedValue(strategy="AUTO") = s'auto-incrémente

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=2)
     */
    protected $pseudo;

    /**
     * checkMX permet de s’assurer qu’il existe au moins un champ MX et c’est ce qui est important pour envoyer un mail.
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *      message = "L'email '{{ value }}' est invalide.",
     *      checkMX = true
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $emailTemp;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $emailToken;    
    
    /**
     * not persisted plainPassword
     */
    private $plainPassword;    
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=6)
     */
    protected $password;

    /**
     * DC2Type:array
     *
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=2)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=2)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $photo;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $lostPasswordToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lostPasswordDate;

    /**
     * Non mapped field, used when changing the password
     */
    private $oldPassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    protected $creationDate;

    /**
     * Un utilisateur est lié à (= a mis sur le site) 0, 1, ou plusieurs Track(s)
     *
     * @ORM\OneToMany(targetEntity="Track", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $userTracks;

    /**
     * Un utilisateur est lié à (= a mis sur le site) 0, 1, ou plusieurs Message(s) : Relation OneToMany bidirectionnelle
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $userMessages;




// On créé le Constructeur avec une valeur par défaut pour le champ roles : ROLE_USER - Et la date de création
    public function __construct()
    {
        $this->setRoles(['ROLE_USER']);
        $this->setCreationDate(new \DateTime());
    }

//    /**
//     * Set the value of id.
//     *
//     * @param integer $id
//     * @return \AppBundle\Entity\User
//     */
//    public function setId($id)
//    {
//        $this->id = $id;
//
//        return $this;
//    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     * @return \AppBundle\Entity\User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of password.
     *
     * @param string $password
     * @return \AppBundle\Entity\User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }    
    
    /**
     * Get the value of salt.
     *
     * @return string
     */
    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    /**
     * Set the value of roles.
     *
     * @param array $roles
     * @return \AppBundle\Entity\User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role
     *
     * @param string $role
     * @return User
     */
    public function addRole($role){
        $roles = $this->getRoles();
        if(!in_array($role, $roles)){
            $this->setRoles(array_merge($roles, array($role)));
        }
        
        return $this;        
    }

    /**
     * Remove a role
     *
     * @param string $role
     * @return User
     */
    public function removeRole($role){
        $roles = $this->getRoles();
        if(in_array($role, $roles)){
            $this->setRoles(array_diff($roles, array($role)));
        }

        return $this;
    }    
    
    /**
     * Set the value of firstName.
     *
     * @param string $firstName
     * @return \AppBundle\Entity\User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of lastName.
     *
     * @param string $lastName
     * @return \AppBundle\Entity\User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * Set the value of lostPasswordToken.
     *
     * @param string $lostPasswordToken
     * @return \AppBundle\Entity\User
     */
    public function setLostPasswordToken($lostPasswordToken)
    {
        $this->lostPasswordToken = $lostPasswordToken;

        return $this;
    }

    /**
     * Get the value of lostPasswordToken.
     *
     * @return string
     */
    public function getLostPasswordToken()
    {
        return $this->lostPasswordToken;
    }

    /**
     * Set the value of lostPasswordDate.
     *
     * @param \DateTime $lostPasswordDate
     * @return \AppBundle\Entity\User
     */
    public function setLostPasswordDate($lostPasswordDate)
    {
        $this->lostPasswordDate = $lostPasswordDate;

        return $this;
    }

    /**
     * Get the value of lostPasswordDate.
     *
     * @return \DateTime
     */
    public function getLostPasswordDate()
    {
        return $this->lostPasswordDate;
    }

    public function getUsername()
    {
        return $this->email;
    }    
    
    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }    

    /**
     * Set emailTemp
     *
     * @param string $emailTemp
     *
     * @return User
     */
    public function setEmailTemp($emailTemp)
    {
        $this->emailTemp = $emailTemp;

        return $this;
    }

    /**
     * Get emailTemp
     *
     * @return string
     */
    public function getEmailTemp()
    {
        return $this->emailTemp;
    }

    /**
     * Set emailToken
     *
     * @param string $emailToken
     *
     * @return User
     */
    public function setEmailToken($emailToken)
    {
        $this->emailToken = $emailToken;

        return $this;
    }

    /**
     * Get emailToken
     *
     * @return string
     */
    public function getEmailToken()
    {
        return $this->emailToken;
    }
    
    /**
     * Set oldPassword
     * 
     * @param string $oldPassword
     * 
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
        
        return $this;
    }

    /**
     * Get oldPassword
     * 
     * @return string
     */
    public function getOldPassword()
    {
        
        return $this->oldPassword;
    }


    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return User
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Add userTrack
     *
     * @param \AppBundle\Entity\Track $userTrack
     *
     * @return User
     */
    public function addUserTrack(Track $userTrack)
    {
        $this->userTracks[] = $userTrack;

        return $this;
    }

    /**
     * Remove userTrack
     *
     * @param \AppBundle\Entity\Track $userTrack
     */
    public function removeUserTrack(Track $userTrack)
    {
        $this->userTracks->removeElement($userTrack);
    }

    /**
     * Get userTracks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserTracks()
    {
        return $this->userTracks;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     *
     * @return User
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Add userMessage
     *
     * @param \AppBundle\Entity\Message $userMessage
     *
     * @return User
     */
    public function addUserMessage(Message $userMessage)
    {
        $this->userMessages[] = $userMessage;

        return $this;
    }

    /**
     * Remove userMessage
     *
     * @param \AppBundle\Entity\Message $userMessage
     */
    public function removeUserMessage(Message $userMessage)
    {
        $this->userMessages->removeElement($userMessage);
    }

    /**
     * Get userMessages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserMessages()
    {
        return $this->userMessages;
    }
}
