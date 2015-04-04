<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * famiglia
 */
class famiglia {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descrizione;

    /**
     * @var \DateTime
     */
    private $dal;

    /**
     * @var \DateTime
     */
    private $al;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $utentes;

    /**
     * Constructor
     */
    public function __construct() {
        $this->utentes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set descrizione
     *
     * @param string $descrizione
     * @return famiglia
     */
    public function setDescrizione($descrizione) {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get descrizione
     *
     * @return string 
     */
    public function getDescrizione() {
        return $this->descrizione;
    }

    /**
     * Set dal
     *
     * @param \DateTime $dal
     * @return famiglia
     */
    public function setDal($dal) {
        $this->dal = $dal;

        return $this;
    }

    /**
     * Get dal
     *
     * @return \DateTime 
     */
    public function getDal() {
        return $this->dal;
    }

    /**
     * Set al
     *
     * @param \DateTime $al
     * @return famiglia
     */
    public function setAl($al) {
        $this->al = $al;

        return $this;
    }

    /**
     * Get al
     *
     * @return \DateTime 
     */
    public function getAl() {
        return $this->al;
    }

    /**
     * Add utentes
     *
     * @param \Fi\SpeseBundle\Entity\utente $utentes
     * @return famiglia
     */
    public function addUtente(\Fi\SpeseBundle\Entity\utente $utentes) {
        $this->utentes[] = $utentes;

        return $this;
    }

    /**
     * Remove utentes
     *
     * @param \Fi\SpeseBundle\Entity\utente $utentes
     */
    public function removeUtente(\Fi\SpeseBundle\Entity\utente $utentes) {
        $this->utentes->removeElement($utentes);
    }

    /**
     * Get utentes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUtentes() {
        return $this->utentes;
    }

    public function __toString() {
        return $this->descrizione;
    }

}
