<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Fi\SpeseBundle\Entity\Tipologia
 *
 * @ORM\Entity()
 * @ORM\Table(name="Tipologia", indexes={@ORM\Index(name="fk_subcategoria_categoria_idx", columns={"categoria_id"})})
 */
class Tipologia
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $categoria_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $descrizione;

    /**
     * @ORM\OneToMany(targetEntity="Movimento", mappedBy="tipologia")
     * @ORM\JoinColumn(name="id", referencedColumnName="tipologia_id", nullable=false)
     */
    protected $movimentos;

    /**
     * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="tipologias")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id", nullable=false)
     */
    protected $categoria;

    public function __construct()
    {
        $this->movimentos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Fi\SpeseBundle\Entity\Tipologia
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
     * Set the value of categoria_id.
     *
     * @param integer $categoria_id
     * @return \Fi\SpeseBundle\Entity\Tipologia
     */
    public function setCategoriaId($categoria_id)
    {
        $this->categoria_id = $categoria_id;

        return $this;
    }

    /**
     * Get the value of categoria_id.
     *
     * @return integer
     */
    public function getCategoriaId()
    {
        return $this->categoria_id;
    }

    /**
     * Set the value of descrizione.
     *
     * @param string $descrizione
     * @return \Fi\SpeseBundle\Entity\Tipologia
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
     * Add Movimento entity to collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Movimento $movimento
     * @return \Fi\SpeseBundle\Entity\Tipologia
     */
    public function addMovimento(Movimento $movimento)
    {
        $this->movimentos[] = $movimento;

        return $this;
    }

    /**
     * Remove Movimento entity from collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Movimento $movimento
     * @return \Fi\SpeseBundle\Entity\Tipologia
     */
    public function removeMovimento(Movimento $movimento)
    {
        $this->movimentos->removeElement($movimento);

        return $this;
    }

    /**
     * Get Movimento entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovimentos()
    {
        return $this->movimentos;
    }

    /**
     * Set Categoria entity (many to one).
     *
     * @param \Fi\SpeseBundle\Entity\Categoria $categoria
     * @return \Fi\SpeseBundle\Entity\Tipologia
     */
    public function setCategoria(Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get Categoria entity (many to one).
     *
     * @return \Fi\SpeseBundle\Entity\Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    public function __sleep()
    {
        return array('id', 'categoria_id', 'descrizione');
    }
}
