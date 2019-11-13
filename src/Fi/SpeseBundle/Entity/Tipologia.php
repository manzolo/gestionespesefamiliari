<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Tipologia.
 */
class Tipologia
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $categoria_id;

    /**
     * @var string
     */
    private $descrizione;

    /**
     * @var Collection
     */
    private $movimentos;

    /**
     * @var Categoria
     */
    private $categoria;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->movimentos = new ArrayCollection();
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
     * Set categoria_id.
     *
     * @param int $categoriaId
     *
     * @return tipologia
     */
    public function setCategoriaId($categoriaId)
    {
        $this->categoria_id = $categoriaId;

        return $this;
    }

    /**
     * Get categoria_id.
     *
     * @return int
     */
    public function getCategoriaId()
    {
        return $this->categoria_id;
    }

    /**
     * Set descrizione.
     *
     * @param string $descrizione
     *
     * @return tipologia
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
     * Add movimentos.
     *
     * @param \Fi\SpeseBundle\Entity\movimento $movimentos
     *
     * @return tipologia
     */
    public function addMovimento(\Fi\SpeseBundle\Entity\movimento $movimentos)
    {
        $this->movimentos[] = $movimentos;

        return $this;
    }

    /**
     * Remove movimentos.
     *
     * @param \Fi\SpeseBundle\Entity\movimento $movimentos
     */
    public function removeMovimento(\Fi\SpeseBundle\Entity\movimento $movimentos)
    {
        $this->movimentos->removeElement($movimentos);
    }

    /**
     * Get movimentos.
     *
     * @return Collection
     */
    public function getMovimentos()
    {
        return $this->movimentos;
    }

    /**
     * Set categoria.
     *
     * @param \Fi\SpeseBundle\Entity\categoria $categoria
     *
     * @return tipologia
     */
    public function setCategoria(Categoria $categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria.
     *
     * @return \Fi\SpeseBundle\Entity\categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    public function __toString()
    {
        return $this->descrizione.' ('.$this->getCategoria()->getDescrizione().')';
    }
}
