<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $role_name = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName()
    {
        return implode("", $this->role_name);
    }

    public function setRoleName(array $role_name): self
    {
        $this->role_name = $role_name;

        return $this;
    }
}
