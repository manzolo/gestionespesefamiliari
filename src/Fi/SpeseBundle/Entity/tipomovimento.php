<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tipomovimento
 */
class tipomovimento {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var string
     */
    private $abbreviazione;

    /**
     * @var string
     */
    private $segno;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $movimentos;

    /**
     * Constructor
     */
    public function __construct() {
        $this->movimentos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tipo
     *
     * @param string $tipo
     * @return tipomovimento
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * Set abbreviazione
     *
     * @param string $abbreviazione
     * @return tipomovimento
     */
    public function setAbbreviazione($abbreviazione) {
        $this->abbreviazione = $abbreviazione;

        return $this;
    }

    /**
     * Get abbreviazione
     *
     * @return string 
     */
    public function getAbbreviazione() {
        return $this->abbreviazione;
    }

    /**
     * Set segno
     *
     * @param string $segno
     * @return tipomovimento
     */
    public function setSegno($segno) {
        $this->segno = $segno;

        return $this;
    }

    /**
     * Get segno
     *
     * @return string 
     */
    public function getSegno() {
        return $this->segno;
    }

    /**
     * Add movimentos
     *
     * @param \Fi\SpeseBundle\Entity\movimento $movimentos
     * @return tipomovimento
     */
    public function addMovimento(\Fi\SpeseBundle\Entity\movimento $movimentos) {
        $this->movimentos[] = $movimentos;

        return $this;
    }

    /**
     * Remove movimentos
     *
     * @param \Fi\SpeseBundle\Entity\movimento $movimentos
     */
    public function removeMovimento(\Fi\SpeseBundle\Entity\movimento $movimentos) {
        $this->movimentos->removeElement($movimentos);
    }

    /**
     * Get movimentos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimentos() {
        return $this->movimentos;
    }

    public function __toString() {
        return $this->tipo;
    }

    
}
