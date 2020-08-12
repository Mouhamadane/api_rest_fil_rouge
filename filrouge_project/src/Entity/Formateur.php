<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormateurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FormateurRepository::class)
 */
class Formateur extends User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"promo:write"})
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Promos::class, mappedBy="formateur")
     */
    private $promos;

    /**
     * @ORM\ManyToMany(targetEntity=Groupes::class, mappedBy="formateur")
     */
    private $groupes;

    public function __construct()
    {
        $this->promos = new ArrayCollection();
        $this->groupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Promos[]
     */
    public function getPromos(): Collection
    {
        return $this->promos;
    }

    public function addPromo(Promos $promo): self
    {
        if (!$this->promos->contains($promo)) {
            $this->promos[] = $promo;
            $promo->addFormateur($this);
        }

        return $this;
    }

    public function removePromo(Promos $promo): self
    {
        if ($this->promos->contains($promo)) {
            $this->promos->removeElement($promo);
            $promo->removeFormateur($this);
        }

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
            $groupe->addFormateur($this);
        }

        return $this;
    }

    public function removeGroupe(Groupes $groupe): self
    {
        if ($this->groupes->contains($groupe)) {
            $this->groupes->removeElement($groupe);
            $groupe->removeFormateur($this);
        }

        return $this;
    }
}
