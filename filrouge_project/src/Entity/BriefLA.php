<?php

namespace App\Entity;

use App\Repository\BriefLARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BriefLARepository::class)
 */
class BriefLA
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Brief::class, inversedBy="briefLAs")
     */
    private $brief;

    /**
     * @ORM\ManyToOne(targetEntity=LivrablesAttendus::class, inversedBy="briefLAs")
     */
    private $livrableAttendu;

    /**
     * @ORM\OneToMany(targetEntity=LivrablePartiels::class, mappedBy="briefLA")
     */
    private $livrables;

    public function __construct()
    {
        $this->livrables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLivrableAttendu(): ?LivrablesAttendus
    {
        return $this->livrableAttendu;
    }

    public function setLivrableAttendu(?LivrablesAttendus $livrableAttendu): self
    {
        $this->livrableAttendu = $livrableAttendu;

        return $this;
    }

    /**
     * @return Collection|LivrablePartiels[]
     */
    public function getLivrables(): Collection
    {
        return $this->livrables;
    }

    public function addLivrable(LivrablePartiels $livrable): self
    {
        if (!$this->livrables->contains($livrable)) {
            $this->livrables[] = $livrable;
            $livrable->setBriefLA($this);
        }

        return $this;
    }

    public function removeLivrable(LivrablePartiels $livrable): self
    {
        if ($this->livrables->contains($livrable)) {
            $this->livrables->removeElement($livrable);
            // set the owning side to null (unless already changed)
            if ($livrable->getBriefLA() === $this) {
                $livrable->setBriefLA(null);
            }
        }

        return $this;
    }
}
