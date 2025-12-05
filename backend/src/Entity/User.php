<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\UserRepository;
use App\State\UserProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', 'phoneNumber')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(validationContext: ["groups" => ["Default", "validation:user:create"]],
            processor: UserProcessor::class),
        new Patch(validationContext: ["groups" => ["Default", "validation:user:update"]],
            processor: UserProcessor::class)
    ],
    normalizationContext: ["groups" => ["serialization:user:read", "serialization:user:create", "serialization:user:update"]],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['serialization:user:read', 'serialization:user:create'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['serialization:user:read'])]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Groups(['serialization:user:create', 'serialization:user:update'])]
    #[Assert\Length(
        min: 12,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins 12 caractères",
        maxMessage: "Le mot de passe ne peut pas être plus grand que 255 caractères"
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{12,255}$/',
        message: 'Le mot de passe doit contenir : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial (12-255 caractères)'
    )]
    #[Assert\NotBlank(groups: ["validation:user:create"])]
    #[Assert\NotNull(groups: ["validation:user:create"])]
    private ?string $plainPassword = null;

    #[Groups(['serialization:user:read', 'serialization:user:create'])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ["validation:user:create"])]
    #[Assert\NotNull(groups: ["validation:user:create"])]
    private ?string $name = null;

    #[Groups(['serialization:user:read', 'serialization:user:create'])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ["validation:user:create"])]
    #[Assert\NotNull(groups: ["validation:user:create"])]
    private ?string $firstName = null;

    #[Groups(['serialization:user:read', 'serialization:user:create'])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $birthdate = null;

    #[Groups(['serialization:user:read', 'serialization:user:create'])]
    #[ORM\Column(name: 'email', type: 'string', length: 255, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank(groups: ["validation:user:create"])]
    #[Assert\NotNull(groups: ["validation:user:create"])]
    private ?string $email = null;

    #[Groups(['serialization:user:read', 'serialization:user:create'])]
    #[ORM\Column(name: 'phoneNumber', type: 'string', length: 25, unique: true, nullable: true)]
    private ?string $phoneNumber = null;

    #[Groups(['serialization:user:read', 'serialization:user:create'])]
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBirthdate(): ?\DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTime $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setUser($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getUser() === $this) {
                $course->setUser(null);
            }
        }

        return $this;
    }


}
