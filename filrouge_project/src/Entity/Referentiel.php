<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ReferentielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReferentielRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"referentiel:read"}},
 *      collectionOperations={
 *          "get_referentiels"={
 *              "method"="GET",
 *              "path"="admin/referentiels",
 *              "security"="is_granted()",
 *              "security"="is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM')",
 *          },
 *          "get_referentiels"={
 *              "method"="GET",
 *              "path"="/admin/referentiels/grpecompetences",
 *              "security"="is_granted()",
 *              "security"="is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM')",
 *          },
 *          "add_referentiels"={
 *              "method"="POST",
 *              "path"="admin/referentiels",
 *              "security"="is_granted()",
 *              "security"="is_granted('ROLE_ADMIN')",
 *          }
 *      },
 *      itemOperations={
 *          "get_referentiel"={
 *              "method"="GET",
 *              "path"="admin/referentiels/{id}"
 *          },
 *          "update_referentiel"={
 *              "method"="PUT",
 *              "path"="admin/referentiels/{id}",
 *              "security"="is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM')",
 *              "security_message"="Vous n'avez pas accès à cette ressource"
 *          }
 *      }
 * )
 */
class Referentiel
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
    private $libelle;

    /**
     * @ORM\Column(type="text")
     */
    private $presentation;

    /**
     * @ORM\Column(type="text")
     */
    private $programme;

    /**
     * @ORM\Column(type="text")
     */
    private $critereAdmission;

    /**
     * @ORM\Column(type="text")
     */
    private $critereEvaluation;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeCompetence::class, inversedBy="referentiels")
     */
    private $groupeCompetences;

    public function __construct()
    {
        $this->groupeCompetences = new ArrayCollection();
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

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getProgramme(): ?string
    {
        return $this->programme;
    }

    public function setProgramme(string $programme): self
    {
        $this->programme = $programme;

        return $this;
    }

    public function getCritereAdmission(): ?string
    {
        return $this->critereAdmission;
    }

    public function setCritereAdmission(string $critereAdmission): self
    {
        $this->critereAdmission = $critereAdmission;

        return $this;
    }

    public function getCritereEvaluation(): ?string
    {
        return $this->critereEvaluation;
    }

    public function setCritereEvaluation(string $critereEvaluation): self
    {
        $this->critereEvaluation = $critereEvaluation;

        return $this;
    }

    /**
     * @return Collection|GroupeCompetence[]
     */
    public function getGroupeCompetences(): Collection
    {
        return $this->groupeCompetences;
    }

    public function addGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if (!$this->groupeCompetences->contains($groupeCompetence)) {
            $this->groupeCompetences[] = $groupeCompetence;
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if ($this->groupeCompetences->contains($groupeCompetence)) {
            $this->groupeCompetences->removeElement($groupeCompetence);
        }

        return $this;
    }
}
