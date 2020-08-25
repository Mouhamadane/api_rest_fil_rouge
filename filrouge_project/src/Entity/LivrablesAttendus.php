<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\LivrablesAttendusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LivrablesAttendusRepository::class)
 */
class LivrablesAttendus
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
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity=Brief::class, inversedBy="livrablesAttenduses", cascade={"persist"})
     */
    private $briefs;

    /**
     * @ORM\OneToMany(targetEntity=Livrables::class, mappedBy="livrablesAttendus")
     * @Groups({"brief:read"})
     */
    private $livrables;

    public function __construct()
    {
        $this->briefs = new ArrayCollection();
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
     * @return Collection|Brief[]
     */
    public function getBriefs(): Collection
    {
        return $this->briefs;
    }

    public function addBrief(Brief $brief): self
    {
        if (!$this->briefs->contains($brief)) {
            $this->briefs[] = $brief;
        }

        return $this;
    }

    public function removeBrief(Brief $brief): self
    {
        if ($this->briefs->contains($brief)) {
            $this->briefs->removeElement($brief);
        }

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
            $livrable->setLivrablesAttendus($this);
        }

        return $this;
    }
    
    public function clearLivrables()
    {
        return $this->livrables = new ArrayCollection();
    }

    public function removeLivrable(Livrables $livrable): self
    {
        if ($this->livrables->contains($livrable)) {
            $this->livrables->removeElement($livrable);
            // set the owning side to null (unless already changed)
            if ($livrable->getLivrablesAttendus() === $this) {
                $livrable->setLivrablesAttendus(null);
            }
        }

        return $this;
    }
}
