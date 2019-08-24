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



//    OBJET !!!
    /**
     * @var int
     *
     * @ORM\Column(name="track", type="integer")
     */
    private $track;




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
     * Set track
     *
     * @param integer $track
     *
     * @return MotClef
     */
    public function setTrack($track)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * Get track
     *
     * @return int
     */
    public function getTrack()
    {
        return $this->track;
    }
}

