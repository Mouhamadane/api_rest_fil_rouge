<?php

namespace App\Entity;

use App\Repository\PromoBriefApprenantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PromoBriefApprenantRepository::class)
 */
class PromoBriefApprenant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups ({"brief:App:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Apprenant::class, inversedBy="promoBriefApprenants")
     * @Groups ({"brief:App:read"})
     */
    private $apprenant;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"brief:App:read"})
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=PromoBrief::class, inversedBy="promoBriefApprenants")
     */
    private $promoBrief;
  
    public function getId(): ?int
    {
        return $this->id;
    }
      
    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

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


    public function getApprenant(): ?Apprenant
    {
        return $this->apprenant;
    }

    public function setApprenant(?Apprenant $apprenant): self
    {
        $this->apprenant = $apprenant;

        return $this;
    }
}
