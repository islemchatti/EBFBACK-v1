<?php

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture

{
    private $passEncoder;

    public function __construct(UserPasswordEncoderInterface $passEncoder)
    {
        $this->passEncoder = $passEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername("userA");
        $user->setMatricule("11111111");
        $user->setPassword($this->passEncoder->encodePassword($user,"admin"));
        $user->setNom("chatti");
        $user->setPrenom("islem");
        $user->setEmail("islemzommit@gmail.com");
        $user->setCin("11111111");
        $user->setTel("58528522");
        $user->setAdresse("bardo52");
        $user->setCnss("11111111");
        $user->setRoles(["ROLE_ADMIN"]);

        $manager->persist($user);

        $user2 = new User();


        $user2->setMatricule("22222222");

        $user2->setNom("nebli");
        $user2->setPrenom("sarra");
        $user2->setEmail("sarranebli@gmail.com");
        $user2->setCin("22222222");
        $user2->setTel("58528522");
        $user2->setAdresse("bardo52");
        $user2->setCnss("22222222");

        $manager->persist($user2);

        $manager->flush();
    }
}
