<?php

namespace App\Entity;

use App\Repository\LivrablePartielsRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LivrablePartielsRepository::class)
 * @ApiResource (
 *     normalizationContext={"goups"={"livrablePartiel:read"}},
 *     denormalizationContext={"groups"={"livrablePartiel:write","commentaire:write"}},
 *     collectionOperations={
 *          "get_competences_promo"={
*               "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET",
 *              "path"="/formateurs/promo/{idp}/referentiels/{idr}/competences"
 *          },
 *          "get_apprenant_self_brief"={
 *              "security"="(is_granted('ROLE_APPRENANT'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET",
 *              "path"="apprenants/{id}/promo/{idp}/referentiel/{idr}/statistiques/briefs"
 *          },
 *          "get_referentiel_competences"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET",
 *              "path"="/formateurs/promo/{idp}/referentiel/{idr}/statistiques/competences"
 *          },
 *          "add_formateur_commentaires"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="POST",
 *              "path"="/formateurs/livrablepartiels/{id}/commentaires"
 *          },
 *          "add_apprenant_commentaires"={
 *              "security"="(is_granted('ROLE_APPRENANT'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="POST",
 *              "denormalization_context"={"groups"={"commentaire:write"}},
 *              "path"="/apprenants/livrablepartiels/{id}/commentaires"
 *          },
 *          "get_apprenant_self_competences"={
 *              "security"="(is_granted('ROLE_APPRENANT'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET",
 *              "path"="apprenant/{id}/promo/{idp}/referentiel/{idr}/competences"
 *          }
 *     },
 *     itemOperations={
 *          "add_livrablePartiel"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET",
 *              "path"="/formateurs/promo/{idp}/brief/{idb}/livrablepartiels"
 *          },
 *          "delete_livrablePartiel"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="DELETE",
 *              "path"="/formateurs/promo/{idp}/brief/{idb}/livrablepartiels"
 *          },
 *          "update_livrablePartiel_Apprenant"={
 *              "security"="(is_granted('ROLE_FORMATEUR'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT",
 *              "path"="/apprenants/{id}/livrablepartiels/{idl}"
 *          },
 *          "update_statut_livrable"={
 *              "security"="(is_granted('ROLE_APPRENANT'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="PUT",
 *              "path"="/apprenants/{id}/livrablepartiels/{idl}"
 *          }
 *
 *     }
 * )
 */
class LivrablePartiels
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"commentaire:write","livrablePartiel:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"livrablePartiel:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="text")
     * @Groups({"livrablePartiel:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"livrablePartiel:read"})
     */
    private $delai;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"livrablePartiel:read"})
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Niveau::class)
     */
    private $niveaux;

    /**
     * @ORM\ManyToOne(targetEntity=PromoBrief::class, inversedBy="livrablePartiels")
     * @Groups ({"livrablePartiel:read","commentaire:write"})
     */
    private $promoBrief;

    /**
     * @ORM\OneToMany(targetEntity=LivrableRendu::class, mappedBy="livrablePartiel")
     * @Groups({"commentaire:write"})
     */
    private $livrableRendus;

    public function __construct()
    {
        $this->niveaux = new ArrayCollection();
        $this->livrableRendus = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDelai(): ?\DateTimeInterface
    {
        return $this->delai;
    }

    public function setDelai(\DateTimeInterface $delai): self
    {
        $this->delai = $delai;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
        }

        return $this;
    }

    public function removeNiveau(Niveau $niveau): self
    {
        if ($this->niveaux->contains($niveau)) {
            $this->niveaux->removeElement($niveau);
        }

        return $this;
    }

    public function getPromoBrief(): ?PromoBrief
    {
        return $this->promoBrief;
    }

    public function setPromoBrief(?PromoBrief $promoBrief): self
    {
        $this->promoBrief = $promoBrief;

        return $this;
    }

    /**
     * @return Collection|LivrableRendu[]
     */
    public function getLivrableRendus(): Collection
    {
        return $this->livrableRendus;
    }

    public function addLivrableRendu(LivrableRendu $livrableRendu): self
    {
        if (!$this->livrableRendus->contains($livrableRendu)) {
            $this->livrableRendus[] = $livrableRendu;
            $livrableRendu->setLivrablePartiel($this);
        }

        return $this;
    }

    public function removeLivrableRendu(LivrableRendu $livrableRendu): self
    {
        if ($this->livrableRendus->contains($livrableRendu)) {
            $this->livrableRendus->removeElement($livrableRendu);
            // set the owning side to null (unless already changed)
            if ($livrableRendu->getLivrablePartiel() === $this) {
                $livrableRendu->setLivrablePartiel(null);
            }
        }

        return $this;
    }
}
