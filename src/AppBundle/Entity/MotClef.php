<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Motclef
 *
 * @ORM\Table(name="motclef")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MotClefRepository")
 */
class MotClef
{
    /**
     * @var string
     *
     * @ORM\Column(name="mot", type="string", length=45, nullable=false)
     * @Assert\Length(min=2)
     */
    private $mot;

    // Tableau d'OBJET !!! Relation MANY to MANY
    // (on décide que MotClef est l'entité propriétaire donc on ajoute : inversedBy="motsclefs")
    // donc Track est l'entité inverse
    // Un mot clef peut être relié à plusieurs tracks et inversement : Relation ManyToMany bidirectionnelle
    /**
     * @ORM\ManyToMany(targetEntity="Track", inversedBy="motsclefs", cascade={"persist","remove"})
     * @ORM\OrderBy({"creationdate" = "DESC"})
     * @ORM\JoinTable(name="mot_clef_track",
     *     joinColumns={@ORM\JoinColumn(name="motclef_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="track_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")})
     */
    private $tracks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", nullable=false)
     */
    private $actif;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;



    // CONSTRUCTEUR avec un tableau de collection car c'est l'entité propriétaire (pour la table relationnelle mot_clef_track ???)
    public function __construct()
    {
        $this->tracks = new ArrayCollection();
        $this->setActif(1);
    }

    /**
     * Set mot
     *
     * @param string $mot
     *
     * @return MotClef
     */
    public function setMot($mot)
    {
        $this->mot = $mot;

        return $this;
    }

    /**
     * Get mot
     *
     * @return string
     */
    public function getMot()
    {
        return $this->mot;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return MotClef
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    // Pour la relation ManyToMany avec Track : Add, Remove, Get (pas de setter car se fait avec Track = entité inverse (selon ce qu'on a décidé))
    /**
     * Add track
     *
     * @param \AppBundle\Entity\Track $track
     *
     * @return MotClef
     */
    public function addTrack(Track $track)
    {
        $this->tracks[] = $track;

        return $this;
    }

    /**
     * Remove track
     *
     * @param \AppBundle\Entity\Track $track
     */
    public function removeTrack(Track $track)
    {
        $this->tracks->removeElement($track);
    }

    /**
     * Get tracks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTracks()
    {
        return $this->tracks;
    }
}
