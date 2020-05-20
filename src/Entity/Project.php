<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
    use Common;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(
     *     min="15",
     *     minMessage="Votre titre doit contenir au minimum {{ limit }} caractères.",
     *     max="50",
     *     maxMessage="Votre titre ne doit pas dépasser {{ limit }} caractères."
     * )
     */
    private string $title = '';

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *     min="50",
     *     minMessage="Votre contenu doit contenir au minimum {{ limit }} caractères.",
     *     max="5000",
     *     maxMessage="Votre contenu ne doit pas dépasser {{ limit }} caractères."
     * )
     */
    private string $content = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     */
    private ?User $user = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Project
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Project
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
