<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupesRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GroupesRepository::class)
 * @ApiResource()
 */
class Groupes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({
     *      "promo:read",
     *      "promo:groupe:principal:read",
     *      "promo:apprenant:read",
     *      "promo_brief:read"
     *    
     *     
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *      "promo:read",
     *      "promo:write",
     *      "promo:groupe:principal:read",
     *      "promo:apprenant:read",
     *      "brief:read",
     *      "briefbrouillons:read",
     *      "briefgroupe:read",
     *      "promo_brief:read"
     * })
     */
    private $nom;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"promo:write","promo:read"})
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"briefgroupe:read","promo:write","promo:read", "promo:groupe:principal:read"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Promos::class, inversedBy="groupes")
     * @Groups ({"promo:read"})
     */
    private $promos;

    /**
     * @ORM\ManyToMany(targetEntity=Formateur::class, inversedBy="groupes")
     */
    private $formateur;

    /**
     * @ORM\ManyToMany(targetEntity=Apprenant::class, inversedBy="groupes")
     * @Groups({"promo:read","promo:write","briefgroupe:read","promo_brief:read", "promo_brief:read","promo:groupe:principal:read","briefbrouillons:read" ,"promo:apprenant:read"})
     * @ApiSubresource
     */
    private $apprenant;

    /**
     * @ORM\Column(type="date")
     * @Groups({"promo:write"})
     */
    private $dateCreation;

    /**
     * @ORM\ManyToMany(targetEntity=Brief::class, mappedBy="groupes")
     */
    private $briefs;

    public function __construct()
    {
        $this->formateur = new ArrayCollection();
        $this->apprenant = new ArrayCollection();
        $this->briefs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    /**
     * @return Collection|Formateur[]
     */
    public function getFormateur(): Collection
    {
        return $this->formateur;
    }

    public function addFormateur(Formateur $formateur): self
    {
        if (!$this->formateur->contains($formateur)) {
            $this->formateur[] = $formateur;
        }

        return $this;
    }

    public function removeFormateur(Formateur $formateur): self
    {
        if ($this->formateur->contains($formateur)) {
            $this->formateur->removeElement($formateur);
        }

        return $this;
    }

    /**
     * @return Collection|Apprenant[]
     */
    public function getApprenant(): Collection
    {
        return $this->apprenant;
    }

    public function addApprenant(Apprenant $apprenant): self
    {
        if (!$this->apprenant->contains($apprenant)) {
            $this->apprenant[] = $apprenant;
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenant->contains($apprenant)) {
            $this->apprenant->removeElement($apprenant);
        }

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection|Brief[]
     */
    public function getBriefs(): Collection
    {
        return $this->briefs;
    }

    public function addBrief(Brief $brief): self
    {
        if (!$this->briefs->contains($brief)) {
            $this->briefs[] = $brief;
            $brief->addGroupe($this);
        }

        return $this;
    }

    public function removeBrief(Brief $brief): self
    {
        if ($this->briefs->contains($brief)) {
            $this->briefs->removeElement($brief);
            $brief->removeGroupe($this);
        }

        return $this;
    }
}
