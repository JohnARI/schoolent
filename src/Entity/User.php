<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     *@var string
     */
    private $role;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=Grade::class, mappedBy="user",cascade={"remove"})
     */
    private $grades;

    /**
     * @ORM\ManyToOne(targetEntity=Session::class, inversedBy="user")
     */
    private $session;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checking;

    /**
     * (le mapped a été modifié de teacher a session)
     * @ORM\OneToMany(targetEntity=Calendar::class, mappedBy="session", orphanRemoval=true)
     */
    private $calendars;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender", orphanRemoval=true)
     */
    private $sent;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="recipient", orphanRemoval=true)
     */
    private $received;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Payment::class, inversedBy="teacher")
     */
    private $payment;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sexe;

    /**
     * @ORM\OneToMany(targetEntity=Grade::class, mappedBy="teacher")
     */
    private $gradesTeacher;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isTeacher;



    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->setRoles(['ROLE_USER']);
        $this->createdAt = $this->updatedAt = new \DateTime();
        $this->notifications = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->calendars = new ArrayCollection();
        $this->sent = new ArrayCollection();
        $this->received = new ArrayCollection();
        $this->checking = 1;
        $this->gradesTeacher = new ArrayCollection();
        $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): void
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }
    }


    public function removeNotification(Notification $notification): void
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }
    }


    public function getLastName(): ?string
    {
        return $this->lastname;
    }

    public function getFullName(): ?string
    {
        return $this->getLastName() . ' ' . $this->getFirstname();
    }

    public function setFullName(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }



    public function setLastName(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRole(): ?string
    {
        $us = $this->getRoles();
        $acces =  implode(",", $us);
        $admin = "Administrateur";
        $professeur = "Professeur";
        $eleve = "Eleve";

        switch ($acces) {
            case 'ROLE_ADMIN,ROLE_USER':
                return $admin;
                break;
            case 'ROLE_TEACHER,ROLE_USER':
                return $professeur;
                break;
            case 'ROLE_USER':
                return $eleve;
                break;
                return  "";
        }
    }

    /**
     * @return Collection|Grade[]
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): self
    {
        if (!$this->grades->contains($grade)) {
            $this->grades[] = $grade;
            $grade->setUser($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): self
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getUser() === $this) {
                $grade->setUser(null);
            }
        }

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

    public function getChecking(): ?bool
    {
        return $this->checking;
    }

    public function setChecking(?bool $checking): self
    {
        $this->checking = $checking;

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
            $calendar->setTeacher($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): self
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getTeacher() === $this) {
                $calendar->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Message $sent): self
    {
        if (!$this->sent->contains($sent)) {
            $this->sent[] = $sent;
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Message $sent): self
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getReceived(): Collection
    {
        return $this->received;
    }

    public function addReceived(Message $received): self
    {
        if (!$this->received->contains($received)) {
            $this->received[] = $received;
            $received->setRecipient($this);
        }

        return $this;
    }

    public function removeReceived(Message $received): self
    {
        if ($this->received->removeElement($received)) {
            // set the owning side to null (unless already changed)
            if ($received->getRecipient() === $this) {
                $received->setRecipient(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getSexe(): ?bool
    {
        return $this->sexe;
    }

    public function setSexe(bool $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function __toString()
    {
        return $this->getFullname();
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGradesTeacher(): Collection
    {
        return $this->gradesTeacher;
    }

    public function addGradesTeacher(Grade $gradesTeacher): self
    {
        if (!$this->gradesTeacher->contains($gradesTeacher)) {
            $this->gradesTeacher[] = $gradesTeacher;
            $gradesTeacher->setTeacher($this);
        }

        return $this;
    }

    public function removeGradesTeacher(Grade $gradesTeacher): self
    {
        if ($this->gradesTeacher->removeElement($gradesTeacher)) {
            // set the owning side to null (unless already changed)
            if ($gradesTeacher->getTeacher() === $this) {
                $gradesTeacher->setTeacher(null);
            }
        }
    public function getIsTeacher(): ?bool
    {
        return $this->isTeacher;
    }

    public function setIsTeacher(?bool $isTeacher): self
    {
        $this->isTeacher = $isTeacher;

        return $this;
    }
}