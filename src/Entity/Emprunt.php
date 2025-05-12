<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use DateTime;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Livre::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livre $livre = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $dateEmprunt = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $dateRetourPrevue = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $dateRetourEffective = null;

    #[ORM\Column(type: 'boolean')]
    private bool $estEnRetard = false;

    public function __construct()
    {
        $this->dateEmprunt = new DateTime();
        // Par défaut, la date de retour prévue est de 3 semaines après l'emprunt
        $this->dateRetourPrevue = (new DateTime())->modify('+3 weeks');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): self
    {
        $this->livre = $livre;
        return $this;
    }

    public function getDateEmprunt(): ?DateTimeInterface
    {
        return $this->dateEmprunt;
    }

    public function setDateEmprunt(DateTimeInterface $dateEmprunt): self
    {
        $this->dateEmprunt = $dateEmprunt;
        return $this;
    }

    public function getDateRetourPrevue(): ?DateTimeInterface
    {
        return $this->dateRetourPrevue;
    }

    public function setDateRetourPrevue(DateTimeInterface $dateRetourPrevue): self
    {
        $this->dateRetourPrevue = $dateRetourPrevue;
        return $this;
    }

    public function getDateRetourEffective(): ?DateTimeInterface
    {
        return $this->dateRetourEffective;
    }

    public function setDateRetourEffective(?DateTimeInterface $dateRetourEffective): self
    {
        $this->dateRetourEffective = $dateRetourEffective;
        return $this;
    }

    public function isEstEnRetard(): bool
    {
        return $this->estEnRetard;
    }

    public function setEstEnRetard(bool $estEnRetard): self
    {
        $this->estEnRetard = $estEnRetard;
        return $this;
    }
    
    // Méthode pour vérifier si l'emprunt est actif (pas encore retourné)
    public function isActif(): bool
    {
        return $this->dateRetourEffective === null;
    }
    
    // Méthode pour calculer si l'emprunt est en retard
    public function calculerRetard(): bool
    {
        // Si déjà retourné, vérifier si c'était en retard lors du retour
        if ($this->dateRetourEffective !== null) {
            return $this->estEnRetard;
        }
        
        // Sinon comparer la date prévue avec la date actuelle
        return $this->dateRetourPrevue < new DateTime();
    }
    
    // Méthode pour effectuer le retour d'un livre
    public function retourner(): self
    {
        $this->dateRetourEffective = new DateTime();
        $this->estEnRetard = $this->calculerRetard();
        return $this;
    }
} 