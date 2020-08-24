<?php

namespace App\Entity;

use App\Repository\PromoBriefRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PromoBriefRepository::class)
 */
class PromoBrief
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=Promos::class)
     */
    private $promos;

    /**
     * @ORM\ManyToOne(targetEntity=Brief::class)
     */
    private $brief;

    /**
     * @ORM\OneToMany(targetEntity=LivrablePartiels::class, mappedBy="promoBrief")
     */
    private $livrablePartiels;

    /**
     * @ORM\ManyToOne(targetEntity=Promos::class, inversedBy="promosbrief")
     */
    private $promosbrief;

    public function __construct()
    {
        $this->livrablePartiels = new ArrayCollection();
        $this->promosbrief = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getPromos(): ?Promos
    {
        return $this->promos;
    }

    public function setPromos(?Promos $promos): self
    {
        $this->promos = $promos;

        return $this;
    }

    public function getBrief(): ?Brief
    {
        return $this->brief;
    }

    public function setBrief(?Brief $brief): self
    {
        $this->brief = $brief;

        return $this;
    }

    /**
     * @return Collection|LivrablePartiels[]
     */
    public function getLivrablePartiels(): Collection
    {
        return $this->livrablePartiels;
    }

    public function addLivrablePartiel(LivrablePartiels $livrablePartiel): self
    {
        if (!$this->livrablePartiels->contains($livrablePartiel)) {
            $this->livrablePartiels[] = $livrablePartiel;
            $livrablePartiel->setPromoBrief($this);
        }

        return $this;
    }

    public function removeLivrablePartiel(LivrablePartiels $livrablePartiel): self
    {
        if ($this->livrablePartiels->contains($livrablePartiel)) {
            $this->livrablePartiels->removeElement($livrablePartiel);
            // set the owning side to null (unless already changed)
            if ($livrablePartiel->getPromoBrief() === $this) {
                $livrablePartiel->setPromoBrief(null);
            }
        }

        return $this;
    }

    public function removePromosbrief(Promos $promosbrief): self
    {
        if ($this->promosbrief->contains($promosbrief)) {
            $this->promosbrief->removeElement($promosbrief);
            // set the owning side to null (unless already changed)
            if ($promosbrief->getPromosbrief() === $this) {
                $promosbrief->setPromosbrief(null);
            }
        }

        return $this;
    }

    public function getPromosbrief(): ?Promos
    {
        return $this->promosbrief;
    }

    public function setPromosbrief(?Promos $promosbrief): self
    {
        $this->promosbrief = $promosbrief;

        return $this;
    }
}
