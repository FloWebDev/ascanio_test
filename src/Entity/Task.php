<?php

namespace App\Entity;

use App\Util\Format;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $z_order;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TaskList", inversedBy="tasks")
     */
    private $task_list;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Priority", inversedBy="tasks")
     */
    private $priority;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        // Valeurs par défaut
        $this->created_at = new \DateTime();
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

    public function setZOrder(?int $z_order): self
    {
        $this->z_order = $z_order;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTaskList(): ?TaskList
    {
        return $this->task_list;
    }

    public function setTaskList(?TaskList $task_list): self
    {
        $this->task_list = $task_list;

        return $this;
    }

    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    public function setPriority(?Priority $priority): self
    {
        $this->priority = $priority;

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
