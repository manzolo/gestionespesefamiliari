<?php

namespace Fi\SpeseBundle\Entity;

/**
 * Categoria.
 */
class Categoria
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $descrizione;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tipologias;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tipologias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descrizione.
     *
     * @param string $descrizione
     *
     * @return categoria
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get descrizione.
     *
     * @return string
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * Add tipologias.
     *
     * @param \Fi\SpeseBundle\Entity\tipologia $tipologias
     *
     * @return categoria
     */
    public function addTipologia(\Fi\SpeseBundle\Entity\tipologia $tipologias)
    {
        $this->tipologias[] = $tipologias;

        return $this;
    }

    /**
     * Remove tipologias.
     *
     * @param \Fi\SpeseBundle\Entity\tipologia $tipologias
     */
    public function removeTipologia(\Fi\SpeseBundle\Entity\tipologia $tipologias)
    {
        $this->tipologias->removeElement($tipologias);
    }

    /**
     * Get tipologias.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTipologias()
    {
        return $this->tipologias;
    }

    public function __toString()
    {
        return $this->descrizione;
    }
}
