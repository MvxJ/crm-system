<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Settings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roleClient = new Role();
        $roleClient->setRole('ROLE_CUSTOMER');
        $roleClient->setName('Klient');

        $roleAdmin = new Role();
        $roleAdmin->setRole('ROLE_ADMIN');
        $roleAdmin->setName('Administrator');

        $roleMarketing = new Role();
        $roleMarketing->setRole('ROLE_MARKETING');
        $roleMarketing->setName('Marketing');

        $roleService = new Role();
        $roleService->setRole('ROLE_SERVICE');
        $roleService->setName('Serwis');

        $roleAccountant = new Role();
        $roleAccountant->setRole('ROLE_ACCOUNTMENT');
        $roleAccountant->setName('Księgowość');

        $roleAccessAdminPanel = new Role();
        $roleAccessAdminPanel->setRole('ROLE_ACCESS_ADMIN_PANEL');
        $roleAccessAdminPanel->setName('Dostęp do panelu administracyjnego');

        $companySettings = new Settings();

        $manager->persist($roleAdmin);
        $manager->persist($roleClient);
        $manager->persist($roleMarketing);
        $manager->persist($roleService);
        $manager->persist($roleAccountant);
        $manager->persist($roleAccessAdminPanel);
        $manager->persist($companySettings);

        $manager->flush();
    }
}
