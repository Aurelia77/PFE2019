<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Track
 *
 * @ORM\Table(name="track", indexes={@ORM\Index(name="fk_track_user_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrackRepository")
 */
class Track
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45, nullable=false)
     * @Assert\Length(max=10)
     *
     */
    private $title;

    //    /**
//     * @var string
//     *
//     * @ORM\Column(name="track", type="string", length=255)
//     *
//     * @Assert\NotBlank(message="Veuillez ajouter votre fichier")         !!!??? Mis ds form Newtrack
//     * @Assert\File(
//     *     maxSize = "15M",
//     *     mimeTypes = {"audio/mpeg"},
//     *     mimeTypesMessage = "Veuillez ajouter un type de fichier valide"
//     * )
//     */
//    private $track;

    /**
     * @var string
     *
     * @ORM\Column(name="track", type="string", length=255, nullable=false)
     */
    private $track;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    // user est le propriétaire du track. C'est un OBJET !!! -  Track est l'entité propriétaire.
    // !!! Bien mettre nullable=false sinon par défaut à true (alors que normalement c'est l'inverse !!!)
    // Plusieurs tracks peuvent être liées à Un même utilisateur : Relation ManyToOne bidirectionnelle
    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="$userTracks", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    // Un track est lié à 0, 1, ou plusieurs Message(s) : Relation OneToMany bidirectionnelle
    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="track", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"creationdate" = "DESC"})
     */
    private $trackMessages;


    // track est le propriétaire du mot clef. C'est un OBJET !!! -  Track est l'entité propriétaire.
    // !!! Bien mettre nullable=false sinon par défaut à true (alors que normalement c'est l'inverse !!!)
    // Plusieurs tracks peuvent être liées à Un même mot clef : Relation ManyToOne bidirectionnelle
    /**
     * @var MotClef
     *
     * @ORM\ManyToOne(targetEntity="MotClef", inversedBy="motclefTracks", cascade={"persist"})
     * @ORM\JoinColumn(name="motclef_id", referencedColumnName="id", nullable=true)
     */
    private $trackMotclef;

//    // Tableau d'OBJET !!! Relation MANY to MANY
//    // (on décide que MotClef est l'entité propriétaire donc Track est l'entité inverse donc on ajoute : mappedBy="tracks")
//    // Un track peut être relié à plusieurs mots clefs et inversement : Relation ManyToMany bidirectionnelle
//    // Si on veut personnaliser le nom de la table de jointure/relationnelle     (@ORM\JoinTable(name="participations")
//    /**
//     * @ORM\ManyToMany(targetEntity="MotClef", mappedBy="tracks", cascade={"persist","remove"})
//     */
//    private $motsclefs;

    /**
     * @var integer
     *
     * @ORM\Column(name="num", type="integer", nullable=false)
     */
    private $num;

    // track est un OBJET !!! (de la même entité) Relation ManyToOne
    // On ne met pas nullable=false car peut ne pas avoir de relation avec un track (si compo de base) !!! De tte façon on ne pt pas sur une relation car pas de @ORM\Column...
    // Plusieurs tracks peuvent être liées à Un track (compo de base) : Relation ManyToOne
    /**
     * @var \AppBundle\Entity\Track
     *
     * @ORM\ManyToOne(targetEntity="Track", cascade={"all"})
     * @ORM\JoinColumn(name="id1", referencedColumnName="id")
     */
    private $id1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", nullable=false)
     */
    private $actif;

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


    // CONSTRUCTEUR avec valeur par défaut : la date de création = date + heure du jour
    public function __construct()
    {
        $this->setCreationDate(new \DateTime());
        $this->setActif(1);

        // Ne pas oublier ces tableaux de collection pour pouvoir utiliser removeElement

        // $this->motsclefs = new ArrayCollection();             // Si Relation ManyToMany avec MotClef ?

        // Relation OneToMany avec Message
        $this->trackMessages = new ArrayCollection();

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
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Track
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
     * @return Track
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
     * @param User $user
     *
     * @return Track
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    // Pour la relation ManyToOne avec Track (même entité) (champ $id1 plus haut) : 1 getter et 1 setter
    /**
     * Set id1
     *
     * @param Track $id1
     *
     * @return Track
     */
    public function setId1(Track $id1)
    {
        $this->id1 = $id1;

        return $this;
    }

    /**
     * Get id1
     *
     * @return Track
     */
    public function getId1()
    {
        return $this->id1;
    }


    // Pour la relation OneToMany avec Message : Add, Remove, Get (pas de setter car se fait dans Message = entité propriétaire)
    /**
     * Add trackMessage
     *
     * @param \AppBundle\Entity\Message $trackMessage
     *
     * @return Track
     */
    public function addTrackMessage(Message $trackMessage)
    {
        $this->trackMessages[] = $trackMessage;

        return $this;
    }

    /**
     * Remove trackMessage
     *
     * @param \AppBundle\Entity\Message $trackMessage
     */
    public function removeTrackMessage(Message $trackMessage)
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


    // Pour la relation ManyToOne avec MotClef (champ $trackMotclef plus haut) : 1 getter et 1 setter

    /**
     * Set trackMotclef
     *
     * @param MotClef $trackMotclef
     *
     * @return Track
     */
    public function setTrackMotclef(MotClef $trackMotclef)
    {
        $this->user = $trackMotclef;

        return $this;
    }

    /**
     * Get trackMotclef
     *
     * @return MotClef
     */
    public function getTrackMotclef()
    {
        return $this->trackMotclef;
    }
}
