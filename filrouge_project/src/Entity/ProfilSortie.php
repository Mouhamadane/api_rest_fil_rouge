<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilSortieRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProfilSortieRepository::class)
 * @ApiResource(
 *      collectionOperations={
 * 
 *         "get_profilsorties"={
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/profilsorties"
 *          },
 *         "add_profilsortie"={
 *              "method"="POST",
 *              "path"="admin/profilsorties",
 *              "security"="is_granted('ROLE_FORMATEUR','ROLE_CM')",
 *              "security_message"="Vous n'avez pas accès à cette ressource"
 *          },
 *          "ShowpromoProfilsortie"={
 *              "normalization_context" ={"groups" ={"profilsortieEtudiant:read"}},
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/profilsorties",
 *              "defaults"={"id"=null}
 *          }
 *       },
 *      itemOperations={
 *          "get_profilsorties_Apprenant"={
 *              "normalization_context" ={"groups" ={"profilsortie:read"}},     
 *              "security"="(is_granted('ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/profilsorties/{id}",
 *              "defaults"={"id"=null}
 * 
 * 
 *          },
 *          "showpromoid"={
 *              "normalization_context" ={"groups" ={"profilsortie:read"} },
 *              "security"="(is_granted('ROLE_FORMATEUR','ROLE_CM'))",
 *              "security_message"="Vous n'avez pas access à cette Ressource",
 *              "method"="GET", 
 *              "path"="/admin/promos/{id}/profilsorties/{ida}",
 *          },
 *          "update_Profilsortie"={
 *              "method"="PUT",
 *              "path"="admin/profilsortie/{id}",
 *              "security"="is_granted('ROLE_FORMATEUR')",
 *              "security_message"="Vous n'avez pas accès à cette ressource"
 *          },
 *          
 *    }
 * )
 */
class ProfilSortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"profilsortie:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profilsortie:read"})
     * 
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Apprenant::class, mappedBy="profilSortie")
     * @ApiSubresource()
     * @Groups({"profilsortie:read"})
     */
    private $apprenants;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
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

    /**
     * @return Collection|Apprenant[]
     */
    public function getApprenants(): Collection
    {
        return $this->apprenants;
    }

    public function addApprenant(Apprenant $apprenant): self
    {
        if (!$this->apprenants->contains($apprenant)) {
            $this->apprenants[] = $apprenant;
            $apprenant->setProfilSortie($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenants->contains($apprenant)) {
            $this->apprenants->removeElement($apprenant);
            // set the owning side to null (unless already changed)
            if ($apprenant->getProfilSortie() === $this) {
                $apprenant->setProfilSortie(null);
            }
        }

        return $this;
    }
}
