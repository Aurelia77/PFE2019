<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Track
 *
 * @ORM\Table(name="track")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrackRepository")
 */
class Track
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;


// user est le propriétaire du track. C'est un OBJET !!!
// Track est l'entité propriétaire.
// !!! Bien mettre nullable=false sinon par défaut à true (alors que normalement c'est l'inverse)
    /**
     * Plusieurs tracks peuvent être liées à Un même utilisateur : Relation ManyToOne bidirectionnelle
     * @ORM\ManyToOne(targetEntity="User", inversedBy="$userTracks", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;


    /**
     * Un track est lié à 0, 1, ou plusieurs Message(s) : Relation OneToMany bidirectionnelle
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="track", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $trackMessages;


//    Tableau d'OBJET !!! Relation MANY to MANY (on décide que MotClef est l'entité propriétaire)
    /**
     * Un track peut être relié à plusieurs mots clefs et inversement : Relation ManyToMany bidirectionnelle
     *
     * @ORM\ManyToMany(targetEntity="MotClef", mappedBy="tracks", cascade={"persist","remove"})
     */
    protected $motsclefs;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $track;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $image;


    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $num;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;


//    /**
//     * @var string
//     *
//     * @ORM\Column(name="track", type="string", length=255)
//     *
//     * @Assert\NotBlank(message="Veuillez ajouter votre fichier")
//     * @Assert\File(
//     *     maxSize = "15M",
//     *     mimeTypes = {"audio/mpeg"},
//     *     mimeTypesMessage = "Veuillez ajouter un type de fichier valide"
//     * )
//     */
//    private $track;


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
     * @return Track
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

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Track
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set track
     *
     * @param string $track
     *
     * @return Track
     */
    public function setTrack($track)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * Get track
     *
     * @return string
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Track
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }




    /**
     * Set num
     *
     * @param integer $num
     *
     * @return Track
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set id1
     *
     * @param integer $id1
     *
     * @return Track
     */
    public function setId1($id1)
    {
        $this->id1 = $id1;

        return $this;
    }

    /**
     * Get id1
     *
     * @return integer
     */
    public function getId1()
    {
        return $this->id1;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Track
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
     * Add motsclef
     *
     * @param \AppBundle\Entity\Track $motsclef
     *
     * @return Track
     */
    public function addMotsclef(Track $motsclef)
    {
        $this->motsclefs[] = $motsclef;

        return $this;
    }

    /**
     * Remove motsclef
     *
     * @param \AppBundle\Entity\Track $motsclef
     */
    public function removeMotsclef(Track $motsclef)
    {
        $this->motsclefs->removeElement($motsclef);
    }

    /**
     * Get motsclefs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMotsclefs()
    {
        return $this->motsclefs;
    }

    /**
     * Add userMessage
     *
     * @param \AppBundle\Entity\Message $userMessage
     *
     * @return Track
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

    /**
     * Add trackMessage
     *
     * @param \AppBundle\Entity\Message $trackMessage
     *
     * @return Track
     */
    public function addTrackMessage(\AppBundle\Entity\Message $trackMessage)
    {
        $this->trackMessages[] = $trackMessage;

        return $this;
    }

    /**
     * Remove trackMessage
     *
     * @param \AppBundle\Entity\Message $trackMessage
     */
    public function removeTrackMessage(\AppBundle\Entity\Message $trackMessage)
    {
        $this->trackMessages->removeElement($trackMessage);
    }

    /**
     * Get trackMessages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTrackMessages()
    {
        return $this->trackMessages;
    }
}
