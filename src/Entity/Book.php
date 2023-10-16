<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $ref;

    /**
     * @ORM\Column(type="string")*/
    private $title;

    /**
     * @ORM\Column(type="string")*/
    private $category;

    /**
     * @ORM\Column(type="date")*/
    private $publicationDate;

    /**
     * @ORM\Column(type="boolean")*/
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    public function getRef(): ?int
    {
        return $this->ref;
    }
    public function setRef(int $ref): self
    {
        $this->ref = $ref;

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }
    // méthode pour formater la date de publication dans votre classe Book  pour pouvoir utiliser le type date
    public function getFormattedPublicationDate(): string
    {
        if ($this->publicationDate instanceof DateTime) {
            return $this->publicationDate->format('Y-m-d');
        }

        return '';
    }

	
}