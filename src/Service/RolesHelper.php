<?php

namespace App\Service;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Class RolesHelper
 *
 * @author Damien Lagae <damien.lagae@enabel.be>
 */
class RolesHelper
{
    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * @var array
     */
    private $roles;

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->fetchRoles();
    }

    private function fetchRoles()
    {
        $roles = array();
        $rolesArray = (array) $this->roleHierarchy;
        array_walk_recursive($rolesArray, function ($val) use (&$roles) {
            $roles[$val] = $val;
        });
        $this->roles = array_unique($roles);
    }

    public function getRoles()
    {
        if (empty($this->roles)) {
            $this->fetchRoles();
        }

        return $this->roles;
    }
}
