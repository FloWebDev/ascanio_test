<?php

namespace App\Entity;

use App\Util\Format;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskListRepository")
 */
class TaskList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $z_order;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="task_list")
     * @ORM\OrderBy({"z_order" = "ASC", "created_at" = "DESC"})
     */
    private $tasks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        // Valeurs par défaut
        $this->created_at = new \DateTime();
        $this->tasks = new ArrayCollection();
    }

    public function __toString()
    {
        return Format::mb_ucfirst($this->name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getZOrder(): ?int
    {
        return $this->z_order;
    }

    public function setZOrder(int $z_order): self
    {
        $this->z_order = $z_order;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        // Ordonnées par "z_order" et "created_at" de manière à avoir toujours les dernières tâches créées 
        // en premières positions pour un même z_order
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setTaskList($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getTaskList() === $this) {
                $task->setTaskList(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
