<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class LivreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Livre::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des livres')
            ->setPageTitle('new', 'Ajouter un livre')
            ->setPageTitle('edit', 'Modifier un livre')
            ->setPageTitle('detail', 'Détails du livre')
            ->setEntityLabelInSingular('Livre')
            ->setEntityLabelInPlural('Livres')
            ->setFormOptions([
                'attr' => [
                    'enctype' => 'multipart/form-data'
                ]
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titre');
        yield TextEditorField::new('resume')->hideOnIndex();
        
        // Configuration simplifiée de l'upload d'image
        yield ImageField::new('couverture')
            ->setBasePath('uploads/couvertures')
            ->setUploadDir('public/uploads/couvertures')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);
        
        yield AssociationField::new('auteur');
    }
}
