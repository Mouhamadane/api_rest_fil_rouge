<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NiveauRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NiveauRepository::class)
 *  @ApiResource( 
 * )
 */
class Niveau
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"competence:read","briefgroupe:read","niveau:read","referentiel:read:all","briefbrouillons:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libelle ne doit pas être vide")
     * @Groups({"competence:read","briefgroupe:read","brief:read","referentiel:read:all","briefbrouillons:read"})
     */
    private $libelle;

    /*
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Critère d'évaluation ne doit pas être vide")
     * @Groups({"competence:read", "competence:write", "referentiel:read:all","briefbrouillons:read"})
     */
    private $critereEvaluation;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Groupe d'action ne doit pas être vide")
     * @Groups({"competence:read", "competence:write", "referentiel:read:all"})
     */
    private $groupeAction;

    /**
     * @ORM\ManyToOne(targetEntity=Competence::class, inversedBy="niveaux", cascade={"persist"})
     *  @Groups({"brief:read","briefgroupe:read","briefbrouillons:read","competence:read","livrable:briefs:read"})
     */
    private $competence;

    /**
     * @ORM\ManyToOne(targetEntity=Brief::class, inversedBy="niveaux")
     * @Groups({"livrable:briefs:read"})
     */
    private $brief;

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

    public function getCritereEvaluation(): ?string
    {
        return $this->critereEvaluation;
    }

    public function setCritereEvaluation(string $critereEvaluation): self
    {
        $this->critereEvaluation = $critereEvaluation;

        return $this;
    }

    public function getGroupeAction(): ?string
    {
        return $this->groupeAction;
    }

    public function setGroupeAction(string $groupeAction): self
    {
        $this->groupeAction = $groupeAction;

        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): self
    {
        $this->competence = $competence;

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
}
