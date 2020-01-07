<?php

namespace App\Tests\Controller;

use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{

    public function provider()
    {
        return [
            [sprintf('admin_%s', rand(1000000, 10000000)), 'admin_pw', 'John', 'Doe', ['ROLE_ADMIN']],
            [sprintf('user_%s', rand(1000000, 10000000)), 'user_pw', 'Mike', 'Mall', ['ROLE_USER']],
        ];
    }

    /**
     * @dataProvider provider
     * @param $username
     * @param $password
     * @param $firstname
     * @param $lastname
     * @param $roles
     * @throws \Exception
     */
    public function testUserService($username, $password, $firstname, $lastname, $roles)
    {
        $client = static::createClient();

        $user = new User($username, $password, $roles);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        /** @var EntityManagerInterface $em */
        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
        $em->clear();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['id' => $user->getId()->toString()]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($firstname, $user->getFirstname());
        $this->assertEquals($lastname, $user->getLastname());
        $this->assertEquals($roles, $user->getRoles());
    }
}
