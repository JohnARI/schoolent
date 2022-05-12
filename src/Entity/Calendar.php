<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CalendarRepository;

/**
 * @ORM\Entity(repositoryClass=CalendarRepository::class)
 */
class Calendar
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /*
     *  @ORM\Column(type="integer", nullable=false)
     */
    private $Category;

    /**
     * @ORM\ManyToOne(targetEntity=Session::class, inversedBy="calendars")
     */
    private $session;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

     /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="calendars")
     */
    private $teacher;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $BackgroundColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $teacher_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $teacher_name;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?int
    {
        return $this->Category;
    }

    public function setCategory(?int $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getTeacher(): ?\DateTimeInterface
    {
        return $this->teacher;
    }

    public function setTeacher(\DateTimeInterface $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->BackgroundColor;
    }

    public function setBackgroundColor(string $BackgroundColor): self
    {
        $this->BackgroundColor = $BackgroundColor;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTeacherId(): ?int
    {
        return $this->teacher_id;
    }

    public function setTeacherId(int $teacher_id): self
    {
        $this->teacher_id = $teacher_id;

        return $this;
    }

    public function getTeacherName(): ?string
    {
        return $this->teacher_name;
    }

    public function setTeacherName(?string $teacher_name): self
    {
        $this->teacher_name = $teacher_name;

        return $this;
    }


}
