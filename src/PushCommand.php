<?php

namespace Softius\JenkinsJobMonitor;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PushCommand
 * @package Softius\JenkinsJobMonitor
 */
class PushCommand extends Command
{
    /**
     * @var GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Configures the push command.
     */
    protected function configure()
    {
        $this
            ->setName('push')
            ->setDescription('Sets external job result')
            ->addArgument('url', InputArgument::REQUIRED, 'Jenkins home URL')
            ->addArgument('job', InputArgument::REQUIRED, 'Job name as configured in Jenkins')
            ->addOption('display', null, InputOption::VALUE_OPTIONAL, '')
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, '')
            ->addOption('duration', null, InputOption::VALUE_OPTIONAL, '')
            ->addOption('log', null, InputOption::VALUE_OPTIONAL, '');
    }

    /**
     * Executes the monitor command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $monitor = new JobMonitor(
            $input->getArgument('job'),
            $input->getOption('display'),
            $input->getOption('description')
        );
        $monitor->setLog($this->getOptionLog($input));

        try {
            $this->getClient()->send($monitor->getRequest(), ['base_uri' => $input->getArgument('url')]);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return 1;
        }

        return 0;
    }

    /**
     * Returns the value for log option.
     *
     * If the log option is not provided, STDIN is being used instead.
     *
     * @param InputInterface $input
     * @throws \RuntimeException
     * @return mixed|string
     */
    public function getOptionLog(InputInterface $input)
    {
        $log = $input->getOption('log');
        if (!$log && 0 === ftell(STDIN)) {
            $log = '';
            while (!feof(STDIN)) {
                $log .= fread(STDIN, 1024);
            }
        }

        return $log;
    }

    /**
     * @param GuzzleHttp\ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
