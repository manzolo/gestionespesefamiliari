<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tipologia
 */
class tipologia
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $categoria_id;

    /**
     * @var string
     */
    private $descrizione;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $movimentos;

    /**
     * @var \Fi\SpeseBundle\Entity\categoria
     */
    private $categoria;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movimentos = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set categoria_id
     *
     * @param integer $categoriaId
     * @return tipologia
     */
    public function setCategoriaId($categoriaId)
    {
        $this->categoria_id = $categoriaId;

        return $this;
    }

    /**
     * Get categoria_id
     *
     * @return integer 
     */
    public function getCategoriaId()
    {
        return $this->categoria_id;
    }

    /**
     * Set descrizione
     *
     * @param string $descrizione
     * @return tipologia
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get descrizione
     *
     * @return string 
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * Add movimentos
     *
     * @param \Fi\SpeseBundle\Entity\movimento $movimentos
     * @return tipologia
     */
    public function addMovimento(\Fi\SpeseBundle\Entity\movimento $movimentos)
    {
        $this->movimentos[] = $movimentos;

        return $this;
    }

    /**
     * Remove movimentos
     *
     * @param \Fi\SpeseBundle\Entity\movimento $movimentos
     */
    public function removeMovimento(\Fi\SpeseBundle\Entity\movimento $movimentos)
    {
        $this->movimentos->removeElement($movimentos);
    }

    /**
     * Get movimentos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimentos()
    {
        return $this->movimentos;
    }

    /**
     * Set categoria
     *
     * @param \Fi\SpeseBundle\Entity\categoria $categoria
     * @return tipologia
     */
    public function setCategoria(\Fi\SpeseBundle\Entity\categoria $categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Fi\SpeseBundle\Entity\categoria 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }
    
        public function __toString() {
        return $this->descrizione;
    }


}
