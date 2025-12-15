<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?int $qteStock = null; // Quantité en stock

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: LigneApprovisionnement::class)]
    private Collection $lignesApprovisionnement;

    public function __construct()
    {
        $this->lignesApprovisionnement = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getQteStock(): ?int
    {
        return $this->qteStock;
    }

    public function setQteStock(int $qteStock): static
    {
        $this->qteStock = $qteStock;
        return $this;
    }

    /**
     * @return Collection<int, LigneApprovisionnement>
     */
    public function getLignesApprovisionnement(): Collection
    {
        return $this->lignesApprovisionnement;
    }

    public function addLignesApprovisionnement(LigneApprovisionnement $lignesApprovisionnement): static
    {
        if (!$this->lignesApprovisionnement->contains($lignesApprovisionnement)) {
            $this->lignesApprovisionnement->add($lignesApprovisionnement);
            $lignesApprovisionnement->setArticle($this);
        }

        return $this;
    }

    public function removeLignesApprovisionnement(LigneApprovisionnement $lignesApprovisionnement): static
    {
        if ($this->lignesApprovisionnement->removeElement($lignesApprovisionnement)) {
            // set the owning side to null (unless already changed)
            if ($lignesApprovisionnement->getArticle() === $this) {
                $lignesApprovisionnement->setArticle(null);
            }
        }
        return $this;
    }
    
    // Ajout pour l'affichage simple
    public function __toString(): string
    {
        return $this->nom ?? '';
    }
}