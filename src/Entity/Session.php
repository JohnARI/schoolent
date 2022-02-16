<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SessionRepository::class)
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startSession;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endSession;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="session")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Calendar::class, mappedBy="session")
     */
    private $calendars;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->calendars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartSession(): ?\DateTimeInterface
    {
        return $this->startSession;
    }

    public function setStartSession(\DateTimeInterface $startSession): self
    {
        $this->startSession = $startSession;

        return $this;
    }

    public function getEndSession(): ?\DateTimeInterface
    {
        return $this->endSession;
    }

    public function setEndSession(\DateTimeInterface $endSession): self
    {
        $this->endSession = $endSession;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setSession($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSession() === $this) {
                $user->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Calendar[]
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): self
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars[] = $calendar;
            $calendar->setSession($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): self
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getSession() === $this) {
                $calendar->setSession(null);
            }
        }

        return $this;
    }

    // public function __toString()
    // {
    //     return $this->name;
    // }
}
