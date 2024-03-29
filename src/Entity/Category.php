<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    use Common;

    /**
     * @ORM\Column(name="title", type="string", length=64)
     */
    public string $title = '';

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    public int $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    public int $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    public int $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    public Category $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    public ?Category $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    public Collection $children;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="categories")
     */
    public Collection $projects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="categories")
     */
    public Collection $users;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoot(): Category
    {
        return $this->root;
    }

    public function setParent(Category $parent = null): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent(): ? Category
    {
        return $this->parent;
    }

    public function addChild(Category $category): self
    {
        if (!$this->children->contains($category)) {
            $this->children[] = $category;
            $category->setParent($this);
        }

        return $this;
    }

    public function removeChild(Category $category): self
    {
        if ($this->children->contains($category)) {
            $this->children->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getParent() === $this) {
                $category->setParent(null);
            }
        }

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addCategory($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCategory($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeCategory($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
