<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BriefLARepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BriefLARepository::class)
 * @ApiResource()
 */
class BriefLA
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"brief:read","briefbrouillons:read","promo_brief:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Brief::class, inversedBy="briefLAs", cascade={"persist"})
     */
    private $brief;

    /**
     * @ORM\ManyToOne(targetEntity=LivrablesAttendus::class, inversedBy="briefLAs", cascade={"persist"})
     * @Groups({"brief:read","briefbrouillons:read","promo_brief:read"})
     */
    private $livrableAttendu;

    /**
     * @ORM\OneToMany(targetEntity=Livrables::class, mappedBy="briefLA")
     * @Groups({"briefbrouillons:read","briefbrouilons:read"})
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

    public function setId()
    {
        return $this->id = null;
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
     * @return Collection|Livrables[]
     */
    public function getLivrables(): Collection
    {
        return $this->livrables;
    }

    public function addLivrable(Livrables $livrable): self
    {
        if (!$this->livrables->contains($livrable)) {
            $this->livrables[] = $livrable;
            $livrable->setBriefLA($this);
        }

        return $this;
    }

    public function clearLivrables(): self
    {
        if (!empty($this->livrables)) {
            $this->livrables = new ArrayCollection();
        }

        return $this;
    }

    public function removeLivrable(Livrables $livrable): self
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
