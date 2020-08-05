<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupeTagRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GroupeTagRepository::class)
 * @ApiResource(
 *      attributes={
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Vous n'avez pas accès aux tags"
 *      },
 *      normalizationContext={"groups"={"grptag:read"}},
 *      denormalizationContext={"groups"={"grptag:write"}},
 *      collectionOperations={
 *          "get_grptags"={
 *              "method"="GET",
 *              "path"="admin/grptags"
 *          },
 *          "add_grptag"={
 *              "method"="POST",
 *              "path"="admin/grptags"
 *          }
 *      },
 *      itemOperations={
 *          "get_grptag"={
 *              "method"="GET",
 *              "path"="admin/grptags/{id}"
 *          },
 *          "update_grptag"={
 *              "method"="PUT",
 *              "path"="admin/grptags/{id}"
 *          }
 *      }
 * )
 */
class GroupeTag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"grptag:read"})
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tag:read", "grptag:read", "grptag:write"})
     */
    private $libelle;
    
    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="groupeTags", cascade={"persist"})
     * @Groups({"grptag:read", "grptag:write"})
     */
    private $tags;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted = false;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
            $tag->addGroupeTag($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removeGroupeTag($this);
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
}
