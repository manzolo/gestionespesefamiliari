<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Fi\SpeseBundle\Entity\Tipomovimento
 *
 * @ORM\Entity()
 * @ORM\Table(name="Tipomovimento")
 */
class Tipomovimento
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $tipo;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $abbreviazione;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $segno;

    /**
     * @ORM\OneToMany(targetEntity="Movimento", mappedBy="tipomovimento")
     * @ORM\JoinColumn(name="id", referencedColumnName="tipomovimento_id", nullable=false)
     */
    protected $movimentos;

    public function __construct()
    {
        $this->movimentos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Fi\SpeseBundle\Entity\Tipomovimento
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
     * Set the value of tipo.
     *
     * @param string $tipo
     * @return \Fi\SpeseBundle\Entity\Tipomovimento
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of tipo.
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set the value of abbreviazione.
     *
     * @param string $abbreviazione
     * @return \Fi\SpeseBundle\Entity\Tipomovimento
     */
    public function setAbbreviazione($abbreviazione)
    {
        $this->abbreviazione = $abbreviazione;

        return $this;
    }

    /**
     * Get the value of abbreviazione.
     *
     * @return string
     */
    public function getAbbreviazione()
    {
        return $this->abbreviazione;
    }

    /**
     * Set the value of segno.
     *
     * @param string $segno
     * @return \Fi\SpeseBundle\Entity\Tipomovimento
     */
    public function setSegno($segno)
    {
        $this->segno = $segno;

        return $this;
    }

    /**
     * Get the value of segno.
     *
     * @return string
     */
    public function getSegno()
    {
        return $this->segno;
    }

    /**
     * Add Movimento entity to collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Movimento $movimento
     * @return \Fi\SpeseBundle\Entity\Tipomovimento
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
     * @return \Fi\SpeseBundle\Entity\Tipomovimento
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

    public function __sleep()
    {
        return array('id', 'tipo', 'abbreviazione', 'segno');
    }
}
