<?php

namespace App\Factory;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Group|Proxy findOrCreate(array $attributes)
 * @method static Group|Proxy random()
 * @method static Group[]|Proxy[] randomSet(int $number)
 * @method static Group[]|Proxy[] randomRange(int $min, int $max)
 * @method static GroupRepository|RepositoryProxy repository()
 * @method Group|Proxy create($attributes = [])
 * @method Group[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class GroupFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    public function admin(): self
    {
        return $this->addState(['roles'=>['ROLE_USER', 'ROLE_ADMIN']]);
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->safeColorName,
            'roles' => ['ROLE_USER']
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Group $group) {})
        ;
    }

    protected static function getClass(): string
    {
        return Group::class;
    }
}
