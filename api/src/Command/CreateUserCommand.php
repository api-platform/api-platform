<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserCommand extends Command
{

    protected static $defaultName = 'app:create-user';

    /** @var UserRepository */
    private $userRepository;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('role', InputArgument::OPTIONAL, 'Users role')
            ->setHelp('This command allows you to create a user...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role') ?? 'ROLE_USER';

        $user = $this->userRepository->findOneBy(['username' => $username]);
        if ($user instanceof User) {
            $output->writeln('Username already taken!');
            return;
        }

        try {
            /** @var User $user */
            $user = new User($username, $password, [$role]);
        } catch (\InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return;
        }

        $encryptedPassword = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encryptedPassword);

        $entityManager = $this->userRepository->getEm();
        $entityManager->persist($user);
        $entityManager->flush();

        $output->writeln('User successfully generated!');
    }
}
