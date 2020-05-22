<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="projects")
     */
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

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

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
