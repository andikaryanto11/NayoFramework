<?php
namespace Core\Commands\Migration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command{
    protected $commandName = 'create:migration';
    protected $commandDescription = "Create a Migration File";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->setHelp('This command allows you to create a migration file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $filename = date('YmdHis');
        $path = APP_PATH . "Database/Migrations/migration_$filename.php";

        $my_migration = fopen($path, "w") or die("Unable to create migration file!");

        $migration_template = "<?php
namespace App\Database\Migrations;
use Core\Database\Table;

class migration_$filename {

    public function up(){

    }
}";

        fwrite($my_migration, $migration_template);

        fclose($my_migration);

        $output->writeln("$path migration has successfully been created." . PHP_EOL);
    }
}