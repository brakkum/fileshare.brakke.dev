<?php

namespace App\Entity;

use App\Repository\SharedFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SharedFileRepository::class)
 */
class SharedFile
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
     * @ORM\Column(type="string", length=255)
     */
    private $hash_of_file_contents;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *     min=1
     * )
     */
    private $allowed_downloads = 1;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time_created;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $private_key = "";

    /**
     * @ORM\Column(type="integer")
     */
    private $number_of_downloads = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_been_shared = false;

    public function __construct()
    {
        $this->time_created = new \DateTime();
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

    public function getHashOfFileContents(): ?string
    {
        return $this->hash_of_file_contents;
    }

    public function setHashOfFileContents(string $hash_of_file_contents): self
    {
        $this->hash_of_file_contents = $hash_of_file_contents;

        return $this;
    }

    public function getAllowedDownloads(): ?int
    {
        return $this->allowed_downloads;
    }

    public function setAllowedDownloads(int $allowed_downloads): self
    {
        $this->allowed_downloads = $allowed_downloads;

        return $this;
    }

    public function getTimeCreated(): ?\DateTimeInterface
    {
        return $this->time_created;
    }

    public function setTimeCreated(\DateTimeInterface $time_created): self
    {
        $this->time_created = $time_created;

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->private_key;
    }

    public function setPrivateKey(?string $private_key): self
    {
        $this->private_key = $private_key;

        return $this;
    }

    public function getNumberOfDownloads(): ?int
    {
        return $this->number_of_downloads;
    }

    public function setNumberOfDownloads(int $number_of_downloads): self
    {
        $this->number_of_downloads = $number_of_downloads;

        return $this;
    }

    public function getHasBeenShared(): ?bool
    {
        return $this->has_been_shared;
    }

    public function setHasBeenShared(bool $has_been_shared): self
    {
        $this->has_been_shared = $has_been_shared;

        return $this;
    }
}
