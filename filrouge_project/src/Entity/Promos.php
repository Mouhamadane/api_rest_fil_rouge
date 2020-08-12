<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\PromosRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PromosRepository::class)
 * @ApiResource( 
 * denormalizationContext={"groups"={"promos:write"}},
 * collectionOperations={
 *         "get_Promos"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos"
 *          },
 *          "get_Promos_Principale"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/principale"
 *          },
 *          "get_Promos_apprenant"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/apprenants/attente"
 *          },
 *           "add_Promos"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="POST", 
 *              "path"="/admin/promos/"
 *          },
 * 
 * },
 * itemOperations={
 *          "get_Promo"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}"
 *          },
 *          "get_Promo_Principale"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/principale"
 *          },
 *           "get_Promo_referentiel"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/referentiels"
 *          },
 *          "get_Promo_Apprenant"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/apprenants/attente"
 *          },
 *          "get_Promo_Groupe"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/groupes/{ida}/apprenants"
 *          },
 *          "get_Promo_Formateur"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/formateurs"
 *          },
 *          "put_Promo"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}"
 *          },
 *          "put_Promo_Apprenant"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}/apprenants"
 *          },
 *          "put_Promo_Formateur"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}/formateurs"
 *          },
 *           "put_Promo_Groupe"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}/groupes/{idb}"
 *          },
 *   }
 * )
 */

class Promos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:write"})
     */
    private $langue;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:write"})
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Groups({"promo:write"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:write"})
     */
    private $lieu;
    
    /**
     * @ORM\Column(type="date")
     * @Groups({"promo:write"})
     */
    private $dateProvisoire;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"promo:write"})
     */
    private $dateFin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:write"})
     */
    private $fabrique;

    /**
     * @ORM\OneToMany(targetEntity=Groupes::class, mappedBy="promos",cascade={"persist"})
     * @Groups({"promo:write"})
     */
    private $groupes;

    /**
     * @ORM\ManyToOne(targetEntity=Referentiel::class, inversedBy="promos", cascade={"persist"})
     * @Groups({"promo:write"})
     * 
     */
    private $referentiel;

    /**
     * @ORM\ManyToMany(targetEntity=Formateur::class, inversedBy="promos")
     * @Groups({"promo:write"})
     */
    private $formateur;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->formateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }


    public function getDateProvisoire(): ?\DateTimeInterface
    {
        return $this->dateProvisoire;
    }

    public function setDateProvisoire(\DateTimeInterface $dateProvisoire): self
    {
        $this->dateProvisoire = $dateProvisoire;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getFabrique(): ?string
    {
        return $this->fabrique;
    }

    public function setFabrique(string $fabrique): self
    {
        $this->fabrique = $fabrique;

        return $this;
    }

    /**
     * @return Collection|Groupes[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupes $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->setPromos($this);
        }

        return $this;
    }

    public function removeGroupe(Groupes $groupe): self
    {
        if ($this->groupes->contains($groupe)) {
            $this->groupes->removeElement($groupe);
            // set the owning side to null (unless already changed)
            if ($groupe->getPromos() === $this) {
                $groupe->setPromos(null);
            }
        }

        return $this;
    }

    public function getReferentiel(): ?Referentiel
    {
        return $this->referentiel;
    }

    public function setReferentiel(?Referentiel $referentiel): self
    {
        $this->referentiel = $referentiel;

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }
}
