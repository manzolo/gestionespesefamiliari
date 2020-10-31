<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Fi\SpeseBundle\Entity\Utente
 *
 * @ORM\Entity()
 * @ORM\Table(name="Utente", indexes={@ORM\Index(name="fk_utente_famiglia1_idx", columns={"famiglia_id"})})
 */
class Utente
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $nome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $cognome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $username;

    /**
     * @ORM\Column(name="`password`", type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $famiglia_id;

    /**
     * @ORM\OneToMany(targetEntity="Movimento", mappedBy="utente")
     * @ORM\JoinColumn(name="id", referencedColumnName="utente_id", nullable=false)
     */
    protected $movimentos;

    /**
     * @ORM\ManyToOne(targetEntity="Famiglia", inversedBy="utentes")
     * @ORM\JoinColumn(name="famiglia_id", referencedColumnName="id")
     */
    protected $famiglia;

    public function __construct()
    {
        $this->movimentos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Fi\SpeseBundle\Entity\Utente
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
     * Set the value of nome.
     *
     * @param string $nome
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of cognome.
     *
     * @param string $cognome
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;

        return $this;
    }

    /**
     * Get the value of cognome.
     *
     * @return string
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of username.
     *
     * @param string $username
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of password.
     *
     * @param string $password
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of famiglia_id.
     *
     * @param integer $famiglia_id
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function setFamigliaId($famiglia_id)
    {
        $this->famiglia_id = $famiglia_id;

        return $this;
    }

    /**
     * Get the value of famiglia_id.
     *
     * @return integer
     */
    public function getFamigliaId()
    {
        return $this->famiglia_id;
    }

    /**
     * Add Movimento entity to collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Movimento $movimento
     * @return \Fi\SpeseBundle\Entity\Utente
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
     * @return \Fi\SpeseBundle\Entity\Utente
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
     * Set Famiglia entity (many to one).
     *
     * @param \Fi\SpeseBundle\Entity\Famiglia $famiglia
     * @return \Fi\SpeseBundle\Entity\Utente
     */
    public function setFamiglia(Famiglia $famiglia = null)
    {
        $this->famiglia = $famiglia;

        return $this;
    }

    /**
     * Get Famiglia entity (many to one).
     *
     * @return \Fi\SpeseBundle\Entity\Famiglia
     */
    public function getFamiglia()
    {
        return $this->famiglia;
    }
    public function getNominativo()
    {
        return $this->nome.' '.$this->cognome;
    }
    public function __toString()
    {
        return $this->nome.' '.$this->cognome;
    }    public function __sleep()
    {
        return array('id', 'nome', 'cognome', 'email', 'username', 'password', 'famiglia_id');
    }
}
