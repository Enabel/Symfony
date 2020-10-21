<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use App\Service\RolesHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;

class GroupCrudController extends AbstractCrudController
{
    /**
     * @var RolesHelper
     */
    private $rolesHelper;

    public function __construct(RolesHelper $rolesHelper)
    {
        $this->rolesHelper = $rolesHelper;
    }

    public static function getEntityFqcn(): string
    {
        return Group::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.label.group')
            ->setEntityLabelInPlural('admin.label.groups')
            ->setSearchFields(['id', 'name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'admin.group.label.id');
        $name = TextField::new('name', 'admin.group.label.name');
        $roles = ChoiceField::new('roles', 'admin.group.label.roles');
        $roles
            ->allowMultipleChoices(true)
            ->setChoices($this->rolesHelper->getRoles())
        ;
        $users = AssociationField::new('users', 'admin.group.label.users');
        $users->setCrudController(UserCrudController::class);
        $createdAt = DateTimeField::new('createdAt', 'admin.group.label.createdAt');
        $updatedAt = DateTimeField::new('updatedAt', 'admin.group.label.updatedAt');

        $panelGeneral = FormField::addPanel('admin.group.panel.general', 'fa fa-group');
        $panelUser = FormField::addPanel('admin.group.panel.user', 'fa fa-user');
        $panelLog = FormField::addPanel('admin.group.panel.log', 'fa fa-clock');

        $list = [ $id, $name, $users ];
        $form = [
            $panelGeneral, $name, $roles,
            $panelUser->onlyOnDetail(), $users->onlyOnDetail(),
            $panelLog->onlyOnDetail(), $createdAt->onlyOnDetail(), $updatedAt->onlyOnDetail()
        ];

        if (Crud::PAGE_INDEX === $pageName) {
            return $list;
        }

        return $form;
    }
}
