<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotNull
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $corps;

    // user est le propriétaire du message. C'est un objet !!!
    /**
     * Plusieurs messages peuvent être liées à Un même utilisateur : Relation ManyToOne bidirectionnelle
     * @ORM\ManyToOne(targetEntity="User", inversedBy="$userTracks", cascade={"persist"})
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=false)
     */
    protected $user;

    // track est le son dont parle le message. C'est un objet !!!
    /**
     * Plusieurs messages peuvent être liées à Un même track : Relation ManyToOne bidirectionnelle
     * @ORM\ManyToOne(targetEntity="Track", inversedBy="$userMessages", cascade={"persist"})
     * @ORM\JoinColumn(name="track", referencedColumnName="id", nullable=false)
     */
    protected $track;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=true)
     */
    private $creationDate;

    // On créé le Constructeur avec la date de création = date + heure du jour
    public function __construct()
    {
        $this->setCreationDate(new \DateTime());
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Message
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


}

