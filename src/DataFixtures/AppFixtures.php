<?php

namespace App\DataFixtures;

use App\Factory\GroupFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Create regular group [no admin]
        GroupFactory::new()
            ->many(20)
            ->create()
        ;

        // Create regular user
        UserFactory::new()
            ->many(50)
            ->create(
                [
                    'groups'=> GroupFactory::randomRange(0,3)
                ]
            )
        ;

        // Create user without group
        UserFactory::new()
            ->lambda()
            ->create()
        ;

        // Create one admin user
        UserFactory::new()
            ->create(
                [
                    'groups'=>[
                        GroupFactory::new()
                            ->admin()
                            ->create()
                    ]
                ]
            )
        ;
    }
}
