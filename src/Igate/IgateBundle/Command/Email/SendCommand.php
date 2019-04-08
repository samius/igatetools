<?php
namespace Igate\IgateBundle\Command\Email;

use Igate\DateTime;
use Symfony\Bundle\SwiftmailerBundle\Command\NewEmailCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Original sender doesn`t allow to set sender`s name.
 */
class SendCommand extends NewEmailCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('igate:email:send')
            ->setDescription('Sends email with sender name')
            ->addOption('fromName', null, InputOption::VALUE_OPTIONAL, 'Sender`s name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailerServiceName = sprintf('swiftmailer.mailer.%s', $input->getOption('mailer'));
        if (!$this->getContainer()->has($mailerServiceName)) {
            throw new \InvalidArgumentException(sprintf('The mailer "%s" does not exist', $input->getOption('mailer')));
        }
        switch ($input->getOption('body-source')) {
            case 'file':
                $filename = $input->getOption('body');
                $content = file_get_contents($filename);
                if ($content === false) {
                    throw new \Exception('Could not get contents from ' . $filename);
                }
                $input->setOption('body', $content);
                break;
            case 'stdin':
                break;
            default:
                throw new \InvalidArgumentException('Body-input option should be "stdin" or "file"');
        }

        $message = $this->createMessage($input);
        $mailer = $this->getContainer()->get($mailerServiceName);
        $output->writeln(sprintf('<info>Sent %s emails<info>', $mailer->send($message)));
    }

    /**
     * Creates new message from input options.
     *
     * @param InputInterface $input An InputInterface instance
     *
     * @return \Swift_Message New message
     */
    private function createMessage(InputInterface $input)
    {
        $message = new \Swift_Message(
            $input->getOption('subject'),
            $input->getOption('body'),
            $input->getOption('content-type'),
            $input->getOption('charset')
        );

        $fromName = $input->getOption('fromName');
        if ($fromName) {
            $message->setFrom($input->getOption('from'), $fromName);
        } else {
            $message->setFrom($input->getOption('from'));
        }

        $message->setTo($input->getOption('to'));

        return $message;
    }

}
