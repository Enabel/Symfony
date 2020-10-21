<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LanguageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.label.user')
            ->setEntityLabelInPlural('admin.label.users')
            ->setSearchFields(['id', 'email', 'displayName', 'countryWorkplace'])
            ->setEntityPermission('ROLE_MANAGE_USER')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id', 'admin.user.label.id');
        $email = EmailField::new('email', 'admin.user.label.email');
        $groups = AssociationField::new('groups', 'admin.user.label.groups');
        $groups->setCrudController(GroupCrudController::class);
        $groups->autocomplete();
        $azureId =  TextField::new('azureId', 'admin.user.label.azureId');
        $displayName = TextField::new('displayName', 'admin.user.label.displayName');
        $language =  LanguageField::new('language', 'admin.user.label.language');
        $jobTitle =  TextField::new('jobTitle', 'admin.user.label.jobTitle');
        $countryWorkplace =  CountryField::new('countryWorkplace', 'admin.user.label.countryWorkplace');
        $lastLoginAt = DateTimeField::new('lastLoginAt', 'admin.user.label.lastLoginAt');
        $isActive = BooleanField::new('isActive', 'admin.user.label.isActive');
        $createdAt = DateTimeField::new('createdAt', 'admin.user.label.createdAt');
        $updatedAt = DateTimeField::new('updatedAt', 'admin.user.label.updatedAt');

        $panelPersonal = FormField::addPanel('admin.user.panel.personal', 'fa fa-id-card');
        $panelAccount = FormField::addPanel('admin.user.panel.account', 'fa fa-key');
        $panelAzure = FormField::addPanel('admin.user.panel.azure', 'fa fa-cloud');
        $panelWork = FormField::addPanel('admin.user.panel.work', 'fa fa-building');
        $panelLog = FormField::addPanel('admin.user.panel.log', 'fa fa-clock');

        $list = [ $id, $displayName, $countryWorkplace, $isActive, $groups, $lastLoginAt ];
        $form = [
            $panelPersonal, $displayName, $email, $language,
            $panelWork, $countryWorkplace, $jobTitle,
            $panelAccount, $id->onlyOnDetail(), $groups, $isActive,
            $panelAzure->onlyOnDetail(), $azureId->onlyOnDetail(),
            $panelLog->onlyOnDetail(), $lastLoginAt->onlyOnDetail(), $createdAt->onlyOnDetail(), $updatedAt->onlyOnDetail()
        ];

        if (Crud::PAGE_INDEX === $pageName) {
            return $list;
        }

        return $form;
    }
}
