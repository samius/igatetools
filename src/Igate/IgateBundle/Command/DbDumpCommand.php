<?php
namespace Igate\IgateBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author milan
 * @package Igate
 */
class DbDumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('igate:db:dump')
             ->setDescription('Dumps whole database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conf = $this->getContainer();
        $dbName = $conf->getParameter('database_name');

        $fileDir = $conf->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . 'db';
        if (!is_dir($fileDir)) {
            mkdir($fileDir);
        }
        $filename = $fileDir . DIRECTORY_SEPARATOR . "$dbName.sql";

        echo("Using file $filename\n");
        echo "Dumping database $dbName ...\n";
        $defaultFlags = "-u {$conf->getParameter('database_user')} -p{$conf->getParameter('database_password')} --host {$conf->getParameter('database_host')} --skip-dump-date --skip-comments";
        $cmd = "mysqldump $defaultFlags --quote-names --routines --triggers {$dbName}";
        $sql = shell_exec($cmd);

        file_put_contents($filename, $sql);
        echo "OK\n";

    }
}