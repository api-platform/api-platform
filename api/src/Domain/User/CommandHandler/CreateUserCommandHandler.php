<?php


namespace App\Domain\User\CommandHandler;


use App\Domain\User\Command\CreateUserCommand;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param CreateUserCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateUserCommand $command)
    {
        # only admins can do that
        if (!$command->metadata()['is_admin']) {
            throw new \Exception('Only admins can do that');
        }
        $username = $command->username();
        $password = $command->password();
        $firstname = $command->firstname();
        $lastname = $command->lastname();
        $roles = $command->roles();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($user instanceof User) {
            throw new \Exception(sprintf('User with username %s already exists.', $username));
        }

        $user = new User($username, $password, $roles);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $encryptedPassword = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encryptedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
