<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// @codingStandardsIgnoreStart
/**
 * Fi\SpeseBundle\Entity\Movimento
 *
 * @ORM\Entity()
 * @ORM\Table(name="Movimento",
 *  indexes={@ORM\Index(name="fk_spesa_tipologia1_idx", columns={"tipologia_id"}), @ORM\Index(name="fk_pagamento_utente1_idx", columns={"utente_id"}), @ORM\Index(name="fk_movimento_tipomovimento1_idx", columns={"tipomovimento_id"})})
 */
// @codingStandardsIgnoreEnd
class Movimento
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
    protected $tipologia_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $utente_id;

    /**
     * @ORM\Column(type="float", precision=10, scale=2)
     */
    protected $importo;

    /**
     * @ORM\Column(name="`data`", type="date")
     */
    protected $data;

    /**
     * @ORM\Column(type="string", length=4000, nullable=true)
     */
    protected $nota;

    /**
     * @ORM\Column(type="integer")
     */
    protected $tipomovimento_id;

    /**
     * @ORM\ManyToOne(targetEntity="Tipologia", inversedBy="movimentos")
     * @ORM\JoinColumn(name="tipologia_id", referencedColumnName="id", nullable=false)
     */
    protected $tipologia;

    /**
     * @ORM\ManyToOne(targetEntity="Utente", inversedBy="movimentos")
     * @ORM\JoinColumn(name="utente_id", referencedColumnName="id", nullable=false)
     */
    protected $utente;

    /**
     * @ORM\ManyToOne(targetEntity="Tipomovimento", inversedBy="movimentos")
     * @ORM\JoinColumn(name="tipomovimento_id", referencedColumnName="id", nullable=false)
     */
    protected $tipomovimento;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Fi\SpeseBundle\Entity\Movimento
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
     * Set the value of tipologia_id.
     *
     * @param integer $tipologia_id
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setTipologiaId($tipologia_id)
    {
        $this->tipologia_id = $tipologia_id;

        return $this;
    }

    /**
     * Get the value of tipologia_id.
     *
     * @return integer
     */
    public function getTipologiaId()
    {
        return $this->tipologia_id;
    }

    /**
     * Set the value of utente_id.
     *
     * @param integer $utente_id
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setUtenteId($utente_id)
    {
        $this->utente_id = $utente_id;

        return $this;
    }

    /**
     * Get the value of utente_id.
     *
     * @return integer
     */
    public function getUtenteId()
    {
        return $this->utente_id;
    }

    /**
     * Set the value of importo.
     *
     * @param float $importo
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setImporto($importo)
    {
        $this->importo = $importo;

        return $this;
    }

    /**
     * Get the value of importo.
     *
     * @return float
     */
    public function getImporto()
    {
        return $this->importo;
    }

    /**
     * Set the value of data.
     *
     * @param \DateTime $data
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of data.
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of nota.
     *
     * @param string $nota
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get the value of nota.
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set the value of tipomovimento_id.
     *
     * @param integer $tipomovimento_id
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setTipomovimentoId($tipomovimento_id)
    {
        $this->tipomovimento_id = $tipomovimento_id;

        return $this;
    }

    /**
     * Get the value of tipomovimento_id.
     *
     * @return integer
     */
    public function getTipomovimentoId()
    {
        return $this->tipomovimento_id;
    }

    /**
     * Set Tipologia entity (many to one).
     *
     * @param \Fi\SpeseBundle\Entity\Tipologia $tipologia
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setTipologia(Tipologia $tipologia = null)
    {
        $this->tipologia = $tipologia;

        return $this;
    }

    /**
     * Get Tipologia entity (many to one).
     *
     * @return \Fi\SpeseBundle\Entity\Tipologia
     */
    public function getTipologia()
    {
        return $this->tipologia;
    }

    /**
     * Set Utente entity (many to one).
     *
     * @param \Fi\SpeseBundle\Entity\Utente $utente
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setUtente(Utente $utente = null)
    {
        $this->utente = $utente;

        return $this;
    }

    /**
     * Get Utente entity (many to one).
     *
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function getUtente()
    {
        return $this->utente;
    }

    /**
     * Set Tipomovimento entity (many to one).
     *
     * @param \Fi\SpeseBundle\Entity\Tipomovimento $tipomovimento
     * @return \Fi\SpeseBundle\Entity\Movimento
     */
    public function setTipomovimento(Tipomovimento $tipomovimento = null)
    {
        $this->tipomovimento = $tipomovimento;

        return $this;
    }

    /**
     * Get Tipomovimento entity (many to one).
     *
     * @return \Fi\SpeseBundle\Entity\Tipomovimento
     */
    public function getTipomovimento()
    {
        return $this->tipomovimento;
    }
    public function getDescrizionecategoria()
    {
        return $this->getTipologia()->getCategoria()->getDescrizione();
    }
    public function __sleep()
    {
        return array('id', 'tipologia_id', 'utente_id', 'importo', 'data', 'nota', 'tipomovimento_id');
    }
}
