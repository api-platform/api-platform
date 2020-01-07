<?php


namespace App\Tests\Controller;

use App\Model\User;

class UserCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     */
    public function createUserCommandTest(){
        $admin = $this->createRandomAdmin();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));
        $firstname = sprintf('firstname_%d', rand(1000000, 10000000 - 1));
        $lastname = sprintf('lastname_%d', rand(1000000, 10000000 - 1));
        $roles = ['ROLE_USER'];

        $command = [
            'message_name' => 'createUser',
            'payload' => ['username' => $username,
                'password' => $password,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'roles' => $roles ]
        ];

        $token = $this->getToken($admin->getUsername(),$admin->getPassword());
        $response = $this->sendCommand('api/messagebox',$command,$token);
        $this->assertEquals(202, $response->getStatusCode());
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($firstname, $user->getFirstname());
        $this->assertEquals($lastname, $user->getLastname());
        $this->assertEquals($roles, $user->getRoles());
    }
}
