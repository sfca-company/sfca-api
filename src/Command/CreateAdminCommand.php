<?php
namespace App\Command;

use App\Entity\User;
use App\Repository\Company\CompanyRepository;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-admin';

    private $passwordHasher;
    private $companyRepo;
    private $userRepo;
    public $companySfca;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepo,
        CompanyRepository $companyRepo

    ) {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->userRepo = $userRepo;
        $this->companyRepo = $companyRepo;
        $this->companySfca = 1;
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if(!empty($this->userRepo->findOneBy(["email"=>"contact@sfca.tech"]))){
            $output->write("utilisateur déjà enregistré contact@sfca.tech");
            return Command::SUCCESS;
            exit;
        }
        $sfca = $this->companyRepo->findOneBy(["id"=>$this->companySfca]);
        if(empty($sfca)){
            $output->write("company 1 sfca non trouvé");
            return Command::SUCCESS;
            exit;
        }
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            "okokyt123"
        );
        $user->setPassword($hashedPassword);
        $user->setEmail("contact@sfca.tech");
        $user->setRoles([UserService::ROLE_ADMIN]);
        $user->addCompany($sfca);
        $this->em->persist($user);
        $this->em->flush();

        $output->write("utilisateur crée");
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}