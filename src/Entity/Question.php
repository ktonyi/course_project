<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'questions')]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Template::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private Template $template;

    #[ORM\Column(type: 'string', enumType: QuestionTypeEnum::class)]
    private QuestionTypeEnum $type;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column]
    private bool $isVisibleInResults = true;

    #[ORM\Column]
    private int $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): self
    {
        $this->template = $template;
        return $this;
    }

    public function getType(): QuestionTypeEnum
    {
        return $this->type;
    }

    public function setType(QuestionTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
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

    public function isVisibleInResults(): bool
    {
        return $this->isVisibleInResults;
    }

    public function setIsVisibleInResults(bool $isVisibleInResults): self
    {
        $this->isVisibleInResults = $isVisibleInResults;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }
}