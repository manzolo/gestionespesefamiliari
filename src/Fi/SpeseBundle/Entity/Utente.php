<?php

namespace Fi\SpeseBundle\Entity;

use \Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\Common\Collections\Collection;

/**
 * Utente.
 */
class Utente
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $cognome;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var Collection
     */
    private $movimentos;

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
     * Set nome.
     *
     * @param string $nome
     *
     * @return utente
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set cognome.
     *
     * @param string $cognome
     *
     * @return utente
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;

        return $this;
    }

    /**
     * Get cognome.
     *
     * @return string
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return utente
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return utente
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return utente
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Add movimentos.
     *
     * @param \Fi\SpeseBundle\Entity\movimento $movimentos
     *
     * @return utente
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
    public function removeMovimento(Movimento $movimentos)
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

    public function __toString()
    {
        return $this->nome.' '.$this->cognome;
    }

    public function getNominativo()
    {
        return $this->nome.' '.$this->cognome;
    }

    /**
     * @var int
     */
    private $famiglia_id;

    /**
     * @var \Fi\SpeseBundle\Entity\famiglia
     */
    private $famiglia;

    /**
     * Set famiglia_id.
     *
     * @param int $famigliaId
     *
     * @return utente
     */
    public function setFamigliaId($famigliaId)
    {
        $this->famiglia_id = $famigliaId;

        return $this;
    }

    /**
     * Get famiglia_id.
     *
     * @return int
     */
    public function getFamigliaId()
    {
        return $this->famiglia_id;
    }

    /**
     * Set famiglia.
     *
     * @param \Fi\SpeseBundle\Entity\famiglia $famiglia
     *
     * @return utente
     */
    public function setFamiglia(Famiglia $famiglia = null)
    {
        $this->famiglia = $famiglia;

        return $this;
    }

    /**
     * Get famiglia.
     *
     * @return \Fi\SpeseBundle\Entity\famiglia
     */
    public function getFamiglia()
    {
        return $this->famiglia;
    }
}
