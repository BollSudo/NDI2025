<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CourseRepository;
use App\State\CourseProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(validationContext: ["groups" => ["Default", "validation:course:create"]], processor: CourseProcessor::class),
        new Patch(validationContext: ["groups" => ["Default", "validation:course:update"]])
    ],
)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ["validation:course:create"])]
    #[Assert\NotNull(groups: ["validation:course:create"])]
    private ?string $university_name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(groups: ["validation:course:create"])]
    #[Assert\NotNull(groups: ["validation:course:create"])]
    private ?\DateTime $starting_year = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(groups: ["validation:course:create"])]
    #[Assert\NotNull(groups: ["validation:course:create"])]
    private ?\DateTime $ending_date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ["validation:course:create"])]
    #[Assert\NotNull(groups: ["validation:course:create"])]
    private ?string $course_name = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $responsibilities = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[Assert\NotBlank(groups: ["validation:course:update"])]
    #[Assert\NotNull(groups: ["validation:course:update"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Assert\NotBlank(groups: ["validation:course:create"])]
    #[Assert\NotNull(groups: ["validation:course:create"])]
    public ?string $userCredential = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniversityName(): ?string
    {
        return $this->university_name;
    }

    public function setUniversityName(string $university_name): static
    {
        $this->university_name = $university_name;

        return $this;
    }

    public function getStartingYear(): ?\DateTime
    {
        return $this->starting_year;
    }

    public function setStartingYear(\DateTime $starting_year): static
    {
        $this->starting_year = $starting_year;

        return $this;
    }

    public function getEndingDate(): ?\DateTime
    {
        return $this->ending_date;
    }

    public function setEndingDate(\DateTime $ending_date): static
    {
        $this->ending_date = $ending_date;

        return $this;
    }

    public function getCourseName(): ?string
    {
        return $this->course_name;
    }

    public function setCourseName(string $course_name): static
    {
        $this->course_name = $course_name;

        return $this;
    }

    public function getResponsibilities(): ?array
    {
        return $this->responsibilities;
    }

    public function setResponsibilities(?array $responsibilities): static
    {
        $this->responsibilities = $responsibilities;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUserCredential(): ?string
    {
        return $this->userCredential;
    }

    public function eraseCredentials() {
        $this->userCredential = null;
    }
}
