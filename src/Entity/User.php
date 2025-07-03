<?php

namespace App\Entity;

use Deprecated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Collection getTemplates()
 * @method bool isBlocked()
 */
#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private string $email;

    #[ORM\Column]
    private string $password;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private bool $isBlocked = false;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Template::class)]
    private Collection $templates;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    #[Deprecated('since Symfony 7.3, moved to __serialize()')]
    public function eraseCredentials(): void
    {
        
    }

    public function getTemplates(): Collection
    {
        return $this->templates;
    }
}