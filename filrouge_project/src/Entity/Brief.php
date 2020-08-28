<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BriefRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BriefRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"brief:read"}},
 *      collectionOperations={
 *         "GET",
 *          "get_brief"={
 *              "method"="GET",
 *              "path"="formateurs/briefs",
 *              "security"="is_granted('ROLE_CM') or is_granted('ROLE_FORMATEUR')",
 *              "security_message"= "Vous n'avez pas acces à cette ressource",
 *              
 *          },
 *
 *          " promoBriefs"={
 *              "method"="GET",
 *              "path"="formateurs/promos/{id}/briefs",
 *              "security"="is_granted('ROLE_CM') or is_granted('ROLE_FORMATEUR')",
 *              "security_message"= "Vous n'avez pas acces à cette ressource"
 *          },
 *          "briefapprenant"={
 *              "method"="GET",
 *              "path"="apprenants/promos/{id}/briefs",
 *              "security"="is_granted('ROLE_CM') or is_granted('ROLE_FORMATEUR')or is_granted('ROLE_APPRENANT')",
 *              "security_message"= "Vous n'avez pas acces à cette ressource"
 *          },
 *          "briefbrouillons"={
 *              "method"="GET",
 *              "path"="formateurs/{id}/briefs/brouillons",
 *              "security"="is_granted('ROLE_FORMATEUR') ",
 *              "security_message"= "Vous n'avez pas acces à cette ressource"
 *          },
 *          "briefvalide"={
 *              "method"="GET",
 *              "path"="formateurs/{id}/briefs/valide",
 *              "security"="is_granted('ROLE_FORMATEUR') ",
 *              "security_message"= "Vous n'avez pas acces à cette ressource"
 *          },
 * },
 *    itemOperations={
 *          "briefpromo"={
 *              "method"="GET",
 *              "path"="formateurs/promos/{idp}/briefs/{idb}",
 *              "security"="is_granted('ROLE_FORMATEUR') ",
 *              "security_message"= "Vous n'avez pas acces à cette ressource"
 *          },
 * }
 * )  
 */
class Brief
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"brief:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brief:read"})
     */
    private $langue;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brief:read"})
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Groups({"brief:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brief:read"})
     */
    private $contexte;

    /**
     * @ORM\Column(type="text")
     * @Groups({"brief:read"})
     */
    private $livrablesAttendus;

    /**
     * @ORM\Column(type="text")
     * @Groups({"brief:read"})
     */
    private $modalitePedagogique;

    /**
     * @ORM\Column(type="text")
     * @Groups({"brief:read"})
     */
    private $criterePerformance;

    /**
     * @ORM\Column(type="text")
     * @Groups({"brief:read"})
     */
    private $modaliteEvaluation;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * 
     */
    private $avatar;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"brief:read"})
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brief:read"})
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=Ressource::class, mappedBy="brief", cascade={"persist"})
     */
    private $ressources;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="briefs")
     * @Groups({"brief:read"})
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity=Niveau::class, mappedBy="brief", cascade={"persist"})
     * @Groups({"brief:read"})
     */
    private $niveaux;

    /**
     * @ORM\ManyToOne(targetEntity=Referentiel::class)
     * @Groups({"brief:read"})
     */
    private $referentiel;

    /**
     * @ORM\ManyToMany(targetEntity=Groupes::class, inversedBy="briefs")
     */
    private $groupes;

  

    /**
     * @ORM\OneToMany(targetEntity=BriefLA::class, mappedBy="brief", cascade={"persist"})
     * @Groups({"brief:read"})
     */
    private $briefLAs;

    /**
     * @ORM\ManyToOne(targetEntity=Formateur::class, inversedBy="briefs")
     */
    private $formateur;

    public function __construct()
    {
        $this->livrablesAttenduses = new ArrayCollection();
        $this->ressources = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->niveaux = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->briefLAs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId()
    {
        return $this->id = null;
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

    public function getContexte(): ?string
    {
        return $this->contexte;
    }

    public function setContexte(string $contexte): self
    {
        $this->contexte = $contexte;

        return $this;
    }

    public function getLivrablesAttendus(): ?string
    {
        return $this->livrablesAttendus;
    }

    public function setLivrablesAttendus(string $livrablesAttendus): self
    {
        $this->livrablesAttendus = $livrablesAttendus;

        return $this;
    }

    public function getModalitePedagogique(): ?string
    {
        return $this->modalitePedagogique;
    }

    public function setModalitePedagogique(string $modalitePedagogique): self
    {
        $this->modalitePedagogique = $modalitePedagogique;

        return $this;
    }

    public function getCriterePerformance(): ?string
    {
        return $this->criterePerformance;
    }

    public function setCriterePerformance(string $criterePerformance): self
    {
        $this->criterePerformance = $criterePerformance;

        return $this;
    }

    public function getModaliteEvaluation(): ?string
    {
        return $this->modaliteEvaluation;
    }

    public function setModaliteEvaluation(string $modaliteEvaluation): self
    {
        $this->modaliteEvaluation = $modaliteEvaluation;

        return $this;
    }

    public function getAvatar()
    {
        return base64_encode(stream_get_contents($this->avatar)) ;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): self
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources[] = $ressource;
            $ressource->setBrief($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->contains($ressource)) {
            $this->ressources->removeElement($ressource);
            // set the owning side to null (unless already changed)
            if ($ressource->getBrief() === $this) {
                $ressource->setBrief(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|Niveau[]
     */
    public function getNiveaux(): Collection
    {
        return $this->niveaux;
    }

    public function addNiveau(Niveau $niveau): self
    {
        if (!$this->niveaux->contains($niveau)) {
            $this->niveaux[] = $niveau;
            $niveau->setBrief($this);
        }

        return $this;
    }

    public function removeNiveau(Niveau $niveau): self
    {
        if ($this->niveaux->contains($niveau)) {
            $this->niveaux->removeElement($niveau);
            // set the owning side to null (unless already changed)
            if ($niveau->getBrief() === $this) {
                $niveau->setBrief(null);
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
        }

        return $this;
    }

    public function clearGroupe()
    {
        return $this->groupes = new ArrayCollection();
    }

    public function removeGroupe(Groupes $groupe): self
    {
        if ($this->groupes->contains($groupe)) {
            $this->groupes->removeElement($groupe);
        }

        return $this;
    }

   
    /**
     * @return Collection|BriefLA[]
     */
    public function getBriefLAs(): Collection
    {
        return $this->briefLAs;
    }

    public function addBriefLA(BriefLA $briefLA): self
    {
        if (!$this->briefLAs->contains($briefLA)) {
            $this->briefLAs[] = $briefLA;
            $briefLA->setBrief($this);
        }

        return $this;
    }

    public function clearBriefLAs(): self
    {
        if (!empty($this->briefLAs)) {
            $this->briefLAs = new ArrayCollection();
        }
        return $this;
    }

    public function removeBriefLA(BriefLA $briefLA): self
    {
        if ($this->briefLAs->contains($briefLA)) {
            $this->briefLAs->removeElement($briefLA);
            // set the owning side to null (unless already changed)
            if ($briefLA->getBrief() === $this) {
                $briefLA->setBrief(null);
            }
        }

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): self
    {
        $this->formateur = $formateur;

        return $this;
    }
}
