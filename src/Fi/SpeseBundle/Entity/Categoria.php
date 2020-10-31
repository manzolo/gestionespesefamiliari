<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Fi\SpeseBundle\Entity\Categoria
 *
 * @ORM\Entity()
 * @ORM\Table(name="Categoria")
 */
class Categoria
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $descrizione;

    /**
     * @ORM\OneToMany(targetEntity="Tipologia", mappedBy="categoria")
     * @ORM\JoinColumn(name="id", referencedColumnName="categoria_id", nullable=false)
     */
    protected $tipologias;

    public function __construct()
    {
        $this->tipologias = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Fi\SpeseBundle\Entity\Categoria
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of descrizione.
     *
     * @param string $descrizione
     * @return \Fi\SpeseBundle\Entity\Categoria
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get the value of descrizione.
     *
     * @return string
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * Add Tipologia entity to collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Tipologia $tipologia
     * @return \Fi\SpeseBundle\Entity\Categoria
     */
    public function addTipologia(Tipologia $tipologia)
    {
        $this->tipologias[] = $tipologia;

        return $this;
    }

    /**
     * Remove Tipologia entity from collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Tipologia $tipologia
     * @return \Fi\SpeseBundle\Entity\Categoria
     */
    public function removeTipologia(Tipologia $tipologia)
    {
        $this->tipologias->removeElement($tipologia);

        return $this;
    }

    /**
     * Get Tipologia entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTipologias()
    {
        return $this->tipologias;
    }

    public function __sleep()
    {
        return array('id', 'descrizione');
    }
}
