<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('conference'),
            TextField::new('author'),
            TextEditorField::new('text'),
            EmailField::new('email'),
            DateTimeField::new('createdAt'),
//            ImageField::new('photoFile')->onlyOnForms(),//->setFormType(VichImageType::class)->setTextAlign('left'),
//            ImageField::new('photo')->onlyOnIndex()->setBasePath('/uploads/photos/'),
//            IntegerField::new('imageSize')
            TextField::new('state'),
        ];
    }

//    public function configureFields(string $pageName): iterable
//    {
//        return [
//            ChoiceField::class,[
//                'choices' => ConferenceRepository->findAll()[
//
//                ]
//            ]
////            TextField::new('title'),
////            TextEditorField::new('description'),
//        ];
//    }

}
