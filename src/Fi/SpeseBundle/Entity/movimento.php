<?php

namespace Fi\SpeseBundle\Entity;

/**
 * movimento.
 */
class movimento
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $tipologia_id;

    /**
     * @var int
     */
    private $utente_id;

    /**
     * @var float
     */
    private $importo;

    /**
     * @var \DateTime
     */
    private $data;

    /**
     * @var string
     */
    private $nota;

    /**
     * @var int
     */
    private $tipomovimento_id;

    /**
     * @var \Fi\SpeseBundle\Entity\tipologia
     */
    private $tipologia;

    /**
     * @var \Fi\SpeseBundle\Entity\utente
     */
    private $utente;

    /**
     * @var \Fi\SpeseBundle\Entity\tipomovimento
     */
    private $tipomovimento;

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
     * Set tipologia_id.
     *
     * @param int $tipologiaId
     *
     * @return movimento
     */
    public function setTipologiaId($tipologiaId)
    {
        $this->tipologia_id = $tipologiaId;

        return $this;
    }

    /**
     * Get tipologia_id.
     *
     * @return int
     */
    public function getTipologiaId()
    {
        return $this->tipologia_id;
    }

    /**
     * Set utente_id.
     *
     * @param int $utenteId
     *
     * @return movimento
     */
    public function setUtenteId($utenteId)
    {
        $this->utente_id = $utenteId;

        return $this;
    }

    /**
     * Get utente_id.
     *
     * @return int
     */
    public function getUtenteId()
    {
        return $this->utente_id;
    }

    /**
     * Set importo.
     *
     * @param float $importo
     *
     * @return movimento
     */
    public function setImporto($importo)
    {
        $this->importo = $importo;

        return $this;
    }

    /**
     * Get importo.
     *
     * @return float
     */
    public function getImporto()
    {
        return $this->importo;
    }

    /**
     * Set data.
     *
     * @param \DateTime $data
     *
     * @return movimento
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data.
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set nota.
     *
     * @param string $nota
     *
     * @return movimento
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota.
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set tipomovimento_id.
     *
     * @param int $tipomovimentoId
     *
     * @return movimento
     */
    public function setTipomovimentoId($tipomovimentoId)
    {
        $this->tipomovimento_id = $tipomovimentoId;

        return $this;
    }

    /**
     * Get tipomovimento_id.
     *
     * @return int
     */
    public function getTipomovimentoId()
    {
        return $this->tipomovimento_id;
    }

    /**
     * Set tipologia.
     *
     * @param \Fi\SpeseBundle\Entity\tipologia $tipologia
     *
     * @return movimento
     */
    public function setTipologia(\Fi\SpeseBundle\Entity\tipologia $tipologia)
    {
        $this->tipologia = $tipologia;

        return $this;
    }

    /**
     * Get tipologia.
     *
     * @return \Fi\SpeseBundle\Entity\tipologia
     */
    public function getTipologia()
    {
        return $this->tipologia;
    }

    /**
     * Set utente.
     *
     * @param \Fi\SpeseBundle\Entity\utente $utente
     *
     * @return movimento
     */
    public function setUtente(\Fi\SpeseBundle\Entity\utente $utente)
    {
        $this->utente = $utente;

        return $this;
    }

    /**
     * Get utente.
     *
     * @return \Fi\SpeseBundle\Entity\utente
     */
    public function getUtente()
    {
        return $this->utente;
    }

    /**
     * Set tipomovimento.
     *
     * @param \Fi\SpeseBundle\Entity\tipomovimento $tipomovimento
     *
     * @return movimento
     */
    public function setTipomovimento(\Fi\SpeseBundle\Entity\tipomovimento $tipomovimento)
    {
        $this->tipomovimento = $tipomovimento;

        return $this;
    }

    /**
     * Get tipomovimento.
     *
     * @return \Fi\SpeseBundle\Entity\tipomovimento
     */
    public function getTipomovimento()
    {
        return $this->tipomovimento;
    }

    public function getDescrizionecategoria()
    {
        return $this->getTipologia()->getCategoria()->getDescrizione();
    }
}
