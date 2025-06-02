<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation extends Produit
{
    #[ORM\ManyToOne(inversedBy: 'formation_created')]
    private ?Formateur $formateur = null;

    #[ORM\Column]
    private ?bool $published = null;


    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $pdfFilename = null;
    /**
     * @var Collection<int, Formateur>
     */
   /* #[ORM\ManyToMany(targetEntity: Formateur::class, mappedBy: 'formation_created')]
    private Collection $formateurs;*/

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        //$this->formateurs = new ArrayCollection();

        $this->createdAt = new \DateTimeImmutable();
    }

    public function getPdfFilename(): ?string
    {
        return $this->pdfFilename;
    }

    public function setPdfFilename(?string $pdfFilename): self
    {
        $this->pdfFilename = $pdfFilename;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    } 

    public function setFormateur(?Formateur $formateur): static
    {
        $this->formateur = $formateur;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
