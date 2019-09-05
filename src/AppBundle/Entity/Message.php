<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Message
 *
 * @ORM\Table(name="message", indexes={@ORM\Index(name="fk_message_user1_idx", columns={"user_id"}), @ORM\Index(name="fk_message_track1_idx", columns={"track_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var string
     *
     * @ORM\Column(name="corps", type="string", length=255, nullable=false)
     */
    private $corps;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", nullable=false)
     */
    private $actif;

    // user est le propriétaire du message. C'est un OBJET !!! - Message est l'entité propriétaire.
    // !!! Bien mettre nullable=false sinon par défaut à true (alors que normalement c'est l'inverse !!!)
    // Plusieurs messages peuvent être liées à Un même utilisateur : Relation ManyToOne bidirectionnelle
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="$userMessages", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    // track est le son (propriétaire) dont parle le message. C'est un OBJET !!! - Message est l'entité propriétaire.
    // !!! Bien mettre nullable=false sinon par défaut à true (alors que normalement c'est l'inverse !!!)
    // Plusieurs messages peuvent être liées à Un même track : Relation ManyToOne bidirectionnelle
    /**
     * @ORM\ManyToOne(targetEntity="Track", inversedBy="$trackMessages", cascade={"persist"})
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id", nullable=false)
     */
    private $track;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=false)
     */
    private $creationdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    // OONSTRUCTIEUR avec valeurs par défaut : date de création = date/heure actuelles + actif = 1
    public function __construct()
    {
        $this->setActif(1);
        $this->setCreationDate(new \DateTime());
    }


    /**
     * Set corps
     *
     * @param string $corps
     *
     * @return Message
     */
    public function setCorps($corps)
    {
        $this->corps = $corps;

        return $this;
    }

    /**
     * Get corps
     *
     * @return string
     */
    public function getCorps()
    {
        return $this->corps;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Message
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Message
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }




    // Pour la relation ManyToOne avec User (champ $user plus haut) : 1 getter et 1 setter
    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Message
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    // Pour la relation ManyToOne avec Track (champ $track plus haut) : 1 getter et 1 setter
    /**
     * Set track
     *
     * @param \AppBundle\Entity\Track $track
     *
     * @return Message
     */
    public function setTrack(Track $track)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * Get track
     *
     * @return \AppBundle\Entity\Track
     */
    public function getTrack()
    {
        return $this->track;
    }
}
