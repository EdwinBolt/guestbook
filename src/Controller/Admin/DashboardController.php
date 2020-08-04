<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Conference;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
//        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
//        return $this->redirect($routeBuilder->setController(ConferenceCrudController::class)->generateUrl());




        return parent::index();
//        return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Guestbook');
    }

    public function configureMenuItems(): iterable
    {

//        return [
//            MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),
//            MenuItem::linkToCrud('Conferences', 'fa fa-tags', Conference::class),
//            MenuItem::linkToCrud('Comments', 'fa fa-tags', Comment::class),
//
//        ];

        yield MenuItem::linkToUrl('Home', 'fa fa-home', '/');
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'icon class', EntityClass::class);
        // links to the 'index' action of the Comments CRUD controller
        yield MenuItem::linkToCrud('Comments', 'fa fa-comments', Comment::class);
        yield MenuItem::linkToCrud('Conferences', 'fa fa-users', Conference::class);


//        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
//
//        yield MenuItem::section('Conferences');
//        yield MenuItem::linkToCrud('Conferences', 'fa fa-tags', Conference::class);
//        yield MenuItem::section('Comments');
//        yield MenuItem::linkToCrud('Comments', 'fa fa-tags', Comment::class);
    }
}
