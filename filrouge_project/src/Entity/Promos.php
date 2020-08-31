<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\PromosRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PromosRepository::class)
 * @ApiResource( 
 *      normalizationContext={"groups"={"promo:read"}},
 *      denormalizationContext={"groups"={"promo:write"}},
 *      collectionOperations={
 *         "get_Promos"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos"
 *          },
 *          "get_Promos_Principal"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "normalization_context"={"groups"={"promo:groupe:principal:read"}},
 *              "method"="GET", 
 *              "path"="/admin/promos/principal"
 *          },
 *          "get_Promos_apprenant"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/apprenants/attente"
 *          },
 *           "add_Promos"={
 *              "method"="POST", 
 *              "path"="/admin/promos/"
 *          },
 * 
 *      },
 *      itemOperations={
 *          "get_Promo"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}"
 *          },
 *          "get_Promo_Principale"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "normalization_context"={"groups"={"promo:groupe:principal:read"}},
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/principale"
 *          },
 *           "get_Promo_referentiel"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "normalization_context"={"groups"={"promo:referentiel:read"}},
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
 *              "normalization_context"={"groups"={"promo:apprenant:read"}},
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/groupes/{ida}/apprenants"
 *          },
 *          "get_Promo_Formateur"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "normalization_context"={"groups"={"promo:formateur:read"}},
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/formateurs"
 *          },
 *          "update_promo"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}"
 *          },
 *          "update_promo_apprenant"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}/apprenants"
 *          },
 *          "update_promo_formateur"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}/formateurs"
 *          },
 *           "ajouter_promo_groupe"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}/groupes",
 *          },
 *           "update_promo_groupe"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT", 
 *              "path"="/admin/promos/{id}/groupes/{idgrpe}",
 *          },
 *      }
 * )
 */

class Promos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({
     *      "promo:read",
     *      "promo:groupe:principal:read",
     *      "promo:referentiel:read",
     *      "promo:formateur:read",
     *      "promo:apprenant:read",
     *      "promo_brief:read"
     *      
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:read", "briefgroupe:read","promo:write","promo_brief:read", "promo:groupe:principal:read", "promo:referentiel:read"})
     */
    private $langue;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *      "promo:read",
     *      "promo:write",
     *      "promo:groupe:principal:read",
     *      "promo:referentiel:read",
     *      "promo:formateur:read",
     *      "promo:apprenant:read",
     *      "promo_brief:read"
     *      
     * })
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Groups({"promo:read","promo:write", "promo:groupe:principal:read", "promo:referentiel:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"promo:read", "promo:write"})
     */
    private $lieu;
    
    /**
     * @ORM\Column(type="date")
     * @Groups({"promo:read", "promo:write", "promo:groupe:principal:read"})
     */
    private $dateProvisoire;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"promo:read", "promo:write"})
     */
    private $dateFin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:read", "promo:write"})
     */
    private $fabrique;

    /**
     * @ORM\OneToMany(targetEntity=Groupes::class, mappedBy="promos",cascade={"persist"})
     * @ApiSubresource(maxDepth=3)
     * @Groups({
     *      "promo:read",
     *      "promo:write",
     *      "promo:groupe:principal:read",
     *      "promo:apprenant:read",
     *       "promo:brief:read",
     * 
     *     
     * })
     */
    private $groupes;

    /**
     * @ORM\ManyToOne(targetEntity=Referentiel::class, inversedBy="promos", cascade={"persist"})
     * @Groups({
     *      "promo:read",
     *      "promo:write",
     *      "promo:groupe:principal:read",
     *      "promo:referentiel:read"
     *      
     *      
     * })
     * 
     */
    private $referentiel;

    /**
     * @ORM\ManyToMany(targetEntity=Formateur::class, inversedBy="promos")
     * @Groups({
     *      "promo:read",
     *      "promo:write",
     *      "promo:groupe:principal:read",
     *      "promo:formateur:read"
     *     
     * })
     */
    private $formateur;

    /**
     * @ORM\Column(type="date")
     * @Groups({"promo:read", "promo:groupe:principal:read"})
     */
    private $dateDebut;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @Groups({"promo:read"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=FilDeDiscussion::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({
     *      "promo:read",
     * })
     */
    private $filDeDiscussion;
     /** @ORM\OneToMany(targetEntity=PromoBrief::class, mappedBy="promos", cascade={"persist"})
     *  @Groups({"promo:read"})
     */
    private $promoBrief;
    /**
     * @ORM\OneToMany(targetEntity=StatistiquesCompetences::class, mappedBy="promos")
     */
    private $statistiquesCompetences;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->formateur = new ArrayCollection();
        $this->promoBrief = new ArrayCollection();
        $this->statistiquesCompetences = new ArrayCollection();

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFilDeDiscussion(): ?FilDeDiscussion
    {
        return $this->filDeDiscussion;
    }

    public function setFilDeDiscussion(FilDeDiscussion $filDeDiscussion): self
    {
        $this->filDeDiscussion = $filDeDiscussion;

        return $this;
    }
    
    /**
     * @return Collection|PromoBrief[]
     */
    public function getPromoBrief(): Collection
    {
        return $this->promoBrief;
    }

    public function addPromoBrief(PromoBrief $promoBrief): self
    {
        if (!$this->promoBrief->contains($promoBrief)) {
            $this->promoBrief[] = $promoBrief;
            $promoBrief->setPromos($this);

        }

        return $this;
    }

    public function removePromoBrief(PromoBrief $promoBrief): self
    {
        if ($this->promoBrief->contains($promoBrief)) {
            $this->promoBrief->removeElement($promoBrief);
            // set the owning side to null (unless already changed)

            if ($promoBrief->getPromos() === $this) {
                $promoBrief->setPromos(null); }

        return $this;
    }
}

    /**
     * @return Collection|StatistiquesCompetences[]
     */
    public function getStatistiquesCompetences(): Collection
    {
        return $this->statistiquesCompetences;
    }

    public function addStatistiquesCompetence(StatistiquesCompetences $statistiquesCompetence): self
    {
        if (!$this->statistiquesCompetences->contains($statistiquesCompetence)) {
            $this->statistiquesCompetences[] = $statistiquesCompetence;
            $statistiquesCompetence->setPromos($this);
        }

        return $this;
    }

    public function removeStatistiquesCompetence(StatistiquesCompetences $statistiquesCompetence): self
    {
        if ($this->statistiquesCompetences->contains($statistiquesCompetence)) {
            $this->statistiquesCompetences->removeElement($statistiquesCompetence);
            // set the owning side to null (unless already changed)
            if ($statistiquesCompetence->getPromos() === $this) {
                $statistiquesCompetence->setPromos(null);

            }
        }

        return $this;
    }
}
