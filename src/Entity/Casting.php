<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CastingRepository")
 */
class Casting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",)
     */
    private $character_name;

    /**
     * @ORM\Column(type="integer") 
     */
    private $order_credit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="casting")
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person", inversedBy="castings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCharacterName(): ?string
    {
        return $this->character_name;
    }

    public function setCharacterName(string $character_name): self
    {
        $this->character_name = $character_name;

        return $this;
    }

    public function getOrderCredit(): ?int
    {
        return $this->order_credit;
    }

    public function setOrderCredit(int $order_credit): self
    {
        $this->order_credit = $order_credit;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function __toString()
    {
        return $this->character_name;
    }

}
