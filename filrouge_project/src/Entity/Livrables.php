<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LivrablesRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LivrablesRepository::class)
 * @ApiResource()
 */
class Livrables
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"brief:read","briefbrouillons:read","briefgroupe:read","promo:referentiel:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brief:read","briefgroupe:read","briefbrouillons:read"})
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Apprenant::class, inversedBy="livrables")
     * @Groups({"brief:read"})
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

    public function setId()
    {
        return $this->id = null;
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
