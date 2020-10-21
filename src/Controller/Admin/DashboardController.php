<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DashboardController
 *
 * @author Damien Lagae <damien.lagae@enabel.be>
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends AbstractDashboardController
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $usersByCountry = $userRepository->statUserByCountry();
        $latestLogsIn = $userRepository->statLatestLogsIn();
        return $this->render(
            'admin/dashboard/backend.html.twig',
            [
                'usersByCountry' =>$usersByCountry,
                'latestLogsIn' => $latestLogsIn
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/img/enabel-icon.png"> ' . $this->translator->trans('app.name.backend'))
            ->setFaviconPath('favicon/favicon.ico')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('admin.menu.dashboard', 'fa fa-home');

        if ($this->isGranted('ROLE_MANAGE_USER')) {
            yield MenuItem::section('admin.menu.permissions', 'fa fa-key');
            yield MenuItem::linkToCrud('admin.menu.user', 'fa fa-user class', User::class);
            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                yield MenuItem::linkToCrud('admin.menu.group', 'fa fa-group class', Group::class);
            }
        }
        yield MenuItem::section();
        yield MenuItem::linkToRoute('admin.menu.homepage', 'fa fa-reply', 'homepage');
    }

    public function configureActions(): Actions
    {
        return Actions::new()
            ->add(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_DETAIL, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_NEW, Action::INDEX)
//                ->remove(Crud::PAGE_INDEX, Action::DELETE)
//                ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
        ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(30)
            ->setDateIntervalFormat('%%y Year(s) %%m Month(s) %%d Day(s)')
        ;
    }
}
