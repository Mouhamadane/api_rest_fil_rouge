<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ApprenantRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ApprenantRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"user:read"}},
 *      collectionOperations={
 *          "get_apprenants"={
 *              "method"="GET",
 *              "path"="api/apprenants",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"= "Vous n'avez pas acces à cette ressource",
 *          },
 *      },
 *      itemOperations={
 *          "get_apprenant"={
 *              "normalization_context"={"groups"={"user:read","user:read:all"}},
 *              "method"="GET",
 *              "path"="/apprenants/{id}"
 *          },
 *      }
 * )
 */
class Apprenant extends User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity=Groupes::class, mappedBy="apprenant")
     * @Groups({"promo:write"})
     */
    protected $groupes;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $groupe->addApprenant($this);
        }

        return $this;
    }

    public function removeGroupe(Groupes $groupe): self
    {
        if ($this->groupes->contains($groupe)) {
            $this->groupes->removeElement($groupe);
            $groupe->removeApprenant($this);
        }

        return $this;
    }
}
