<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LivrablesAttendusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource( 
 * )
 * @ORM\Entity(repositoryClass=LivrablesAttendusRepository::class)
 */
class LivrablesAttendus
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
     * @ORM\OneToMany(targetEntity=BriefLA::class, mappedBy="livrableAttendu")
     */
    private $briefLAs;

    public function __construct()
    {
        $this->briefs = new ArrayCollection();
        $this->livrables = new ArrayCollection();
        $this->briefLAs = new ArrayCollection();
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
            $briefLA->setLivrableAttendu($this);
        }

        return $this;
    }

    public function removeBriefLA(BriefLA $briefLA): self
    {
        if ($this->briefLAs->contains($briefLA)) {
            $this->briefLAs->removeElement($briefLA);
            // set the owning side to null (unless already changed)
            if ($briefLA->getLivrableAttendu() === $this) {
                $briefLA->setLivrableAttendu(null);
            }
        }

        return $this;
    }
}
