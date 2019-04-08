<?php
namespace Igate\IgateBundle\Command\Db;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author milan
 * @package Igate
 */
class RestoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('igate:db:restore')
            ->setDescription('Restores whole database from dump')
            ->addArgument('dbname', InputArgument::OPTIONAL, 'Dump filename without extension');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \appDevDebugProjectContainer $conf */
        $conf = $this->getContainer();
        $db = $conf->get('doctrine')->getManager()->getConnection();
        $dbName = $conf->getParameter('database_name');
        $executable = $conf->getParameter('mysql_executable') ?: 'mysql';

        $fileDir = $conf->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . 'db';
        if (!is_dir($fileDir)) {
            mkdir($fileDir);
        }
        if ($input->getArgument('dbname')) {
            $filename = $fileDir . DIRECTORY_SEPARATOR . $input->getArgument('dbname') . '.sql';
        } else {
            $filename = $fileDir . DIRECTORY_SEPARATOR . "$dbName.sql";
        }

        echo("Using file $filename\n");
        echo("Removing database $dbName ..\n");
        $db->query('DROP DATABASE IF EXISTS ' . $dbName);
        echo("Creating database $dbName ..\n");
        $db->query("CREATE DATABASE $dbName DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci");


        echo "Importing data...\n";
        $cmd = "$executable --default-character-set=utf8 --host {$conf->getParameter('database_host')} -u {$conf->getParameter('database_user')} -p{$conf->getParameter('database_password')} {$dbName} < $filename";
        shell_exec($cmd);

        echo "OK\n";
    }
}