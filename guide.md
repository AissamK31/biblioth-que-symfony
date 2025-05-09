# Guide d'initiation à Symfony : Projet Bibliothèque

Ce guide vous permettra de créer une application simple de gestion de bibliothèque avec Symfony. Vous apprendrez les concepts de base de ce framework PHP et réaliserez une application fonctionnelle étape par étape.

## Sommaire

1. [Présentation de Symfony](#présentation-de-symfony)
2. [Installation et configuration](#installation-et-configuration)
3. [Structure du projet](#structure-du-projet)
4. [Création d'une entité](#création-dune-entité)
5. [Génération du repository](#génération-du-repository)
6. [Création d'un contrôleur](#création-dun-contrôleur)
7. [Création des templates](#création-des-templates)
8. [Ajout de données avec les fixtures](#ajout-de-données-avec-les-fixtures)
9. [Création de l'interface d'administration](#création-de-linterface-dadministration)
10. [Test de l'application](#test-de-lapplication)
11. [Prochaines étapes](#prochaines-étapes)

## Présentation de Symfony

Symfony est un framework PHP professionnel utilisé pour créer des applications web. Il offre :

- Une architecture MVC (Modèle-Vue-Contrôleur)
- Des composants réutilisables
- Des outils de développement efficaces
- Une grande communauté active

## Installation et configuration

### Prérequis

- PHP 8.1 ou supérieur
- Composer (gestionnaire de dépendances PHP)
- Symfony CLI (optionnel mais recommandé)

### Installation de Symfony

```bash
# Installation de Symfony CLI
curl -sS https://get.symfony.com/cli/installer | bash

# Création d'un nouveau projet Symfony
symfony new bibliotheque --webapp
cd bibliotheque
```

### Configuration de la base de données

Modifiez le fichier `.env` à la racine du projet pour configurer votre base de données :

```
# .env
DATABASE_URL="mysql://utilisateur:mot_de_passe@127.0.0.1:3306/bibliotheque?serverVersion=8.0"
```

## Structure du projet

Symfony organise votre code selon une structure claire :

- `config/` : fichiers de configuration
- `public/` : point d'entrée de l'application (index.php)
- `src/` : votre code source PHP
  - `Controller/` : contrôleurs qui gèrent les requêtes
  - `Entity/` : modèles de données
  - `Repository/` : classes pour récupérer les données
- `templates/` : fichiers de template Twig
- `migrations/` : fichiers de migration de base de données
- `var/` : fichiers temporaires (cache, logs)
- `vendor/` : librairies externes

## Création d'une entité

Les entités représentent vos modèles de données. Créons une entité `Livre` :

```bash
symfony console make:entity Livre
```

Répondez aux questions pour créer les propriétés suivantes :

- `titre` (string, 255)
- `resume` (string, 255)
- `couverture` (string, 255)

Voici à quoi ressemble l'entité générée :

```php
<?php // Début du fichier PHP

namespace App\Entity; // Espace de noms pour les entités

use App\Repository\LivreRepository; // Importe le repository associé
use Doctrine\ORM\Mapping as ORM; // Importe les annotations Doctrine pour le mapping objet-relationnel

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

    #[ORM\Column(length: 255)] // Colonne avec longueur maximale de 255 caractères
    private ?string $couverture = null; // Propriété couverture, peut être null

    // Getters et setters générés automatiquement
    // ...
}
```

### Création de la base de données et des tables

```bash
# Création de la base de données
symfony console doctrine:database:create

# Création des migrations
symfony console make:migration

# Exécution des migrations
symfony console doctrine:migrations:migrate
```

## Création d'un contrôleur

Le contrôleur gère les requêtes HTTP et retourne des réponses :

```bash
symfony console make:controller LivreController
```

Modifiez le contrôleur pour ajouter les méthodes permettant d'afficher la liste des livres et le détail d'un livre :

```php
<?php // Début du fichier PHP, obligatoire

namespace App\Controller; // Définit l'espace de noms du contrôleur

// Importation des classes nécessaires pour le contrôleur
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base pour tous les contrôleurs Symfony
use Symfony\Component\HttpFoundation\Response; // Classe pour générer des réponses HTTP
use Symfony\Component\Routing\Attribute\Route; // Attribut pour définir les routes
use App\Repository\LivreRepository; // Repository pour accéder aux données des livres
use App\Entity\Livre; // Entité Livre

class LivreController extends AbstractController // Définition de la classe contrôleur qui hérite d'AbstractController
{
    #[Route('/livres', name: 'app_livres')] // Définit la route URL et son nom
    public function index(LivreRepository $livreRepository): Response // Méthode pour afficher tous les livres, avec injection du repository
    {
        $livres = $livreRepository->findAll(); // Récupère tous les livres depuis la base de données
        return $this->render('livre/index.html.twig', [ // Rend le template avec les données
            'livres' => $livres, // Passe la variable $livres au template
        ]);
    }

    #[Route('/livre/{id}', name: 'app_livre_show')] // Route avec paramètre id pour afficher un livre spécifique
    public function show(Livre $livre): Response // Méthode avec ParamConverter qui injecte automatiquement l'objet Livre
    {
        return $this->render('livre/show.html.twig', [ // Rend le template de détail
            'livre' => $livre, // Passe la variable $livre au template
        ]);
    }
}
```

## Création des templates

Les templates utilisent Twig, un moteur de template puissant et flexible.

### Template de base (base.html.twig)

```twig
<!DOCTYPE html> {# Déclaration du type de document HTML #}
<html> {# Balise racine HTML #}
    <head> {# En-tête du document #}
        <meta charset="UTF-8"> {# Définit l'encodage des caractères #}
        <title>{% block title %}Welcome!{% endblock %}</title> {# Titre de la page, peut être remplacé dans les templates enfants #}
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text><text y=%221.3em%22 x=%220.2em%22 font-size=%2276%22 fill=%22%23fff%22>sf</text></svg>"> {# Favicon Symfony par défaut #}
        {% block stylesheets %} {# Bloc pour les feuilles de style, peut être étendu dans les templates enfants #}
        {% endblock %}

        {% block javascripts %} {# Bloc pour les scripts JavaScript #}
            {% block importmap %}{{ importmap('app') }}{% endblock %} {# Utilise importmap pour gérer les dépendances JS #}
        {% endblock %}
    </head>
    <body> {# Corps du document #}
        {% block body %}{% endblock %} {# Bloc principal qui sera remplacé dans les templates enfants #}
    </body>
</html>
```

### Template d'index (livre/index.html.twig)

Créez le dossier `templates/livre` s'il n'existe pas, puis créez le fichier :

```twig
{% extends 'base.html.twig' %} {# Étend le template de base #}

{% block title %}Liste des livres{% endblock %} {# Définit le titre de la page #}

{% block body %} {# Début du bloc body qui sera inséré dans le template de base #}
<h1>Liste des livres</h1> {# Titre principal de la page #}
<ul> {# Début d'une liste non ordonnée #}
    {% for livre in livres %} {# Boucle sur tous les livres passés au template #}
        <li> {# Élément de liste #}
            <a href="{{ path('app_livre_show', {'id': livre.id}) }}"> {# Lien vers la page de détail du livre avec son id #}
                {{ livre.titre }} {# Affiche le titre du livre #}
            </a>
        </li>
    {% else %} {# Si aucun livre n'est trouvé #}
        <li>Aucun livre trouvé.</li> {# Message affiché si la liste est vide #}
    {% endfor %} {# Fin de la boucle #}
</ul> {# Fin de la liste #}
{% endblock %} {# Fin du bloc body #}
```

### Template de détail (livre/show.html.twig)

```twig
{% extends 'base.html.twig' %} {# Étend le template de base #}

{% block title %}Livre{% endblock %} {# Définit le titre de la page #}

{% block body %} {# Début du bloc body qui sera inséré dans le template de base #}

<h1>{{ livre.titre }}</h1> {# Affiche le titre du livre comme titre principal #}
<p>{{ livre.resume }}</p> {# Affiche le résumé du livre dans un paragraphe #}
<img src="{{ livre.couverture }}" alt="{{ livre.titre }}"> {# Affiche l'image de couverture avec le titre comme texte alternatif #}

<a href="{{ path('app_livres') }}">Retour à la liste des livres</a> {# Lien pour revenir à la liste des livres #}
<a href="#">Modifier</a> {# Lien non implémenté pour modifier le livre #}
<a href="#">Supprimer</a> {# Lien non implémenté pour supprimer le livre #}
{% endblock %} {# Fin du bloc body #}
```

## Ajout de données avec les fixtures

Les fixtures permettent d'ajouter des données de test à votre application.

### Installation du bundle DoctrineFixtures

```bash
composer require --dev doctrine/doctrine-fixtures-bundle
```

### Création des fixtures

```bash
symfony console make:fixtures AppFixtures
```

Modifiez le fichier src/DataFixtures/AppFixtures.php :

```php
<?php // Début du fichier PHP

namespace App\DataFixtures; // Espace de noms pour les fixtures

use App\Entity\Livre; // Import de l'entité Livre
use Doctrine\Bundle\FixturesBundle\Fixture; // Import de la classe Fixture de base
use Doctrine\Persistence\ObjectManager; // Import du gestionnaire d'objets Doctrine

class AppFixtures extends Fixture // Définition de la classe qui étend Fixture
{
    public function load(ObjectManager $manager): void // Méthode exécutée lors du chargement des fixtures
    {
        for ($i = 0; $i < 10; $i++) { // Boucle pour créer 10 livres
            $livre = new Livre(); // Création d'une nouvelle instance de Livre
            $livre->setTitre("livre " . $i); // Définit le titre avec une valeur incluant l'index
            $livre->setResume("Le livre de la vie est un livre qui parle de la vie " . $i); // Définit le résumé
            $livre->setCouverture("https://via.placeholder.com/150 " . $i); // Définit l'URL de la couverture (placeholder)
            $manager->persist($livre); // Indique à Doctrine de persister cet objet
        }

        $manager->flush(); // Exécute les requêtes SQL pour insérer tous les objets persistés
    }
}
```

### Chargement des fixtures

```bash
symfony console doctrine:fixtures:load
```

## Création de l'interface d'administration

Pour faciliter la gestion de notre bibliothèque, nous allons créer une interface d'administration en utilisant EasyAdmin Bundle, un puissant outil qui génère automatiquement un tableau de bord d'administration.

### Installation d'EasyAdmin

```bash
composer require easycorp/easyadmin-bundle
```

### Configuration de l'interface d'administration

Créons un tableau de bord d'administration :

```bash
symfony console make:admin:dashboard
```

Cela va créer une classe `DashboardController` dans le dossier `src/Controller/Admin/`. Modifiez-la comme suit :

```php
<?php // Début du fichier PHP

namespace App\Controller\Admin; // Espace de noms pour les contrôleurs d'administration

use App\Entity\Livre; // Import de l'entité Livre
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard; // Import de la configuration du tableau de bord
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem; // Import des éléments de menu
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController; // Contrôleur de base pour le tableau de bord
use Symfony\Component\HttpFoundation\Response; // Pour les réponses HTTP
use Symfony\Component\Routing\Attribute\Route; // Pour les routes

class DashboardController extends AbstractDashboardController // Classe de tableau de bord héritant du contrôleur abstrait
{
    #[Route('/admin', name: 'admin')] // Route pour accéder au tableau de bord
    public function index(): Response // Méthode pour afficher la page d'accueil du tableau de bord
    {
        // Option 1. Vous pouvez créer votre propre page d'accueil personnalisée
        // return $this->render('admin/dashboard.html.twig');

        // Option 2. Vous pouvez utiliser une des pages génériques prédéfinies
        return $this->render('admin/dashboard.html.twig', [
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName(),
        ]);
    }

    public function configureDashboard(): Dashboard // Configuration du tableau de bord
    {
        return Dashboard::new()
            ->setTitle('Bibliothèque Admin'); // Titre du tableau de bord
    }

    public function configureMenuItems(): iterable // Configuration des éléments du menu
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'); // Lien vers le tableau de bord avec icône
        yield MenuItem::linkToCrud('Livres', 'fas fa-book', Livre::class); // Lien vers la gestion des livres avec icône
    }
}
```

### Création du contrôleur CRUD pour l'entité Livre

Maintenant, créons un contrôleur CRUD pour gérer les livres :

```bash
symfony console make:admin:crud
```

Sélectionnez l'entité `Livre` et suivez les instructions. Cela va créer une classe `LivreCrudController` dans le dossier `src/Controller/Admin/`. Modifiez-la comme suit :

```php
<?php // Début du fichier PHP

namespace App\Controller\Admin; // Espace de noms pour les contrôleurs d'administration

use App\Entity\Livre; // Import de l'entité Livre
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Contrôleur de base pour le CRUD
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Champ pour l'ID
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField; // Champ pour le texte
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField; // Champ pour l'image

class LivreCrudController extends AbstractCrudController // Classe CRUD héritant du contrôleur abstrait
{
    public static function getEntityFqcn(): string // Retourne le nom complet de la classe de l'entité
    {
        return Livre::class;
    }

    public function configureFields(string $pageName): iterable // Configuration des champs du formulaire
    {
        return [
            IdField::new('id')->hideOnForm(), // Champ ID, caché dans le formulaire
            TextField::new('titre'), // Champ pour le titre
            TextField::new('resume')->hideOnIndex(), // Champ pour le résumé, caché dans la liste
            TextField::new('couverture'), // Champ pour l'URL de la couverture
        ];
    }
}
```

### Création du template pour le tableau de bord (optionnel)

Créez un fichier `templates/admin/dashboard.html.twig` pour personnaliser l'apparence du tableau de bord :

```twig
{% extends '@EasyAdmin/page/content.html.twig' %} {# Étend le template de base d'EasyAdmin #}

{% block content %} {# Bloc pour le contenu principal #}
    <div class="jumbotron"> {# Conteneur principal style "jumbotron" #}
        <h1>Bienvenue dans l'administration de la Bibliothèque</h1> {# Titre principal #}
        <p>Utilisez le menu à gauche pour gérer vos livres.</p> {# Description #}
    </div>
{% endblock %}
```

## Test de l'application

Lancez le serveur de développement :

```bash
symfony server:start
```

Accédez à votre application dans votre navigateur :

- Liste des livres : http://localhost:8000/livres
- Détail d'un livre : http://localhost:8000/livre/1
- Interface d'administration : http://localhost:8000/admin

L'interface d'administration vous permet désormais de :

- Voir la liste de tous les livres
- Ajouter de nouveaux livres
- Modifier les livres existants
- Supprimer des livres

## Concepts clés à comprendre

### Le modèle MVC (Modèle-Vue-Contrôleur)

- **Modèle** (Entity) : Représente les données et la logique métier
- **Vue** (Templates) : Affiche les données à l'utilisateur
- **Contrôleur** : Gère les requêtes, récupère les données et les passe aux vues

### Routes

Les routes définissent comment les URL sont associées aux actions du contrôleur.

### Doctrine ORM

Doctrine est un ORM (Object-Relational Mapping) qui permet de manipuler la base de données en utilisant des objets PHP.

### Twig

Twig est un moteur de templates qui permet de séparer la logique de présentation du code PHP.

### EasyAdmin

EasyAdmin est un bundle qui permet de générer rapidement une interface d'administration complète pour votre application Symfony.

## Prochaines étapes

Pour améliorer cette application, vous pourriez :

1. Améliorer l'interface d'administration avec des champs personnalisés
2. Ajouter une gestion des utilisateurs avec sécurité
3. Améliorer le design avec Bootstrap ou un autre framework CSS
4. Ajouter une validation des données
5. Implémenter des fonctionnalités de recherche
6. Ajouter des catégories ou des auteurs pour les livres

### Ressources utiles

- [Documentation officielle de Symfony](https://symfony.com/doc/current/index.html)
- [Documentation de Twig](https://twig.symfony.com/doc/3.x/)
- [Documentation de Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.10/index.html)
- [Documentation d'EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html)

---

N'hésitez pas à explorer la documentation officielle pour approfondir vos connaissances sur Symfony !
