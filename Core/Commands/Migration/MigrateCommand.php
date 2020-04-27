<?php
namespace Core\Commands\Migration;

use Core\Database\Migration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command{
    protected $commandName = 'app:migrate';
    protected $commandDescription = "Migrate all migration database";
    private $migration;
    protected function configure()
    {
        $this
            ->setName($this->commandName)
            
            ->setDescription($this->commandDescription)
            
        ;
    }

    protected function initialize(InputInterface $input){

        $this->migration = new Migration();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migration->migrateAll();
    }
}