<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *      normalizationContext={"groups"={"profil:read"}},
 *      attributes={
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"= "Vous n'avez pas acces à cette ressource"
 *      },
 *      collectionOperations={
 *          "get_profils"={
 *              "method"="GET",
 *              "path"="admin/profils"
 *          },
 *          "post_profils"={
 *              "method"="POST",
 *              "path"="admin/profils"
 *          }    
 *      },
 *      itemOperations={
 *          "get_profil"={
 *              "method"="GET",
 *              "path"="admin/profils/{id}",
 *              "requirements"={"id"="\d+"}
 *          },
 *          "update_profil"={
 *              "method"="PUT",
 *              "path"="admin/profils/{id}",
 *              "requirements"={"id"="\d+"}
 *          },
 *          "get_profil_users"={
 *              "method"="GET",
 *              "path"="admin/profils/{id}/users",
 *              "normalization_context"={"groups"={"profil:read","profil:read:all"}}
 *          },
 *          "delete_profil"={
 *              "method"="DELETE",
 *              "path"="admin/profils/{id}",
 *              "requirements"={"id"="\d+"}
 *          }
 *      }
 * 
 * )
 * @UniqueEntity(
 *      fields={"libelle"},
 *      message="Ce libellé existe déjà"
 * )
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 */
class Profil
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("profil:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil:read","user:read:all"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profil")
     * @Groups("profil:read:all")
     * 
     */
    private $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setProfil($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getProfil() === $this) {
                $user->setProfil(null);
            }
        }

        return $this;
    }
}
