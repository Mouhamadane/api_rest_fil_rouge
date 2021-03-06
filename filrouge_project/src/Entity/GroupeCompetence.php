<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GroupeCompetenceRepository;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GroupeCompetenceRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"groupecompetence:read"}},
 *      denormalizationContext={"groups"={"groupecompetence:write"}},
 *      subresourceOperations={
 *          "get_groupe_competences_competences"={
 *              "method"="GET",
 *              "path"="/admin/grpecompetences/{id}/competences"
 *          },
 *      },
 *      collectionOperations={
 *         "get_grpecompetences"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/grpecompetences"
 *          },
 *          "get_competences"={
 *              "security"="(is_granted('ROLE_ADMIN'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET",
 *              "path"="/admin/grpecompetences/competences",
 *              "normailzation_context"={"groups"={"groupecompetence:read", "competences:read"}}
 *          },
 *          "add_grpecompetence"={
 *              "method"="POST",
 *              "path"="/admin/grpecompetences" 
 *          }
 *      },
 *      itemOperations={
 *          "get_grpecompetence"={
 *              "security"="(is_granted('VIEW', object))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", "path"="/admin/grpecompetences/{id}"
 *          },
 *          "update_grpecompetence"={
 *              "method"="PUT",
 *              "path"="/admin/grpecompetences/{id}"
 *          },
 *      }
 * )
 */
class GroupeCompetence
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"groupecompetence:read", "referentiel:read:all", "promo:referentiel:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libellé ne doit pas être vide")
     * @Groups({"groupecompetence:read", "groupecompetence:write", "referentiel:read:all", "promo:referentiel:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le descriptif ne doit pas être vide")
     * @Groups({"groupecompetence:read", "groupecompetence:write", "promo:referentiel:read"})
     */
    private $descriptif;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @Groups({"groupecompetence:read"})
     */
    private $admin;

    /**
     * @ORM\ManyToMany(targetEntity=Competence::class, mappedBy="groupeCompetences", cascade={"persist"})
     * @Assert\Valid
     * @Groups({"groupecompetence:read", "groupecompetence:write", "referentiel:read:all", "promo:referentiel:read"})
     * @ApiSubresource
     */
    private $competences;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"groupecompetence:read"})
     */
    private $isDeleted;

    /**
     * @ORM\ManyToMany(targetEntity=Referentiel::class, mappedBy="groupeCompetences")
     */
    private $referentiels;

    public function __construct()
    {
        $this->isDeleted = false;
        $this->competences = new ArrayCollection();
        $this->referentiels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @return Collection|Competence[]
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
            $competence->addGroupeCompetence($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        if ($this->competences->contains($competence)) {
            $this->competences->removeElement($competence);
            $competence->removeGroupeCompetence($this);
        }

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return Collection|Referentiel[]
     */
    public function getReferentiels(): Collection
    {
        return $this->referentiels;
    }

    public function addReferentiel(Referentiel $referentiel): self
    {
        if (!$this->referentiels->contains($referentiel)) {
            $this->referentiels[] = $referentiel;
            $referentiel->addGroupeCompetence($this);
        }

        return $this;
    }

    public function removeReferentiel(Referentiel $referentiel): self
    {
        if ($this->referentiels->contains($referentiel)) {
            $this->referentiels->removeElement($referentiel);
            $referentiel->removeGroupeCompetence($this);
        }

        return $this;
    }
}
