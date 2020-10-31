<?php

namespace Fi\SpeseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Fi\SpeseBundle\Entity\Famiglia
 *
 * @ORM\Entity()
 * @ORM\Table(name="Famiglia")
 */
class Famiglia
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
    protected $descrizione;

    /**
     * @ORM\Column(type="date")
     */
    protected $dal;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $al;

    /**
     * @ORM\OneToMany(targetEntity="Utente", mappedBy="famiglia")
     * @ORM\JoinColumn(name="id", referencedColumnName="famiglia_id", nullable=false)
     */
    protected $utentes;

    public function __construct()
    {
        $this->utentes = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Fi\SpeseBundle\Entity\Famiglia
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
     * @return \Fi\SpeseBundle\Entity\Famiglia
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
     * Set the value of dal.
     *
     * @param \DateTime $dal
     * @return \Fi\SpeseBundle\Entity\Famiglia
     */
    public function setDal($dal)
    {
        $this->dal = $dal;

        return $this;
    }

    /**
     * Get the value of dal.
     *
     * @return \DateTime
     */
    public function getDal()
    {
        return $this->dal;
    }

    /**
     * Set the value of al.
     *
     * @param \DateTime $al
     * @return \Fi\SpeseBundle\Entity\Famiglia
     */
    public function setAl($al)
    {
        $this->al = $al;

        return $this;
    }

    /**
     * Get the value of al.
     *
     * @return \DateTime
     */
    public function getAl()
    {
        return $this->al;
    }

    /**
     * Add Utente entity to collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Utente $utente
     * @return \Fi\SpeseBundle\Entity\Famiglia
     */
    public function addUtente(Utente $utente)
    {
        $this->utentes[] = $utente;

        return $this;
    }

    /**
     * Remove Utente entity from collection (one to many).
     *
     * @param \Fi\SpeseBundle\Entity\Utente $utente
     * @return \Fi\SpeseBundle\Entity\Famiglia
     */
    public function removeUtente(Utente $utente)
    {
        $this->utentes->removeElement($utente);

        return $this;
    }

    /**
     * Get Utente entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtentes()
    {
        return $this->utentes;
    }

    public function __sleep()
    {
        return array('id', 'descrizione', 'dal', 'al');
    }
}
