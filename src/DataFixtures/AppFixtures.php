<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface $passwordHasher
     */
    protected UserPasswordHasherInterface $passwordHasher;

    /**
     * AppFixtures constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher   = $passwordHasher;
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->loadAdmin($manager);
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    private function loadAdmin(ObjectManager $manager): void
    {
        foreach ($this->getAdminData() as [$email, $password, $status, $roles, $createAt]) {
            $admin = new Admin();
            $admin->setEmail($email);
            $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
            $admin->setStatus($status);
            $admin->setRoles($roles);
            $admin->setCreateAt($createAt);

            $manager->persist($admin);
        }
        $manager->flush();
    }

    /**
     * @return array
     */
    private function getAdminData(): array
    {
        return [
            [
                'admin@ortmontreuil.fr',
                'admin',
                true,
                ['ROLE_ADMIN'],
                new \DateTime()
            ],
            [
                'daniel.soudry@ortmontreuil.fr',
                'danis',
                true,
                ['ROLE_ADMIN'],
                new \DateTime()
            ]
        ];
    }
}
