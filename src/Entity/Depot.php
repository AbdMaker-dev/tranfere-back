<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DepotRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Asset;

/**
 * 
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compte:write","compte:read", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Asset\NotBlank(message="Veuillez remplir ce champs date depot")
     * @Groups({"compte:write","compte:read", "user:read"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="float")
     * @Asset\NotBlank(message="Veuillez remplir ce champs montant")
     * @Groups({"compte:write","compte:read", "user:read"})
     */
    private $montantDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots")
     * @Groups({"compte:write","compte:read", "user:read"})
     */
    private $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots")
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getMontantDepot(): ?float
    {
        return $this->montantDepot;
    }

    public function setMontantDepot(float $montantDepot): self
    {
        $this->montantDepot = $montantDepot;

        return $this;
    }

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}