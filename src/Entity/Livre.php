<?php // Début du fichier PHP

namespace App\Entity; // Espace de noms pour les entités

use App\Repository\LivreRepository; // Importe le repository associé
use Doctrine\ORM\Mapping as ORM; // Importe les annotations Doctrine pour le mapping objet-relationnel
use App\Entity\Auteur;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: LivreRepository::class)] // Définit la classe comme une entité gérée par le repository LivreRepository
class Livre // Définition de la classe Livre
{
    #[ORM\Id] // Marque ce champ comme identifiant
    #[ORM\GeneratedValue] // La valeur sera générée automatiquement (auto-increment)
    #[ORM\Column] // Définit que c'est une colonne en base de données
    private ?int $id = null; // Propriété id initialisée à null, peut être null (?)

    #[ORM\Column(length: 255)] // Colonne avec longueur maximale de 255 caractères
    private ?string $titre = null; // Propriété titre, peut être null

    #[ORM\Column(length: 255)] // Colonne avec longueur maximale de 255 caractères
    private ?string $resume = null; // Propriété resume, peut être null

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couverture = null; // Propriété couverture, peut être null

    #[ORM\ManyToOne(targetEntity: Auteur::class, inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Auteur $auteur = null;

    // Variable temporaire pour le téléchargement des fichiers
    private $couvertureFile;

    public function getId(): ?int // Getter pour l'id, retourne un int ou null
    {
        return $this->id; // Retourne la valeur de la propriété id
    }

    public function getTitre(): ?string // Getter pour le titre, retourne une string ou null
    {
        return $this->titre; // Retourne la valeur de la propriété titre
    }

    public function setTitre(string $titre): static // Setter pour le titre, prend une string en paramètre
    {
        $this->titre = $titre; // Assigne la valeur du paramètre à la propriété

        return $this; // Retourne l'instance courante pour permettre le chaînage des méthodes
    }

    public function getResume(): ?string // Getter pour le résumé, retourne une string ou null
    {
        return $this->resume; // Retourne la valeur de la propriété resume
    }

    public function setResume(string $resume): static // Setter pour le résumé, prend une string en paramètre
    {
        $this->resume = $resume; // Assigne la valeur du paramètre à la propriété

        return $this; // Retourne l'instance courante pour permettre le chaînage des méthodes
    }

    public function getCouverture(): ?string // Getter pour la couverture, retourne une string ou null
    {
        return $this->couverture; // Retourne la valeur de la propriété couverture
    }

    public function setCouverture(?string $couverture): static // Setter pour la couverture, prend une string en paramètre
    {
        $this->couverture = $couverture; // Assigne la valeur du paramètre à la propriété

        return $this; // Retourne l'instance courante pour permettre le chaînage des méthodes
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): self
    {
        $this->auteur = $auteur;
        return $this;
    }

    // Getter et setter pour la variable temporaire
    public function getCouvertureFile()
    {
        return $this->couvertureFile;
    }

    public function setCouvertureFile($couvertureFile): self
    {
        $this->couvertureFile = $couvertureFile;
        return $this;
    }
}
