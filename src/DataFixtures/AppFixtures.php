<?php

namespace App\DataFixtures;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
  
 
    public function load(ObjectManager $manager)
    {
        $user = new Utilisateur();
        $user->setEmail('admin@admin.com');
        $role = ['ROLE_ADMIN'];
        $user->setRoles($role); 
        $password = $this->encoder->encodePassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);
        $manager->flush();
    }
}
