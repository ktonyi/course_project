<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'templates')]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column]
    private string $topic;

    #[ORM\Column(type: 'json')]
    private array $tags = [];

    #[ORM\Column]
    private bool $isPublic = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'templates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'template_allowed_users')]
    private Collection $allowedUsers;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Form::class)]
    private Collection $forms;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Question::class, cascade: ['persist', 'remove'])]
    private Collection $questions;

    public function __construct()
    {
        $this->allowedUsers = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getAllowedUsers(): Collection
    {
        return $this->allowedUsers;
    }

    public function addAllowedUser(User $user): self
    {
        if (!$this->allowedUsers->contains($user)) {
            $this->allowedUsers->add($user);
        }
        return $this;
    }

    public function removeAllowedUser(User $user): self
    {
        $this->allowedUsers->removeElement($user);
        return $this;
    }

    public function getForms(): Collection
    {
        return $this->forms;
    }

    public function addForm(Form $form): self
    {
        if (!$this->forms->contains($form)) {
            $this->forms->add($form);
            $form->setTemplate($this);
        }
        return $this;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setTemplate($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
        }
        return $this;
    }
}