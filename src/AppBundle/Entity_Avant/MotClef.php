<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotClef
 *
 * @ORM\Table(name="motclef")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MotClefRepository")
 */
class MotClef
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
     * @ORM\Column(name="mot", type="string", length=255, unique=true)
     */
    private $mot;



//    Tableau d'OBJET !!! Relation MANY to MANY (on décide que MotClef est l'entité propriétaire)
    /**
     * Un mot clef peut être relié à plusieurs tracks et inversement
     *
     * @ORM\ManyToMany(targetEntity="Track", inversedBy="motsclefs", cascade={"persist","remove"})
     */
    protected $tracks;




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
     * Constructor
     */
    public function __construct()
    {
        $this->tracks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add track
     *
     * @param \AppBundle\Entity\User $track
     *
     * @return MotClef
     */
    public function addTrack(User $track)
    {
        $this->tracks[] = $track;

        return $this;
    }

    /**
     * Remove track
     *
     * @param \AppBundle\Entity\User $track
     */
    public function removeTrack(User $track)
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
