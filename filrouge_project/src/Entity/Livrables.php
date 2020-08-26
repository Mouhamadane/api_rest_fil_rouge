<?php

namespace App\Entity;

use App\Repository\LivrablesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LivrablesRepository::class)
 */
class Livrables
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
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Apprenant::class, inversedBy="livrables")
     */
    private $apprenant;

    /**
     * @ORM\ManyToOne(targetEntity=BriefLA::class, inversedBy="livrables")
     */
    private $briefLA;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function getBriefLA(): ?BriefLA
    {
        return $this->briefLA;
    }

    public function setBriefLA(?BriefLA $briefLA): self
    {
        $this->briefLA = $briefLA;

        return $this;
    }
}
